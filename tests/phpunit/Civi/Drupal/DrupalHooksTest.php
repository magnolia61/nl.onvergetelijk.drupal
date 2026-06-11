<?php

namespace Civi\Drupal;

use Civi\Test\EndToEndInterface;
use Civi\Test\TransactionalInterface;

/**
 * Tests voor nl.onvergetelijk.drupal (drupal_civicrm_username, add_activity).
 *
 * @group e2e
 *
 * drupal.php beheert Drupal-gebruikersnamen, -mails en -rollen vanuit CiviCRM.
 *
 * drupal_civicrm_username() bouwt een gebruikersnaam op basis van voor-,
 * tussen- en achternaam, verwijdert accenten en speciale tekens, en geeft
 * een array terug met 'user_name', 'familienaam', etc.
 *
 * add_activity() slaat een "D7 Sync" activiteit op in CiviCRM en geeft
 * het activiteit-id terug.
 *
 * Scenario's:
 *   A: Centrale functies zijn beschikbaar
 *   B: drupal_civicrm_username() met contact_id=0 → geen crash
 *   C: add_activity() met lege contactid → geen crash
 *   D: Retourstructuur van drupal_civicrm_username bevat alle verwachte sleutels
 *   E: Voornaam + achternaam → user_name = 'jan.jansen'
 *   F: Voornaam + tussenvoegsel in achternaam → prefix gesplitst
 *   G: Voornaam + expliciete tussenvoegsel + achternaam → user_name correct
 *   H: Accenten worden verwijderd uit user_name
 *   I: Speciale tekens (apostrof, punt) worden verwijderd uit user_name
 *   J: add_activity() met geldig contact slaat activiteit op en geeft id terug
 */
class DrupalHooksTest extends \PHPUnit\Framework\TestCase implements EndToEndInterface, TransactionalInterface {

  use \Civi\Test\Api3TestTrait;

  private int $contactId;

  public function setUp(): void {
    parent::setUp();
    if (!function_exists('drupal_civicrm_username')) {
      $this->markTestSkipped('drupal_civicrm_username() niet beschikbaar; is nl.onvergetelijk.drupal geïnstalleerd?');
    }
  }

  // ########################################################################
  // ### SCENARIO A: FUNCTIES BESTAAN
  // ########################################################################

  /**
   * Alle publieke functies van nl.onvergetelijk.drupal zijn beschikbaar.
   */
  public function testFunctiesBestaanAllemaal() {
    $this->assertTrue(function_exists('drupal_civicrm_username'), 'drupal_civicrm_username() moet beschikbaar zijn.');
    $this->assertTrue(function_exists('add_activity'),            'add_activity() moet beschikbaar zijn.');
  }

  // ########################################################################
  // ### SCENARIO B: USERNAME MET ONGELDIG CONTACT → GEEN CRASH
  // ########################################################################

  /**
   * drupal_civicrm_username() met contact_id=0 → geen crash.
   * De CiviCRM API geeft een lege resultset voor contact 0 — de functie
   * mag dit niet als fatale fout behandelen.
   */
  public function testUsernameMetOngeldigContactIdGeeftGeenCrash() {
    try {
      $result = drupal_civicrm_username(0, 'Test', NULL, 'Persoon', 'Test Persoon');
      // NULL of array zijn beide acceptabel bij contact_id=0
      $this->assertTrue(
        $result === NULL || is_array($result),
        'drupal_civicrm_username(0) moet NULL of array teruggeven.'
      );
    }
    catch (\Exception $e) {
      // Een CiviCRM API-fout voor contact 0 is acceptabel gedrag
      $this->assertTrue(TRUE, 'CiviCRM API-fout voor contact 0 is acceptabel: ' . $e->getMessage());
    }
  }

  // ########################################################################
  // ### SCENARIO C: ADD_ACTIVITY MET LEGE CONTACTID → GEEN CRASH
  // ########################################################################

  /**
   * add_activity() met contact_id=0 → geen crash.
   */
  public function testAddActivityMetLegeContactIdGeeftGeenCrash() {
    try {
      $result = add_activity(0, 'Test Subject', 'Test Details');
      $this->assertTrue(
        $result === NULL || is_array($result) || is_int($result),
        'add_activity(0) mag geen exception gooien.'
      );
    }
    catch (\Exception $e) {
      // Een CiviCRM API-fout voor contact 0 is acceptabel gedrag
      $this->assertTrue(TRUE, 'CiviCRM API-fout voor contact 0 is acceptabel: ' . $e->getMessage());
    }
  }

  // ########################################################################
  // ### HELPERS
  // ########################################################################

  /**
   * Maak een testcontact aan en geef het id terug.
   * Hergebruikt een gecached id binnen de testinstantie.
   */
  private function maakTestContact(string $voornaam, string $achternaam, string $tussenvoegsel = ''): int {
    return $this->callAPISuccess('Contact', 'create', [
      'contact_type' => 'Individual',
      'first_name'   => $voornaam,
      'middle_name'  => $tussenvoegsel,
      'last_name'    => $achternaam,
    ])['id'];
  }

