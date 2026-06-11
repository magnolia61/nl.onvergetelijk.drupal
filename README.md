# nl.onvergetelijk.drupal

## Functionele beschrijving

De `drupal`-extensie zorgt voor een betrouwbare koppeling tussen CiviCRM-contacten en Drupal-websiteaccounts. Elke begeleider en deelnemer die dit jaar meekan, heeft een Drupal-account nodig voor toegang tot de website. `drupal` bewaakt dat deze koppeling correct en uniek is, detecteert afwijkingen (orphan-accounts, dubbele koppelingen, verkeerde emailadressen) en herstelt deze automatisch.

De module is bewust uitgebreid en defensief: bij elke aanroep doorloopt het een reeks controles via meerdere databronnen (CiviCRM UFMatch, Drupal API, email-lookup) om te voorkomen dat accounts worden aangemaakt terwijl er al een bestaat, of dat twee contacten aan hetzelfde Drupal-account worden gekoppeld.

## Afhankelijkheden

- `nl.onvergetelijk.base`

---

## Technische documentatie

### Kernfunctie

`drupal_civicrm_configure($contactid, $displayname, $usermail, $ditjaar_array, $allpart_array)` — de hoofdmotor (±2100 regels). Doorloopt achtereenvolgens:

1. **1.x — Account info ophalen**: zoekt het Drupal-account en de UFMatch-koppeling op via meerdere methoden (CiviCRM API op CID, CiviCRM API op extern ID, Drupal LoadByName, Drupal LoadByMail, UFMatch op mail)
2. **2.x — Rogue match detectie**: controleert of gevonden Drupal-accounts of UFMatches niet al aan een ander contact zijn gekoppeld
3. **3.x — Username/mail controle**: zoekt potentiële naam- of mailconflicten op
4. **4.x — Conclusiebepaling**: stelt de geldige waarden vast voor Drupal-ID, UFMatch-ID, gebruikersnaam en emailadres
5. **5.x — Veiligheidscontroles**: blokades voor systeem-integriteitsfouten, orphan-detectie, email-collisions
6. **6.x — Aanmaken**: maakt Drupal-account en UFMatch aan als ze ontbreken
7. **7.x — Bijwerken**: past bestaand Drupal-account en UFMatch bij als email of naam is gewijzigd

### Hulpfuncties
- `find_contact()`, `find_ufmatch()`, `diag_ufmatch()` (uit `base.helpers.contact.php`) worden extensief gebruikt voor de opzoekstappen.

### Hooks geïmplementeerd
- `civicrm_config`, `civicrm_install`, `civicrm_enable`
- Geen directe CiviCRM-hook registraties; wordt altijd aangeroepen vanuit `core_civicrm_custom`.

---

*Beheerd door Stichting Onvergetelijke Zomerkampen.*
