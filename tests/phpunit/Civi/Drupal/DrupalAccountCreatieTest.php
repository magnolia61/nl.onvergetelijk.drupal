<?php

namespace Civi\Drupal;

use Civi\Test\EndToEndInterface;

/**
 * Tests voor de FEITELIJKE Drupal-account-aanmaak via drupal_civicrm_configure().
 *
 * @group e2e
 *
 * ## Waarom een aparte klasse ZONDER TransactionalInterface
 * drupal_civicrm_configure() doet Drupal `user_save()` — een schrijfactie BUITEN de CiviCRM
 * DB-transactie. In combinatie met TransactionalInterface breekt dat de rollback en laten
 * navolgende tests vallen (mysqli-fout in CRM_Core_BAO_Log::register tijdens Contact.create).
 * Daarom draait deze klasse zonder auto-rollback en ruimt tearDown alles handmatig op
 * (Drupal-user + UFMatch + contact). Zie [[OZK Test Conventions]].
 *
 * ## Achtergrond (sessie 2026-06-20)
 * Dit is de creator die de bron-fix account_civicrm_post() / account_ensure_drupal_account()
 * (nl.onvergetelijk.account) aanroept zodra een nieuw Contact zonder koppeling binnenkomt.
 * Hier toetsen we dat de creator zelf correct werkt.
 *
 * ## Wat wordt getest
 *   - HAPPY : Individual + e-mail, geen koppeling → Drupal-user + UFMatch + external_identifier (GEEN mail)
 *   - GUARD : lege usermail → geen account (early return in drupal_civicrm_configure)
 *   - IDEMPOTENT: tweede aanroep maakt geen tweede Drupal-user, koppeling blijft gelijk
 */
class DrupalAccountCreatieTest extends \PHPUnit\Framework\TestCase implements EndToEndInterface {

  use \Civi\Test\Api3TestTrait;

  /** CiviCRM-contacten aangemaakt tijdens een test (handmatig opruimen: geen transactie). */
  private array $createdContactIds = [];

  // ########################################################################
  // ### setUp / tearDown
  // ########################################################################

  public function setUp(): void {
    parent::setUp();

    if (!function_exists('drupal_civicrm_configure')) {
      $this->markTestSkipped('drupal_civicrm_configure() niet beschikbaar; is nl.onvergetelijk.drupal geïnstalleerd?');
    }
    if (!function_exists('user_load') || !function_exists('user_delete')) {
      $this->markTestSkipped('Drupal user-functies (user_load/delete) niet beschikbaar.');
    }
  }

  public function tearDown(): void {
    // Geen TransactionalInterface → alles wat we aanmaakten zelf weggooien.
    foreach ($this->createdContactIds as $contactId) {
      try {
        $uid = (int) $this->externalId($contactId);

        // 1. UFMatch los (koppelt cid ↔ uid).
        foreach (civicrm_api4('UFMatch', 'get', [
          'checkPermissions' => FALSE,
          'where'            => [['contact_id', '=', $contactId]],
        ]) as $m) {
          civicrm_api4('UFMatch', 'delete', [
            'checkPermissions' => FALSE,
            'where'            => [['id', '=', $m['id']]],
          ]);
        }

        // 2. Drupal-user weg (buiten de CiviCRM-transactie). Cache vooraf legen tegen
        //    de optimistic-lock race op cache_entity_user (zie AccountScenarioTest).
        if ($uid > 0) {
          if (function_exists('cache_clear_all')) {
            cache_clear_all($uid, 'cache_entity_user');
          }
          user_delete($uid);
        }

        // 3. Contact hard verwijderen (incl. e-mails).
        $this->callAPISuccess('Contact', 'delete', ['id' => $contactId, 'skip_undelete' => TRUE]);
      }
      catch (\Throwable $e) {
        // Best-effort opruiming; een test mag niet stuklopen op cleanup.
        fwrite(STDERR, "cleanup waarschuwing voor contact $contactId: " . $e->getMessage() . "\n");
      }
    }
    $this->createdContactIds = [];
    parent::tearDown();
  }

  // ########################################################################
  // ### Helpers
  // ########################################################################