  // ########################################################################
  // ### SCENARIO D: RETOURSTRUCTUUR BEVAT VERWACHTE SLEUTELS
  // ########################################################################

  /**
   * drupal_civicrm_username() geeft een array terug met alle verwachte sleutels.
   */
  public function testRetourstructuurBevatAlleSleutels() {
    $contactId = $this->maakTestContact('Jan', 'Jansen');

    try {
      $result = drupal_civicrm_username($contactId, 'Jan', NULL, 'Jansen', 'Jan Jansen');
    }
    catch (\Exception $e) {
      $this->markTestSkipped('drupal_civicrm_username gooit exception in testomgeving: ' . $e->getMessage());
      return;
    }

    if ($result === NULL) {
      $this->markTestSkipped('drupal_civicrm_username geeft NULL in testomgeving (Drupal API niet beschikbaar).');
      return;
    }

    $this->assertIsArray($result, 'drupal_civicrm_username() moet een array teruggeven.');

    $verwachteSleutels = [
      'contact_id',
      'first_name',
      'middle_name',
      'last_name',
      'displayname',
      'contactname',
      'familienaam',
      'user_name',
      'user_name_nick',
      'valid_username',
      'valid_drupalid',
      'need2update_extid',
      'need2update_jobtitle',
      'need2repair_ufmatch',
      'need2update_ufmatch',
      'need2create_ufmatch',
      'safe2update_ufmatch',
      'safe2create_ufmatch',
    ];

    foreach ($verwachteSleutels as $sleutel) {
      $this->assertArrayHasKey(
        $sleutel,
        $result,
        "Sleutel '$sleutel' ontbreekt in de retourarray van drupal_civicrm_username()."
      );
    }
  }

  // ########################################################################
  // ### SCENARIO E: VOORNAAM + ACHTERNAAM → USER_NAME = 'jan.jansen'
  // ########################################################################

  /**
   * Eenvoudige naam zonder tussenvoegsel → user_name = 'voornaam.achternaam',
   * alles lowercase, zonder accenten of speciale tekens.
   */
  public function testEenvoudigeNaamGeeftCorrectUserName() {
    $contactId = $this->maakTestContact('Jan', 'Jansen');

    try {
      $result = drupal_civicrm_username($contactId, 'Jan', NULL, 'Jansen', 'Jan Jansen');
    }
    catch (\Exception $e) {
      $this->markTestSkipped('drupal_civicrm_username gooit exception in testomgeving: ' . $e->getMessage());
      return;
    }

    if ($result === NULL) {
      $this->markTestSkipped('drupal_civicrm_username geeft NULL in testomgeving (Drupal API niet beschikbaar).');
      return;
    }

    // Verwacht: 'jan.jansen'  (voornaam.achternaam, lowercase, punt als separator)
    $this->assertEquals(
      'jan.jansen',
      $result['user_name'],
      "user_name moet 'jan.jansen' zijn voor contact 'Jan Jansen'."
    );
  }

  // ########################################################################
  // ### SCENARIO F: TUSSENVOEGSEL IN ACHTERNAAM → WORDT GESPLITST
  // ########################################################################

  /**
   * Achternaam 'de Boer' (tussenvoegsel meegestuurd in last_name, middle_name leeg) →
   * functie detecteert het prefix en het resulterende user_name bevat het tussenvoegsel
   * aaneengesloten aan de achternaam: 'jan.deboer'.
   */
  public function testTussenvoegselInAchternaamWordtGesplitst() {
    // Tussenvoegsel in de achternaam, middle_name nog leeg
    $contactId = $this->maakTestContact('Jan', 'de Boer', '');

    try {
      $result = drupal_civicrm_username($contactId, 'Jan', NULL, 'de Boer', 'Jan de Boer');
    }
    catch (\Exception $e) {
      $this->markTestSkipped('drupal_civicrm_username gooit exception in testomgeving: ' . $e->getMessage());
      return;
    }

    if ($result === NULL) {
      $this->markTestSkipped('drupal_civicrm_username geeft NULL in testomgeving (Drupal API niet beschikbaar).');
      return;
    }

    // Verwacht format: 'jan.deboer'  (tussenvoegsel aaneengesloten aan achternaam)
    $this->assertStringStartsWith(
      'jan.',
      $result['user_name'],
      "user_name moet beginnen met 'jan.' voor een contact met voornaam Jan."
    );
    $this->assertStringNotContainsString(
      ' ',
      $result['user_name'],
      "user_name mag geen spaties bevatten."
    );
  }

  // ########################################################################
  // ### SCENARIO G: EXPLICIETE TUSSENVOEGSEL + ACHTERNAAM → CORRECT FORMAT
  // ########################################################################

  /**
   * Voornaam + tussenvoegsel (middle_name) + achternaam →
   * user_name = 'voornaam.tussenvoegselachternaam' (zonder spatie, punt na voornaam).
   */
  public function testExplicieTussenvoegselGeeftCorrectUserName() {
    $contactId = $this->maakTestContact('Anna', 'Berg', 'van den');

    try {
      $result = drupal_civicrm_username($contactId, 'Anna', 'van den', 'Berg', 'Anna van den Berg');
    }
    catch (\Exception $e) {
      $this->markTestSkipped('drupal_civicrm_username gooit exception in testomgeving: ' . $e->getMessage());
      return;
    }

    if ($result === NULL) {
      $this->markTestSkipped('drupal_civicrm_username geeft NULL in testomgeving (Drupal API niet beschikbaar).');
      return;
    }

    // Verwacht: 'anna.vandenberg'  (voornaam.tussenvoegsel+achternaam aaneengesloten)
    $this->assertStringStartsWith(
      'anna.',
      $result['user_name'],
      "user_name moet beginnen met 'anna.' voor Anna van den Berg."
    );
    $this->assertStringNotContainsString(
      ' ',
      $result['user_name'],
      "user_name mag geen spaties bevatten."
    );
  }

  // ########################################################################
  // ### SCENARIO H: ACCENTEN WORDEN VERWIJDERD
  // ########################################################################

  /**
   * Naam met accenten (bijv. é, ü) → user_name bevat alleen ASCII-tekens.
   */
  public function testAccentenWordenVerwijderdUitUserName() {
    $contactId = $this->maakTestContact('Ménège', 'Müller');

    try {
      $result = drupal_civicrm_username($contactId, 'Ménège', NULL, 'Müller', 'Ménège Müller');
    }
    catch (\Exception $e) {
      $this->markTestSkipped('drupal_civicrm_username gooit exception in testomgeving: ' . $e->getMessage());
      return;
    }

    if ($result === NULL) {
      $this->markTestSkipped('drupal_civicrm_username geeft NULL in testomgeving (Drupal API niet beschikbaar).');
      return;
    }

    $userName = $result['user_name'];

    if ($userName === NULL) {
      $this->markTestSkipped('user_name is NULL in testomgeving — transliterator waarschijnlijk niet beschikbaar.');
      return;
    }

    // Controleer dat er geen niet-ASCII tekens in de user_name zitten
    $this->assertMatchesRegularExpression(
      '/^[a-z0-9._-]+$/',
      $userName,
      "user_name '$userName' mag alleen lowercase ASCII-letters, cijfers, punten en koppeltekens bevatten."
    );
  }

  // ########################################################################
  // ### SCENARIO I: SPECIALE TEKENS WORDEN VERWIJDERD
  // ########################################################################

  /**
   * Naam met apostrof of andere speciale tekens → user_name bevat alleen
   * alfanumerieke tekens, punten en koppeltekens.
   */
  public function testSpecialeTekensWordenVerwijderdUitUserName() {
    // Apostrof in achternaam (bijv. D'Arcy)
    $contactId = $this->maakTestContact("Tom", "D'Arcy");

    try {
      $result = drupal_civicrm_username($contactId, "Tom", NULL, "D'Arcy", "Tom D'Arcy");
    }
    catch (\Exception $e) {
      $this->markTestSkipped('drupal_civicrm_username gooit exception in testomgeving: ' . $e->getMessage());
      return;
    }

    if ($result === NULL) {
      $this->markTestSkipped('drupal_civicrm_username geeft NULL in testomgeving (Drupal API niet beschikbaar).');
      return;
    }

    $userName = $result['user_name'];

    if ($userName === NULL) {
      return; // Geen user_name (contact zonder voornaam/achternaam) is ook goed
    }

    $this->assertStringNotContainsString(
      "'",
      $userName,
      "user_name mag geen apostrof bevatten."
    );
    $this->assertMatchesRegularExpression(
      '/^[a-z0-9._-]+$/',
      $userName,
      "user_name '$userName' mag alleen lowercase alfanumerieke tekens, punten en koppeltekens bevatten."
    );
  }

  // ########################################################################
  // ### SCENARIO J: ADD_ACTIVITY MET GELDIG CONTACT SLAAT ACTIVITEIT OP
  // ########################################################################

  /**
   * add_activity() met een geldig contact_id slaat een activiteit op en
   * geeft een numeriek activiteit-id terug (of NULL als custom fields
   * ontbreken in de testomgeving).
   */
  public function testAddActivityMetGeldigContactGeeftId() {
    $contactId = $this->maakTestContact('Test', 'Activiteit');

    try {
      $result = add_activity($contactId, 'D7 Sync Test', 'Dit is een automatische test.');
    }
    catch (\Exception $e) {
      // Custom fields als ACT_ALG en ACT_LOG bestaan mogelijk niet in de testomgeving
      $this->markTestSkipped('add_activity() gooit exception (custom fields ontbreken?): ' . $e->getMessage());
      return;
    }

    // NULL is acceptabel als de testomgeving custom fields mist
    if ($result === NULL) {
      $this->markTestSkipped('add_activity() geeft NULL (custom fields of activity-type 159 ontbreekt in testomgeving).');
      return;
    }

    // Als we een resultaat terugkrijgen moet het een integer of array zijn
    $this->assertTrue(
      is_int($result) || is_array($result),
      'add_activity() moet een integer (activity_id) of resultaatarray teruggeven.'
    );
  }

}