  /**
   * Maak een Individual met (optioneel) e-mail; registreer voor opruiming.
   */
  private function maakIndividual(bool $metEmail = TRUE): array {
    $suffix = substr(uniqid(), -6);
    $values = [
      'contact_type' => 'Individual',
      'first_name'   => 'Creatie',
      'last_name'    => 'Test' . $suffix,
    ];
    if ($metEmail) {
      $values['email'] = 'creatie' . $suffix . '@example.invalid';
    }
    $contactId = (int) $this->callAPISuccess('Contact', 'create', $values)['id'];
    $this->createdContactIds[] = $contactId;

    return [
      'id'          => $contactId,
      'displayname' => 'Creatie Test' . $suffix,
      'email'       => $values['email'] ?? '',
    ];
  }

  private function externalId(int $contactId): ?string {
    $result = civicrm_api4('Contact', 'get', [
      'checkPermissions' => FALSE,
      'select'           => ['external_identifier'],
      'where'            => [['id', '=', $contactId]],
    ]);
    return $result[0]['external_identifier'] ?? NULL;
  }

  private function ufMatchUid(int $contactId): ?int {
    $result = civicrm_api4('UFMatch', 'get', [
      'checkPermissions' => FALSE,
      'select'           => ['uf_id'],
      'where'            => [['contact_id', '=', $contactId]],
    ]);
    return isset($result[0]['uf_id']) ? (int) $result[0]['uf_id'] : NULL;
  }

  // ########################################################################
  // ### HAPPY PATH
  // ########################################################################

  /**
   * Nieuw Individual + e-mail + geen koppeling → Drupal-user + UFMatch + external_identifier.
   * Account-only: er wordt geen mail verstuurd (drupal_civicrm_configure genereert geen OTL).
   */
  public function testNieuwIndividualKrijgtDrupalAccountEnUfmatch(): void {
    $c = $this->maakIndividual(TRUE);

    $this->assertEmpty($this->externalId($c['id']), 'Vooraf: nog geen koppeling.');

    drupal_civicrm_configure($c['id'], $c['displayname'], $c['email']);

    // external_identifier moet nu een geldige uid bevatten.
    $uid = $this->externalId($c['id']);
    $this->assertNotEmpty($uid, 'external_identifier moet gevuld zijn na aanmaak.');
    $this->assertGreaterThan(0, (int) $uid, 'external_identifier moet een geldige Drupal-uid zijn.');

    // De Drupal-user bestaat echt.
    $user = user_load((int) $uid);
    $this->assertNotEmpty($user, 'Er moet een Drupal-user bestaan voor de nieuwe uid.');

    // UFMatch koppelt het contact aan diezelfde uid.
    $this->assertSame((int) $uid, $this->ufMatchUid($c['id']),
      'UFMatch moet het contact aan dezelfde Drupal-uid koppelen.');
  }

  // ########################################################################
  // ### GUARD: lege usermail
  // ########################################################################

  /**
   * Zonder usermail keert drupal_civicrm_configure() vroeg terug: geen account, geen koppeling.
   */
  public function testZonderUsermailGeenAccount(): void {
    $c = $this->maakIndividual(FALSE);

    drupal_civicrm_configure($c['id'], $c['displayname'], '');

    $this->assertEmpty($this->externalId($c['id']),
      'Zonder usermail mag er geen external_identifier worden gezet.');
    $this->assertNull($this->ufMatchUid($c['id']),
      'Zonder usermail mag er geen UFMatch worden aangemaakt.');
  }

  // ########################################################################
  // ### IDEMPOTENT: tweede aanroep dupliceert niet
  // ########################################################################

  /**
   * Een tweede aanroep voor hetzelfde contact maakt geen tweede Drupal-user en
   * laat de bestaande koppeling ongemoeid.
   */
  public function testTweedeAanroepDupliceertNiet(): void {
    $c = $this->maakIndividual(TRUE);

    drupal_civicrm_configure($c['id'], $c['displayname'], $c['email']);
    $uidNa1 = (int) $this->externalId($c['id']);
    $this->assertGreaterThan(0, $uidNa1, 'Eerste aanroep moet een account aanmaken.');

    drupal_civicrm_configure($c['id'], $c['displayname'], $c['email']);
    $uidNa2 = (int) $this->externalId($c['id']);

    $this->assertSame($uidNa1, $uidNa2,
      'Tweede aanroep mag de koppeling niet wijzigen (geen nieuw account).');
  }

}
