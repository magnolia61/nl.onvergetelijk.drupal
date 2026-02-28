<?php

require_once 'drupal.civix.php';

use CRM_Drupal_ExtensionUtil as E;

/**
 * Implements hook_civicrm_config().
 *
 * @link https://docs.civicrm.org/dev/en/latest/hooks/hook_civicrm_config/
 */
function drupal_civicrm_config(&$config): void {
  _drupal_civix_civicrm_config($config);
}

/**
 * Implements hook_civicrm_install().
 *
 * @link https://docs.civicrm.org/dev/en/latest/hooks/hook_civicrm_install
 */
function drupal_civicrm_install(): void {
  _drupal_civix_civicrm_install();
}

/**
 * Implements hook_civicrm_enable().
 *
 * @link https://docs.civicrm.org/dev/en/latest/hooks/hook_civicrm_enable
 */
function drupal_civicrm_enable(): void {
  _drupal_civix_civicrm_enable();
}

function drupal_civicrm_configure($contactid, $displayname, $usermail, $ditjaar_array = NULL, $allpart_array = NULL) {

    $extdebug       = 0;          // 1 = basic // 2 = verbose // 3 = params / 4 = results
    $apidebug       = FALSE;

    $extdrupal      = 1;
    $extwrite       = 1;

    $contact_id     = $contactid;
    $displayname    = $displayname;
    $ditjaar_array  = $ditjaar_array;
    $allpart_array  = $allpart_array;
    $user_mail      = $usermail;

    $need2create_account        = 0;
    $need2update_account        = 0;
    $need2create_ufmatch        = 0;
    $need2update_ufmatch        = 0;
    $need2repair_ufmatch        = 0;
    $need2update_extid          = 0;
    $need2update_jobtitle       = 0;
    $need2update_ufmatch_ufid   = 0;
    $need2update_ufmatch_mail   = 0;
    $need2update_drupal_name    = 0;

    $safe2create_account        = 0;
    $safe2update_account        = 0;
    $safe2create_ufmatch        = 0;
    $safe2update_ufmatch        = 0;
    $safe2repair_ufmatch        = 0;
    $safe2update_extid          = 0;
    $safe2update_jobtitle       = 0;
    $safe2update_ufmatch_ufid   = 0;
    $safe2update_ufmatch_mail   = 0;
    $safe2update_drupal_name    = 0;

    ### CONSTRUCT A PASSWORD
    $user_pwd       = bin2hex(openssl_random_pseudo_bytes(8));

    $today_datetime = date("Y-m-d H:i:s");

    $ditjaar_pos_kampfunctie    = $allpart_array['result_allpart_pos_kampfunctie'] ?? NULL;
    $ditjaar_pos_kampkort       = $allpart_array['result_allpart_pos_kampkort']    ?? NULL;

    $contact_values = [];

//  if ($contact_id > 0 AND $ditjaar_array) {
    if ($contact_id > 0) {

        wachthond($extdebug,1, "########################################################################");
        wachthond($extdebug,1, "### DRUPAL - START CHECK VOOR $displayname $ditjaar_pos_kampfunctie $ditjaar_pos_kampkort", "[cid: $contact_id]");
        wachthond($extdebug,1, "########################################################################");     

    } else {
        return; // if not, get out of here
    }    

    wachthond($extdebug,3, "user_mail",         $user_mail);
    wachthond($extdebug,4, "ditjaar_array",     $ditjaar_array);
    wachthond($extdebug,4, "allpart_array",     $allpart_array);

    $ditjaardeelyes         =   $ditjaar_array['ditjaardeelyes']    ?? NULL;
    $ditjaardeelnot         =   $ditjaar_array['ditjaardeelnot']    ?? NULL;
    $ditjaardeelmss         =   $ditjaar_array['ditjaardeelmss']    ?? NULL;
    $ditjaardeelstf         =   $ditjaar_array['ditjaardeelstf']    ?? NULL;
    $ditjaardeeltst         =   $ditjaar_array['ditjaardeeltst']    ?? NULL;
    $ditjaardeeltxt         =   $ditjaar_array['ditjaardeeltxt']    ?? NULL;
    $ditjaarleidyes         =   $ditjaar_array['ditjaarleidyes']    ?? NULL;
    $ditjaarleidnot         =   $ditjaar_array['ditjaarleidnot']    ?? NULL;
    $ditjaarleidmss         =   $ditjaar_array['ditjaarleidmss']    ?? NULL;
    $ditjaarleidstf         =   $ditjaar_array['ditjaarleidstf']    ?? NULL;
    $ditjaarleidtst         =   $ditjaar_array['ditjaarleidtst']    ?? NULL;
    $ditjaarleidtxt         =   $ditjaar_array['ditjaarleidtxt']    ?? NULL;

    $ditjaar_pos_part_id                = $allpart_array['result_allpart_pos_part_id']              ?? NULL;
    $ditjaar_pos_deel_part_id           = $allpart_array['result_allpart_pos_deel_part_id']         ?? NULL;
    $ditjaar_pos_leid_part_id           = $allpart_array['result_allpart_pos_leid_part_id']         ?? NULL;

    $ditjaar_pos_event_id               = $allpart_array['result_allpart_pos_event_id']             ?? NULL;
    $ditjaar_pos_deel_event_id          = $allpart_array['result_allpart_pos_deel_event_id']        ?? NULL;
    $ditjaar_pos_leid_event_id          = $allpart_array['result_allpart_pos_leid_event_id']        ?? NULL;

    $ditjaar_pos_event_type_id          = $allpart_array['result_allpart_pos_event_type_id']        ?? NULL;
    $ditjaar_pos_deel_event_type_id     = $allpart_array['result_allpart_pos_deel_event_type_id']   ?? NULL;
    $ditjaar_pos_leid_event_type_id     = $allpart_array['result_allpart_pos_leid_event_type_id']   ?? NULL;

    $ditjaar_pos_part_status_id         = $allpart_array['result_allpart_pos_part_status_id']       ?? NULL;
    $ditjaar_pos_deel_part_status_id    = $allpart_array['result_allpart_pos_deel_part_status_id']  ?? NULL;
    $ditjaar_pos_leid_part_status_id    = $allpart_array['result_allpart_pos_leid_part_status_id']  ?? NULL;

    $ditjaar_pos_kampfunctie            = $allpart_array['result_allpart_pos_kampfunctie']          ?? NULL;
    $ditjaar_pos_deel_kampfunctie       = $allpart_array['result_allpart_pos_deel_kampfunctie']     ?? NULL;
    $ditjaar_pos_leid_kampfunctie       = $allpart_array['result_allpart_pos_leid_kampfunctie']     ?? NULL;

    $ditjaar_pos_kampkort               = $allpart_array['result_allpart_pos_kampkort']             ?? NULL;
    $ditjaar_pos_deel_kampkort          = $allpart_array['result_allpart_pos_deel_kampkort']        ?? NULL;
    $ditjaar_pos_leid_kampkort          = $allpart_array['result_allpart_pos_leid_kampkort']        ?? NULL;

    wachthond($extdebug,4, "ditjaar_pos_kampfunctie",   $ditjaar_pos_kampfunctie);
    wachthond($extdebug,4, "ditjaar_pos_kampkort",      $ditjaar_pos_kampkort);

    wachthond($extdebug,2, "########################################################################");
    wachthond($extdebug,2, "### DRUPAL 1.X START DRUPAL ACCOUNT INFO & REPAIR");
    wachthond($extdebug,2, "########################################################################");

    wachthond($extdebug,2, "########################################################################");
    wachthond($extdebug,2, "### DRUPAL 1.1 - CHECK CONNECTED DRUPAL ACCOUNT",        "[$displayname]");
    wachthond($extdebug,2, "########################################################################");

    $array_contditjaar = base_cid2cont($contact_id);
    wachthond($extdebug,3, "array_contditjaar", $array_contditjaar);

    $contact_type               = $array_contditjaar['contact_type']            ?? NULL;
    $contact_subtype            = $array_contditjaar['contact_subtype']         ?? NULL;

    wachthond($extdebug,3, "contact_type",      $contact_type);
    wachthond($extdebug,3, "contact_subtype",   $contact_subtype);

    $first_name                 = $array_contditjaar['first_name']              ?? NULL;
    $middle_name                = $array_contditjaar['middle_name']             ?? NULL;
    $last_name                  = $array_contditjaar['last_name']               ?? NULL;        
    $nick_name                  = $array_contditjaar['nick_name']               ?? NULL;
    $displayname                = $array_contditjaar['displayname']             ?? NULL;
    $crm_drupalnaam             = $array_contditjaar['crm_drupalnaam']          ?? NULL;    // drupal username
    $crm_externalid             = $array_contditjaar['crm_externalid']          ?? NULL;    // drupal cms id
    $leeftijd_nextkamp          = $array_contditjaar['leeftijd_nextkamp_deci']  ?? NULL;    // in decimalen

    $array_username = drupal_civicrm_username ($contact_id, $first_name, $middle_name, $last_name, $displayname, $nick_name);
    wachthond($extdebug,3, "array_username", $array_username);

    $user_name                  = $array_username['user_name']                  ?? NULL;
    $valid_username             = $array_username['valid_username']             ?? NULL;
    $valid_drupalid             = $array_username['valid_drupalid']             ?? NULL;

    if ($user_name) { $email_plac_email = $user_name."@placeholder.nl"; }

    $params_contact = [
//      'reload'            =>  TRUE,
        'checkPermissions'  =>  FALSE,
        'debug'             => $apidebug,
        'where' => [
            ['id',               '=',  $contact_id],
            ['contact_sub_type', '!=', 'Staf'],
            ['contact_type',     '=',  'Individual'],
        ],
        'values' => [
            'id'            =>  $contact_id,
            'display_name'  =>  $displayname,
        ],
    ];

    wachthond($extdebug,3, "########################################################################");
    wachthond($extdebug,2, "### DRUPAL 1.2 CHECK OP DRUPAL ACCOUNT VIA CIVICRM API", "[VIA CID $contact_id]");
    wachthond($extdebug,3, "########################################################################");

    $crm_drupal_account_found       = 0;
    $crm_drupal_account_danger      = 0;
    $crm_drupal_account_name_safe   = 0;
    $crm_drupal_account_mail_safe   = 0;

    $params_drupaluser = [
        'return'        => ["id","name"],
        'contact_id'    => $contact_id,
        'sequential'    => 1,
    ];
    try{
        wachthond($extdebug,7, 'params_drupaluser',     $params_drupaluser);
        $result_drupaluser = civicrm_api3('User','get', $params_drupaluser);
        wachthond($extdebug,9, 'result_drupaluser',     $result_drupaluser);
    }
    catch (CiviCRM_API3_Exception $e) {
        // Handle error here.
        $errorMessage   = $e->getMessage();
        $errorCode      = $e->getErrorCode();
        $errorData      = $e->getExtraParams();
        wachthond($extdebug,4, "ERRORCODE: $errorCode", $errorMessage);
    }

    ##########################################################################################
    // HANDLE LOGIC
    ##########################################################################################

    if ($result_drupaluser) {

        $crm_drupal_account_id      = $result_drupaluser['values'][0]['id']     ?? NULL;
        $crm_drupal_account_name    = $result_drupaluser['values'][0]['name']   ?? NULL;
        $crm_drupal_account_mail    = $result_drupaluser['values'][0]['email']  ?? NULL;
        wachthond($extdebug,3, 'crm_drupal_account_id',     $crm_drupal_account_id);
        wachthond($extdebug,3, 'crm_drupal_account_name',   $crm_drupal_account_name);
        wachthond($extdebug,3, 'crm_drupal_account_mail',   $crm_drupal_account_mail);

        ##########################################################################################
        ### CONNECTED DRUPAL NAAM ###
        ##########################################################################################
        if ($crm_drupal_account_name) {
            wachthond($extdebug,2,  "EEN CONNECTED DRUPAL NAAM GEVONDEN (UID: $crm_drupal_account_id)",     
                                    "PRIMA!");
            $crm_drupal_account_found       = 1;
        } else {
            wachthond($extdebug,2,  "GEEN CONNECTED DRUPAL NAAM GEVONDEN",  
                                    "ERROR (VIA CID $contact_id)");
        }

        ##########################################################################################
        ### WARNING ###
        ##########################################################################################
        if ($crm_externalid > 0     AND $crm_drupal_account_id  != $crm_externalid) {
            wachthond($extdebug,2,  "DRUPALID ($crm_drupal_account_id) != CRMEXTID ($crm_externalid)",
                                    "WARNING [DRUPALID DANGER]");
            $crm_drupal_account_danger      = 1;
        }

        if (empty($crm_externalid)  AND $crm_drupal_account_id > 0) {
            wachthond($extdebug,2,  "DRUPAL ACCOUNT FOUND BUT CMSEXTID EMPTY",
                                    "[UPDATE CMSEXTID TO $crm_drupal_account_id]");
            $need2update_extid              = 1;
        }

        if ($crm_drupal_account_name != $user_name) {
            wachthond($extdebug,2,  "DRUPALNAME != CRMNAME ($user_name)",
                                    "WARNING [DRUPALNAME DANGER]");
            $crm_drupal_account_name_safe   = 0;
        }

        ##########################################################################################
        ### PRIMA ###
        ##########################################################################################
        if (empty($crm_externalid) AND $crm_drupal_account_id) {
            $crm_drupal_account_danger      = 0;
            $crm_drupal_account_name_safe   = 1;
            $valid_drupalid                 = $crm_drupal_account_id;
        }

        if ($crm_externalid AND $crm_drupal_account_id      AND $crm_drupal_account_id   == $crm_externalid) {
            wachthond($extdebug,2, "DRUPALID == CRMEXTID", "PRIMA [$crm_drupal_account_id]");
            $crm_drupal_account_danger      = 0;
            $crm_drupal_account_name_safe   = 1;
        }           
        if ($user_name      AND $crm_drupal_account_name    AND $crm_drupal_account_name == $user_name) {
            wachthond($extdebug,2, "DRUPALNAME == CRMNAME", "PRIMA [$user_name]");
            $crm_drupal_account_danger      = 0;
            $crm_drupal_account_name_safe   = 1;
        }

    } else {
        wachthond($extdebug,2, "VIA CIVICRM API OBV CID GEEN CONNECTED DRUPAL ACCOUNT GEVONDEN");           
    }

    wachthond($extdebug,3, "########################################################################");
    wachthond($extdebug,2, "### DRUPAL 1.3 CHECK OP DRUPAL ACCOUNT VIA DRUPAL API", "[VIA CMSEXTID $crm_externalid]");
    wachthond($extdebug,3, "########################################################################");

    $cms_drupal_account_found       = 0;
    $cms_drupal_account_danger      = 0;
    $cms_drupal_account_name_safe   = 0;
    $cms_drupal_account_mail_safe   = 0;

    if (empty($crm_externalid)) {

        wachthond($extdebug,2, "CMSEXTID IS LEEG DUS KON HIER OBV NIET CHECKEN", "[NOT FOUND]");

    } else {

        global $cms_drupal_account;
        $cms_drupal_account      = user_load($crm_externalid);

        if ($cms_drupal_account !== false && is_object($cms_drupal_account)) {

            $cms_drupal_account_id   = $cms_drupal_account->uid;
            $cms_drupal_account_mail = $cms_drupal_account->mail;
            $cms_drupal_account_name = $cms_drupal_account->name;

            $cms_drupal_account_role = $cms_drupal_account->roles;

            wachthond($extdebug,3, "cms_drupal_account_id",     $cms_drupal_account_id);
            wachthond($extdebug,3, "cms_drupal_account_mail",   $cms_drupal_account_mail);
            wachthond($extdebug,3, "cms_drupal_account_name",   $cms_drupal_account_name);
            wachthond($extdebug,4, "cms_drupal_account",        $cms_drupal_account);
        }

        ##########################################################################################
        // HANDLE LOGIC
        ##########################################################################################

        if ($cms_drupal_account_id > 0) {
            wachthond($extdebug,2, "VIA CMSEXTID ($crm_externalid) DRUPAL ACCOUNT ($cms_drupal_account_id) GEVONDEN", 
                                    "[PRIMA!]");
            $cms_drupal_account_found   = 1;
        }

        if ($cms_drupal_account_id > 0 AND $cms_drupal_account_name != $user_name) {
            wachthond($extdebug,2,  "VIA CMSEXTID ($crm_externalid) DRUPAL ACCOUNT ($cms_drupal_account_id) GEVONDEN",
                                    "CHECK [WANT ANDERE USER_NAME]");
            $cms_drupal_account_danger  = 1;
        }

        if ($cms_drupal_account_id > 0 AND $cms_drupal_account_mail != $user_mail) {
            wachthond($extdebug,2,  "VIA CMSEXTID ($crm_externalid) DRUPAL ACCOUNT ($cms_drupal_account_id) GEVONDEN",
                                    "CHECK [WANT ANDERE USER_MAIL]");
            $cms_drupal_account_danger  = 1;
        }

        wachthond($extdebug,3, "user_name (contructed)",        $user_name);
        wachthond($extdebug,3, "cms_drupal_account_name",       $cms_drupal_account_name);
        wachthond($extdebug,3, "job_title (crm drupalnaam)",    $crm_drupalnaam);

        if ($cms_drupal_account_name == $user_name AND $cms_drupal_account_name == $crm_drupalnaam) {
            wachthond($extdebug,2,  "DRUPALNAME == CONSTRUCTED USERNAME == civcrm_jobtitle",
                                    "PRIMA [VIA CRMEXTID $crm_externalid]");

            $cms_drupal_account_name_safe   = 1;

        } else {
            wachthond($extdebug,2,  "DRUPALNAME != CONSTRUCTED USERNAME != civcrm_jobtitle", 
                                    "ERROR [VIA CRMEXTID ($crm_externalid]");
            wachthond($extdebug,3,  "user_name_nick (meisjesnaam)", $user_name_nick);

            // M61 TODO: KAN SOMS VOORKOMEN INDIEN GETROUWD EN DAN NIEUWE NAAM OF BIJ ECHTE DUBBELE NAAM
            // MSS CHECK OP USERNAME VIA NIEUWE NAAM VS MEISJESNAAM
        }

        wachthond($extdebug,3, "user_mail (constructed)",       $user_mail);
        wachthond($extdebug,3, "cms_drupal_account_mail",       $cms_drupal_account_mail);

        if ($cms_drupal_account_mail == $user_mail) {
            wachthond($extdebug,2,  "DRUPALMAIL == CIVICRM EMAIL",                          
                                    "PRIMA [VIA CRMEXTID $crm_externalid]");
            $cms_drupal_account_mail_safe = 1;
        }

        wachthond($extdebug,3, "cms_drupal_account_name_safe",      $cms_drupal_account_name_safe);
        wachthond($extdebug,3, "cms_drupal_account_mail_safe",      $cms_drupal_account_mail_safe);

    }

    wachthond($extdebug,3, "########################################################################");
    wachthond($extdebug,2, "### DRUPAL 1.4 CHECK VIA CIVICRM API OP UF_MATCH", "[VIA CID: $contact_id]");
    wachthond($extdebug,3, "########################################################################");

    $found_cid_ufmatch      = 0;
    $rogue_cid_ufmatch      = 0;

    $inputtype              = 'contactid';
    $input                  = $contact_id;
    $inputname              = "CONTACTID";

    $cid_ufmatch            = find_ufmatch($inputtype, $input)  ?? NULL;
    $cid_ufmatch_found      = (is_object($cid_ufmatch) == true ?  1 : 0 );
    wachthond($extdebug,4, "cid_ufmatch",       $cid_ufmatch);
    wachthond($extdebug,3, "cid_ufmatch_found", $cid_ufmatch_found);

    ##########################################################################################
    // HANDLE LOGIC
    ##########################################################################################

    if ($cid_ufmatch_found == 1 ) {

        if ($cid_ufmatch->cid   == $contact_id) {
            wachthond($extdebug,2,  "IN MATCH KLOPT UFMATCH CID MET CONTACT ID ($cid_ufmatch->cid)",
                                    "PRIMA [CID_MATCH_ID: $cid_ufmatch->id]");
            $found_cid_ufmatch  = 1;
        }
        if ($cid_ufmatch->ufid  == $crm_drupal_account_id) {
            wachthond($extdebug,2,  "IN MATCH KLOPT UFMATCH UFID MET CONNECTED DID ($crm_drupal_account_id)",
                                    "PRIMA [CID_MATCH_ID: $cid_ufmatch->id]");
        }
        if ($cid_ufmatch->ufid  != $crm_drupal_account_id) {

            $rogue_cid_ufmatch  = 1;

            $cid_ufmatch_findcontact        = find_contact('drupalid',  $cid_ufmatch->ufid) ?? NULL;
            $cid_ufmatch_findcontact_found  = (is_object($cid_ufmatch_findcontact) == true ?  1 : 0 );
            wachthond($extdebug,4, "cid_ufmatch_findcontact",           $cid_ufmatch_findcontact);
            wachthond($extdebug,3, "cid_ufmatch_findcontact_found",     $cid_ufmatch_findcontact_found);

            if ($cid_ufmatch_findcontact_found == 1) {

                wachthond($extdebug,3, "cid_ufmatch_findcontact->cid",  $cid_ufmatch_findcontact->cid);
                wachthond($extdebug,3, "cid_ufmatch_findcontact->naam", $cid_ufmatch_findcontact->naam);

                if ($cid_ufmatch_findcontact->cid == $contact_id) {
                    wachthond($extdebug,2, "IN MATCH WIJST UFMATCH CID NAAR ROGUE CONTACT ($cid_ufmatch_findcontact->naam)", "ERROR!");
                    $need2repair_ufmatch = 1;
                }
                if ($cid_ufmatch_findcontact->cid != $contact_id) {
                    wachthond($extdebug,2, "IN MATCH WIJST UFMATCH CID NAAR ROGUE CONTACT ($cid_ufmatch_findcontact->naam)", "ERROR!");
                    $need2repair_ufmatch = 1;
                }

            } else {
                wachthond($extdebug,2, "IN MATCH WIJST UFMATCH UFID NAAR ROGUE CONTACT ($cid_ufmatch->ufid)",       "ERROR!");
                wachthond($extdebug,2, "BIJ DID $cid_ufmatch->ufid VIA FIND_CONTACT GEEN CONTACT GEVONDEN",         "ERROR!");
                $need2repair_ufmatch = 1;  
            }
        }

    } else {
        wachthond($extdebug,2, "VIA $inputname ($input) GEEN UFMATCH GEVONDEN", "[NOT FOUND]");
    }

    wachthond($extdebug,3, "########################################################################");
    wachthond($extdebug,2, "### DRUPAL 1.5 CHECK VIA CIVICRM API OP UF_MATCH", "[VIA CMSEXTID $crm_externalid]");
    wachthond($extdebug,3, "########################################################################");

    $found_cmsid_ufmatch    = 0;
    $rogue_cmsid_ufmatch    = 0;

    if (empty($crm_externalid)) {

        wachthond($extdebug,2, "CMSEXTID IS LEEG DUS KON HIER OBV NIET CHECKEN", "[NOT FOUND]");

    } else {

        $inputtype              = 'drupalid';
        $input                  = $crm_externalid;
        $inputname              = "CMSID";

        $cmsid_ufmatch          = find_ufmatch($inputtype, $input)  ?? NULL;
        $cmsid_ufmatch_found    = (is_object($cmsid_ufmatch) == true ?  1 : 0 ); 
        wachthond($extdebug,4, "cmsid_ufmatch",         $cmsid_ufmatch);
        wachthond($extdebug,3, "cmsid_ufmatch_found",   $cmsid_ufmatch_found);

        ##########################################################################################
        // HANDLE LOGIC
        ##########################################################################################
        if ($cmsid_ufmatch_found == 1 ) {

            // HANDLE MAIN LOGIC
            if ($cmsid_ufmatch->ufid    == $crm_externalid) {
                wachthond($extdebug,2,  "IN MATCH KLOPT UFMATCH UFID MET CMSEXTID ($cmsid_ufmatch->ufid)",
                                        "PRIMA [CMSID_MATCH_ID: $cmsid_ufmatch->id]");      
                $found_cmsid_ufmatch    = 1;
            }

            // HANDLE EXTRA LOGIC
            if ($cmsid_ufmatch->cid     == $contact_id) {
                wachthond($extdebug,2,  "IN MATCH KLOPT UFMATCH CID MET CONTACT_ID ($cmsid_ufmatch->cid)",
                                        "PRIMA [CMSID_MATCH_ID: $cmsid_ufmatch->id]");
            }

            if ($cmsid_ufmatch->cid     != $contact_id) {

                $rogue_cmsid_ufmatch    = 1;

                $cmsid_ufmatch_findcontact          = find_contact('contactid', $cmsid_ufmatch->cid)            ?? NULL;
                $cmsid_ufmatch_findcontact_found    = (is_object($cmsid_ufmatch_findcontact) == true ?  1 : 0 );
                wachthond($extdebug,4, "cmsid_ufmatch_findcontact",         $cmsid_ufmatch_findcontact);
                wachthond($extdebug,3, "cmsid_ufmatch_findcontact_found",   $cmsid_ufmatch_findcontact_found);

                if ($cmsid_ufmatch_findcontact_found == 1) {

                    wachthond($extdebug,3, "cmsid_ufmatch_findcontact_contactid",               $cmsid_ufmatch_findcontact->cid);
                    wachthond($extdebug,3, "cmsid_ufmatch_findcontact_externalid (drupal id)",  $cmsid_ufmatch_findcontact->cmsid);
                    wachthond($extdebug,3, "cmsid_ufmatch_findcontact_drupalnaam (user_name)",  $cmsid_ufmatch_findcontact->name);
                    wachthond($extdebug,3, "cmsid_ufmatch_findcontact_displayname (civicrm)",   $cmsid_ufmatch_findcontact->naam);

                    wachthond($extdebug,2,  "IN MATCH WIJST UFMATCH CID NAAR ROGUE CONTACT ($cmsid_ufmatch_findcontact->cid)",
                                            "ERROR! ($cmsid_ufmatch_findcontact->naam)");

                } else {
                    wachthond($extdebug,2, "IN MATCH WIJST UFMATCH CID NAAR ORPHAN CONTACT ($cmsid_ufmatch->cid)",  "ERROR!");
                    wachthond($extdebug,2, "BIJ CID $cmsid_ufmatch->cid VIA FIND_CONTACT GEEN CONTACT GEVONDEN",    "ERROR!");                  
                }
            }

        } else {
            wachthond($extdebug,2, "VIA $inputname ($input) GEEN UFMATCH GEVONDEN", "[NOT FOUND]");
        }
    }

    wachthond($extdebug,3, "########################################################################");
    wachthond($extdebug,2, "### DRUPAL 1.6 CHECK VIA CIVICRM API OP UF_MATCH", "[VIA USERMAIL: $user_mail]");
    wachthond($extdebug,3, "########################################################################");

    $found_mail_ufmatch     = 0;
    $rogue_mail_ufmatch     = 0;

    $inputtype              = 'usermail';
    $input                  = $user_mail;
    $inputname              = "USERMAIL";

    $mail_ufmatch           = find_ufmatch($inputtype, $input)  ?? NULL;
    $mail_ufmatch_found     = (is_object($mail_ufmatch) == true ?  1 : 0 ); 

    ##########################################################################################
    // HANDLE LOGIC
    ##########################################################################################

    if ($mail_ufmatch->cid > 0) {

        wachthond($extdebug,4, "mail_ufmatch",      $mail_ufmatch);
        wachthond($extdebug,3, "mail_ufmatch_found",$mail_ufmatch_found);

        // HANDLE MAIN LOGIC
        if ($mail_ufmatch->name == $user_mail) {
            wachthond($extdebug,2, "IN MATCH VIA MAIL KLOPT UFMATCH_NAME MET USER_MAIL", 
                "PRIMA [$mail_ufmatch->name]");     
            $found_mail_ufmatch     = 1;
        }

        // HANDLE EXTRA LOGIC
        if ($mail_ufmatch->cid  == $contact_id) {
            wachthond($extdebug,2, "IN MATCH VIA MAIL KLOPT UFMATCH CID MET CONTACT_ID", 
                "PRIMA [MATCH_ID: $mail_ufmatch->id / MATCH_CID: $mail_ufmatch->cid]");
            $found_mail_ufmatch     = 1;
        }

        if ($mail_ufmatch->cid  != $contact_id) {

            $rogue_mail_ufmatch     = 1;
                    
            $mail_ufmatch_findcontact       = find_contact('contactid', $mail_ufmatch->cid)         ?? NULL;
            $mail_ufmatch_findcontact_found = (is_object($mail_ufmatch_findcontact) == true ?  1 : 0 );
            wachthond($extdebug,4, "mail_ufmatch_findcontact",          $mail_ufmatch_findcontact);
            wachthond($extdebug,3, "mail_ufmatch_findcontact_found",    $mail_ufmatch_findcontact_found);

            if ($mail_ufmatch_findcontact_found == 1) {

                wachthond($extdebug,3, "mail_ufmatch_findcontact_contactid",                $mail_ufmatch_findcontact->cid);
                wachthond($extdebug,2, "mail_ufmatch_findcontact_externalid  (drupal id)",  $mail_ufmatch_findcontact->cmsid);
                wachthond($extdebug,2, "mail_ufmatch_findcontact_drupalnaam  (user_name)",  $mail_ufmatch_findcontact->name);
                wachthond($extdebug,2, "mail_ufmatch_findcontact_displayname (civicrm)",    $mail_ufmatch_findcontact->naam);

                wachthond($extdebug,2,  "IN MATCH VIA MAIL WIJST UFMATCH CID NAAR ANDER CONTACT",
                                        "CHECK! ($mail_ufmatch_findcontact->naam) [DUBBEL? FAMILIE?]");

                if ($mail_ufmatch_findcontact->name == $user_name) {
                    // ALS USERNAME OVEREENKOMT DAN TOCH MOGELIJK ECHT DE JUSTE DIE OPNIEUW GEKOPPELD MOET WORDEN
                    wachthond($extdebug,2,  "USERNAME VAN ANDER CONTACT (CID:$mail_ufmatch_findcontact->cid) IS ZELFDE",
                                            "[WRS DUBBEL CONTACT]");
                }

            } else {
                wachthond($extdebug,2,  "IN MATCH VIA MAIL WIJST UFMATCH CID NAAR ANDER CONTACT ($mail_ufmatch->cid)",
                                        "CHECK! [DUBBEL? FAMILIE?]");
                wachthond($extdebug,2,  "BIJ CID $mail_ufmatch->cid VIA FIND_CONTACT GEEN CONTACT GEVONDEN", "[NOT FOUND]");
            }
        }

    } else {
        wachthond($extdebug,2, "VIA $inputname ($input) GEEN UFMATCH GEVONDEN", "ERROR!");

        $usermail_notinany_ufmatch  = 1;
        wachthond($extdebug,2, "usermail_notinany_ufmatch",     $usermail_notinany_ufmatch);

//          $mail_notinany_cmsaccount   = 1;
//          $usermail_notinany_crmcontact   = 1;

    }

    ##########################################################################################
    if ($crm_drupal_account_danger == 1 OR $contact_id) {
    ##########################################################################################

        wachthond($extdebug,3, "########################################################################");
        wachthond($extdebug,2, "### DRUPAL 2.1 A CHECK VIA DRUPAL API CMSEXTID $crm_externalid", "ROGUE MATCH?");
        wachthond($extdebug,3, "########################################################################");
    
        if (empty($crm_externalid)) {

            wachthond($extdebug,2, "CMSEXTID IS LEEG DUS KON HIER OBV NIET CHECKEN", "[NOT FOUND]");

        } else {

            global $cmsextid_account;
            $cmsextid_account = user_load($crm_externalid);

            ##########################################################################################
            // HANDLE LOGIC
            ##########################################################################################

            if ($cmsextid_account->uid > 0 AND $cmsextid_account->uid != $crm_externalid AND $crm_externalid > 0) {

                wachthond($extdebug,2, "VIA CRMEXTID $crm_externalid CONFLICTEREND ACCOUNT ($cmsextid_account->uid) GEVONDEN", "DANGER!");

                wachthond($extdebug,4, "cmsextid_account_name",     $cmsextid_account->name);
                wachthond($extdebug,4, "cmsextid_account_mail",     $cmsextid_account->mail);
                wachthond($extdebug,4, "cmsextid_account_uid",      $cmsextid_account->uid);

                $safe2repair_account = 0;
            } else {
                wachthond($extdebug,2, "VIA CRMEXTID $crm_externalid GEEN ROGUE ACCOUNT GEVONDEN");
            }
        }

        wachthond($extdebug,3, "########################################################################");
        wachthond($extdebug,2, "### DRUPAL 2.2 B CHECK VIA DRUPAL API CMSID_UFMATCH_UFID $cmsid_ufmatch->ufid", "ROGUE MATCH?");
        wachthond($extdebug,3, "########################################################################");

        if ($cmsid_ufmatch_found == 0) {

            wachthond($extdebug,2, "GEEN MAIL_UFMATCH GEVONDEN DUS KON HIER OBV NIET CHECKEN","[NOT FOUND]");

        } else {

            global $cmsid_ufid_account;
            $cmsid_ufid_account = user_load($cmsid_ufmatch->ufid);

            wachthond($extdebug,4, "cmsid_ufid_account_name",   $cmsid_ufid_account->name);
            wachthond($extdebug,4, "cmsid_ufid_account_mail",   $cmsid_ufid_account->mail);
            wachthond($extdebug,4, "cmsid_ufid_account_uid",    $cmsid_ufid_account->uid);

            ##########################################################################################
            // HANDLE LOGIC
            ##########################################################################################

            if ($cmsid_ufid_account->uid > 0 AND $cmsid_ufid_account->uid != $crm_externalid AND $crm_externalid > 0) {

                wachthond($extdebug,2, "VIA CMS UFMATCH_UFID ($cmsid_ufmatch->ufid) ROGUE UFMATCH ($cmsid_ufid_account) GEVONDEN", "DANGER!");

                $safe2repair_ufmatch    = 0;

            } else {

                wachthond($extdebug,2, "VIA CMSID_UFMATCH $cmsid_ufmatch->ufid GEEN ROGUE ACCOUNT GEVONDEN");
                $safe2repair_ufmatch    = 1;
            }
        }

        wachthond($extdebug,3, "########################################################################");
        wachthond($extdebug,2, "### DRUPAL 2.3 C CHECK VIA CIVICRM API UFMATCH_UFID $mail_ufmatch->ufid", "ROGUE/ORPHAN?");
        wachthond($extdebug,3, "########################################################################");

        if ($mail_ufmatch_found == 0) {

            wachthond($extdebug,2, "GEEN MAIL_UFMATCH GEVONDEN DUS KON HIER OBV NIET CHECKEN","[NOT FOUND]");

        } else {

            global $mail_ufid_account;
            $mail_ufid_account = user_load($mail_ufmatch->ufid);

            wachthond($extdebug,4, "mail_ufid_account_mail",    $mail_ufid_account->mail);
            wachthond($extdebug,4, "mail_ufid_account_uid",     $mail_ufid_account->uid);
            wachthond($extdebug,4, "mail_ufid_account_name",    $mail_ufid_account->name);

            ##########################################################################################
            // HANDLE LOGIC
            ##########################################################################################

            if ($mail_ufid_account->uid != $crm_externalid AND $crm_externalid > 0) {

                wachthond($extdebug,2, "VIA UFMATCH MAIL ($mail_ufmatch->ufid) MSS ROGUE ACCOUNT ($mail_ufid_account->name) GEVONDEN", "CHECK!");

                $safe2repair_ufmatch = 0;

            } else {

                wachthond($extdebug,2, "VIA UFMATCH MAIL ($mail_ufmatch->ufid) GEEN ROGUE ACCOUNT GEVONDEN");

                $safe2repair_ufmatch = 1;
            }
        }

        wachthond($extdebug,3, "########################################################################");
        wachthond($extdebug,2, "### DRUPAL 2.4 D CHECK IF DRUPALID IS CRMEXTID FOR OTHER CONTACT", "$crm_drupal_account_id");
        wachthond($extdebug,3, "########################################################################");

        if (empty($crm_drupal_account_id)) {

            wachthond($extdebug,2, "GEEN CRM_DRUPAL_ACCOUNT GEVONDEN DUS KON HIER OBV NIET CHECKEN","[NOT FOUND]");

        } else {

            $check_findcontact          = NULL;
            $check_findcontact          = find_contact('drupalid',  $crm_drupal_account_id) ?? NULL;
            $check_findcontact_found    = (is_object($check_findcontact) == true ?  1 : 0 );
            wachthond($extdebug,4, "check_findcontact",         $check_findcontact);
            wachthond($extdebug,3, "check_findcontact_found",   $check_findcontact_found);

            if ($check_findcontact_found == 1) {

                wachthond($extdebug,3, "check_findcontact_contactid",               $check_findcontact->cid);
                wachthond($extdebug,3, "check_findcontact_externalid  (drupal id)", $check_findcontact->cmsid);
                wachthond($extdebug,3, "check_findcontact_drupalnaam  (user_name)", $check_findcontact->name);
                wachthond($extdebug,3, "check_findcontact_displayname (civicrm)",   $check_findcontact->naam);

                $safe2update_account    = 0;
                $safe2update_jobtitle   = 0;

                wachthond($extdebug,2,  "SAFE2  UPDATE DRUPALACCOUNT",  $safe2update_account);
                wachthond($extdebug,2,  "IN MATCH WIJST UFMATCH UID NAAR ROGUE CONTACT",
                                        "ERROR! [$check_findcontact->cid]");
            } else {
                wachthond($extdebug,2,  "BIJ UID $crm_drupal_account_id GEEN CONTACT GEVONDEN", 
                                        "CHECK! [MOGELJK HERSTELBAAR]");    
            }
            ##########################################################################################
            // HANDLE LOGIC
            ##########################################################################################

            if ($check_findcontact->cid > 0 AND $check_findcontact->cid == $contact_id) {

                wachthond($extdebug,2, "CONNECTED UID IS EXTID ($crm_externalid) VAN $check_findcontact->naam", "PRIMA!");
                $need2update_extid = 1;
                $safe2update_extid = 1;
            }

            if ($check_findcontact->cid > 0 AND $check_findcontact->cid != $contact_id) {

                wachthond($extdebug,2,  "CONNECTED UID IS EXTID ($crm_externalid) VAN $check_findcontact->naam", 
                                        "SAFE2REPAIR? DANGER!");
                wachthond($extdebug,2,  "IN MATCH WIJST UFMATCH UID NAAR ROGUE CONTACT",
                                        "ERROR! ($check_findcontact->naam)");
                $safe2update_extid = 0;
            }
        }
    }

    wachthond($extdebug,3, "########################################################################");
    wachthond($extdebug,2, "### DRUPAL 3.1 CHECK USERNAME WITH LOADBYNAME",                $user_name);
    wachthond($extdebug,3, "########################################################################");

    if ($user_name) {

        // M61: mogelijk security risk omdat ander account met dezelfde naam gekoppeld kan worden!!!
        // Want er kunnen meerder mensen zijn die met hun naam dezelfde constructed username opleveren
        // Er is nog geen mechanisme om dan een aangepaste username te creeren
        // Misschien moet hier nog het emailadres aan een check toegevoegd worden

        $drupal_loadbyname          = NULL;
        $drupal_loadbyname          = user_load_by_name($user_name);
        $drupal_loadbyname_found    = 0;

        if ($drupal_loadbyname->uid > 0) {
            wachthond($extdebug,4, "drupal_loadbyname",         $drupal_loadbyname);
            wachthond($extdebug,3, "drupal_loadbyname->uid",    $drupal_loadbyname->uid);
            wachthond($extdebug,2, "drupal_loadbyname->mail",   $drupal_loadbyname->mail);
            wachthond($extdebug,2, "drupal_loadbyname->name",   $drupal_loadbyname->name);
        }

        ##########################################################################################
        // HANDLE LOGIC
        ##########################################################################################

        if ($drupal_loadbyname->uid > 0 AND $drupal_loadbyname->uid == $cid_ufmatch->ufid) {

            wachthond($extdebug,2,  "VIA USERNAME DRUPAL ACCOUNT ($drupal_loadbyname->uid) GEVONDEN",
                                    "PRIMA! [UFMATCH_ID: $cid_ufmatch->id]");

            $drupal_loadbyname_found    = 1;
            $drupal_loadbyname_conflict = 0;

            $need2update_extid  = 1;
            $safe2update_extid  = 1;

            wachthond($extdebug,2, "NEED2 UPDATE EXTERNALID",       $need2update_extid);
            wachthond($extdebug,2, "SAFE2 UPDATE EXTERNALID",       $safe2update_extid);            
        }

        if ($drupal_loadbyname->uid > 0 AND $drupal_loadbyname->uid != $cid_ufmatch->ufid) {

            $drupal_loadbyname_found    = 1;
            $drupal_loadbyname_conflict = 1;

            // 9.4 A 1 CHECK OF GEVONDEN DRUPAL ACCOUNT ERGENS EEN UFMATCH HEEFT

            $check_ufmatch          = NULL;
            $check_ufmatch          = find_ufmatch('drupalid', $drupal_loadbyname->uid) ?? NULL;
            $check_ufmatch_found    = (is_object($check_ufmatch) == true ?  1 : 0 );

            wachthond($extdebug,4, "cid_ufmatch", $check_ufmatch);

            if ($check_ufmatch->id > 0) {
                wachthond($extdebug,2, "VIA DRUPALID VAN USERNAME ANDERE UFMATCH GEVONDEN", "ROGUE! [MOGELIJK HERSTEL NODIG] ");
            } else {
                wachthond($extdebug,2, "VIA DRUPALID VAN USERNAME GEEN UFMATCH GEVONDEN",   "CHECK! [MOGELIJK ORPHAN ACCOUNT]");                
            }

            // 9.4 A 2 CHECK OF GEVONDEN DRUPAL ACCOUNT VERBONDEN IS MET EEN CRM CONTACT

            $drupal_loadbyname_findcontact          = find_contact('drupalid', $drupal_loadbyname->uid);
            $drupal_loadbyname_findcontact_found    = (is_object($drupal_loadbyname_findcontact) == true ?  1 : 0 );
            wachthond($extdebug,4, "drupal_loadbymail_findcontact",         $drupal_loadbyname_findcontact);
            wachthond($extdebug,3, "drupal_loadbymail_findcontact_found",   $drupal_loadbyname_findcontact_found);

            if ($drupal_loadbyname_findcontact->id > 0) {

                if ($drupal_loadbyname_findcontact->id == $contact_id) {
                    wachthond($extdebug,2, "VIA USERNAME GEVONDEN ACCOUNT KLOPT HET HUIDIG CONTACT",    "PRIMA! [UPDATE EXTID NODIG VOOR [$drupal_loadbyname_findcontact->name]");
                }
                if ($drupal_loadbyname_findcontact->id != $contact_id) {
                    wachthond($extdebug,2, "VIA USERNAME GEVONDEN ACCOUNT HEEFT EEN ROGUE EXTID MATCH", "ERROR! [CONFLICT MET $drupal_loadbyname_findcontact->naam]");
                }
            } else {
                    wachthond($extdebug,2, "VIA DRUPALID VAN USER_NAME GEEN CRM CONTACT GEVONDEN",      "CHECK! [MOGELIJK ORPHAN ACCOUNT]");
            }

            if ($check_ufmatch_found == 0 AND $drupal_loadbyname_findcontact_found == 0) {
                $drupal_loadbyname_orphan       = 1;
                $drupal_loadbyname_orphan_uid   = $drupal_loadbyname->uid;
                $drupal_loadbyname_orphan_name  = $drupal_loadbyname->name;
                wachthond($extdebug,2, "VIA USERNAME KOPPELBARE ORPHAN DRUPAL ACCOUNT GEVONDEN", "[$drupal_loadbyname_orphan_uid / $drupal_loadbyname->name]");
            }

        }

        if (empty($drupal_loadbyname->uid)) {

            wachthond($extdebug,2, "VIA USERNAAM ($user_name) DRUPAL ACCOUNT NIET GEVONDEN", "[NOT FOUND]");

            $drupal_loadbyname_found    = 0;
            $drupal_loadbyname_conflict = 0;
            // M61: als er geen drupal account gevonden is kan deze op 0.
        }

    } else {

        wachthond($extdebug,2, "NOT EXECUTED BECAUSE USER_NAME IS EMPTY");

    }
    
    wachthond($extdebug,3, "########################################################################");
    wachthond($extdebug,2, "### DRUPAL 3.2 CHECK USERMAIL WITH LOADBYMAIL",                $user_mail);
    wachthond($extdebug,3, "########################################################################");

    if ($user_mail) {

        $drupal_loadbymail          = NULL;
        $drupal_loadbymail          = user_load_by_mail($user_mail);
        $drupal_loadbymail_found    = 0;
        $drupal_loadbymail_conflict = 0;

        if ($drupal_loadbymail->uid > 0) {
            wachthond($extdebug,4, "drupal_loadbymail",         $drupal_loadbymail);
            wachthond($extdebug,3, "drupal_loadbymail->uid",    $drupal_loadbymail->uid);
            wachthond($extdebug,2, "drupal_loadbymail->mail",   $drupal_loadbymail->mail);
            wachthond($extdebug,2, "drupal_loadbymail->name",   $drupal_loadbymail->name);
        }

        ##########################################################################################
        // HANDLE LOGIC
        ##########################################################################################

        if ($drupal_loadbymail->uid > 0) {

            wachthond($extdebug,3, "########################################################################");
            wachthond($extdebug,2, "### DRUPAL 3.2 A CHECK OF GEVONDEN DRUPAL ACCOUNT WAAR DAN OOK EEN UFMATCH HEEFT");
            wachthond($extdebug,3, "########################################################################");

            $check_ufmatch                          = NULL;
            $check_ufmatch_found                    = 0;
            $check_findcontact_found                = 0;
            $drupal_loadbymail_findcontact_found    = 0;

            $check_ufmatch          = find_ufmatch('drupalid', $drupal_loadbymail->uid) ?? NULL;
            $check_ufmatch_found    = (is_object($check_ufmatch) == true ?  1 : 0 );
            wachthond($extdebug,4, "check_ufmatch",         $check_ufmatch);
            wachthond($extdebug,3, "check_ufmatch_found",   $check_ufmatch_found);

            if ($check_ufmatch->cid == $contact_id) {

                wachthond($extdebug,2,  "VIA USERMAIL DRUPAL ACCOUNT ($drupal_loadbymail->uid) GEVONDEN", 
                                        "PRIMA! [UFMATCH_ID: $cid_ufmatch->id]");

                $drupal_loadbymail_found    = 1;
                $drupal_loadbymail_conflict = 0;

                if ($drupal_loadbymail->mail != $cid_ufmatch->name) {

                    wachthond($extdebug,1, "SAFE2UPDATE: UFMATCH ($cid_ufmatch->id) MAIL VAN $cid_ufmatch->name NAAR", $user_mail);

                    $need2update_ufmatch_mail   = 1;
                    $safe2update_ufmatch_mail   = 1;
                    $valid_ufmatch              = $cid_ufmatch->id;

                    // M61: ALS VIA USER_MAIL EEN DRUPAL ACCOUNT GEVONDEN WORDT 
                    // M61: EN ER MET DIE USER_MAIL GEEN UFMATCH GEVONDEN
                    // M61: EN HET DRUPAL ID KOMT OVEREEN MET DE HUIDIGE UFMATCH
                    // M61: DAN IS HET SAFE OM DE GEVONDEN UFMATCH TE UPDATEN NAAR DIT EMAILADRES
                }
            }

            if ($check_ufmatch->cid != $contact_id) {

                // ALS ENIGE UFMATCH VIA MAIL IS KAN HET GOED ZIJN DAT DIE LINKT NAAR VALIDE ACCOUNT VAN GEZINSLID

                $check_findcontact          = find_contact('contactid', $check_ufmatch->cid) ?? NULL;
                $check_findcontact_found    = (is_object($check_findcontact) == true ?  1 : 0 );
                if ($check_findcontact_found == 1) {
                    wachthond($extdebug,4, "check_findcontact",         $check_findcontact);
                    wachthond($extdebug,3, "check_findcontact_found",   $check_findcontact_found);
                    wachthond($extdebug,2, "ROGUE CONTACT UFMATCH WIJST NAAR $check_findcontact->naam", "CHECK! [MOGELIJK FAMILIE]");
                } else {
                    wachthond($extdebug,2, "ROGUE CONTACT UFMATCH (CID: $check_ufmatch->cid) WIJST NIET NAAR CRM CONTACT", "CHECK! [MSS VERWIJDERD]");                  
                }
            }

            if ($check_ufmatch_found == 0) {
                wachthond($extdebug,2, "VIA DRUPALID VAN USERMAIL GEEN UFMATCH GEVONDEN",   
                    "CHECK! [MOGELIJK ORPHAN ACCOUNT]");                
            }

            wachthond($extdebug,3, "########################################################################");
            wachthond($extdebug,2, "### DRUPAL 3.2 B CHECK OF GEVONDEN DRUPAL ACCOUNT VERBONDEN IS MET EEN CRM CONTACT");
            wachthond($extdebug,3, "########################################################################");

            $drupal_loadbymail_findcontact          = find_contact('drupalid', $drupal_loadbymail->uid) ?? NULL;
            $drupal_loadbymail_findcontact_found    = (is_object($drupal_loadbymail_findcontact) == true ?  1 : 0 );
            wachthond($extdebug,4, "drupal_loadbymail_findcontact",         $drupal_loadbymail_findcontact);
            wachthond($extdebug,3, "drupal_loadbymail_findcontact_found",   $drupal_loadbymail_findcontact_found);

            if ($drupal_loadbymail_findcontact->cid > 0) {

                wachthond($extdebug,3, "drupal_loadbymail_findcontact->name",   $drupal_loadbymail_findcontact->name);

                if ($drupal_loadbymail_findcontact->cid == $contact_id) {
                    wachthond($extdebug,2,  "VIA MAIL GEVONDEN ACCOUNT KLOPT MET DIT CONTACT",  
                                            "PRIMA! [MSS UPDATE EXTID NODIG]");
                }
                if ($drupal_loadbymail_findcontact->cid != $contact_id) {
                    wachthond($extdebug,2,  "ROGUE CONTACT UFMATCH WIJST NAAR $check_findcontact->naam",
                                            "CHECK! [MOGELIJK FAMILIE]");
                }
            } else {
                    wachthond($extdebug,2,  "VIA UID $drupal_loadbymail->uid GEEN CONTACT GEVONDEN",
                                            "CHECK! [MOGELIJK ORPHAN ACCOUNT]");
            }

            wachthond($extdebug,3, "check_findcontact_found",   $check_findcontact_found);

            if ($check_ufmatch_found == 0 AND $drupal_loadbymail_findcontact_found == 0) {
                $drupal_loadbymail_orphan       = 1;
                $drupal_loadbymail_orphan_uid   = $drupal_loadbymail->uid;
                $drupal_loadbymail_orphan_name  = $drupal_loadbymail->name;
                wachthond($extdebug,2,  "VIA USERMAIL EEN KOPPELBARE ORPHAN DRUPAL ACCOUNT GEVONDEN", 
                                        "[$drupal_loadbymail_orphan_uid / $drupal_loadbymail->mail]");
            } else {
    //              wachthond($extdebug,2, "check_ufmatch_found",                   $check_ufmatch_found);
    //              wachthond($extdebug,2, "drupal_loadbymail_findcontact_found",   $drupal_loadbymail_findcontact_found);
            }

        } elseif (empty($drupal_loadbymail_mail)) {

            wachthond($extdebug,2, "VIA USERMAIL ($user_mail) DRUPAL ACCOUNT NIET GEVONDEN", "[NOT FOUND]");
            // KAN ZIJN DAT DIE DAN JUIST MOET WORDEN AANGEMAAKT
            // KAN DUS ZIJN DAT GELDT: $safe2update_ufmatch_mail == 1;
            // IN IEDER GEVAL GEEN CONFLICT NU MET DIT CONSTRUCTED USERMAIL AAN DE CMS KANT

            $mail_notinany_cmsaccount   = 1;
            wachthond($extdebug,2, "mail_notinany_cmsaccount",  $mail_notinany_cmsaccount);         

            // M61: TODO: HIER AL DE CHECK DOEN OF NIEUWE USERNAME IN UFMATCH GEWIJZIGD IS
            // M61: OF OF USERMAIL SWS VOORKOMT IN UF MATCH
        }

    } else {

        wachthond($extdebug,2, "NOT EXECUTED BECAUSE USER_MAIL IS EMPTY");

    }

    wachthond($extdebug,3, "########################################################################");
    wachthond($extdebug,2, "### DRUPAL 4.X BEPAAL OF DRUPAL ACCOUNT & UF MATCH CREATE OR UPDATE SAFE IS");
    wachthond($extdebug,3, "########################################################################");

    wachthond($extdebug,3, "########################################################################");
    wachthond($extdebug,2, "### DRUPAL 4.1 DETERMINE VALID VALUE FOR CMS UID", "FIND VALID DRUPALID");
    wachthond($extdebug,3, "########################################################################");
    wachthond($extdebug,3, "crm_externalid",                $crm_externalid);           // via opgeslagen veld in civicrm
    wachthond($extdebug,3, "crm_drupal_account_id",         $crm_drupal_account_id);    // via civicrm_api3('User','get     & $contact_id           V
    wachthond($extdebug,3, "cms_drupal_account_id",         $cms_drupal_account_id);    // via user_load                    & $crm_externalid
    wachthond($extdebug,3, "cmsextid_account->uid",         $cmsextid_account->uid);    // via user_load                    & $crm_externalid
    wachthond($extdebug,2, "cid_ufmatch->ufid",             $cid_ufmatch->ufid);        // via civicrm_api4('UFMatch','get' & $contact_id           V
    wachthond($extdebug,2, "cmsid_ufmatch->ufid",           $cmsid_ufmatch->ufid);      // via civicrm_api4('UFMatch','get' & $crm_externalid
    wachthond($extdebug,2, "mail_ufmatch_ufid",             $mail_ufmatch->ufid);       // via civicrm_api4('UFMatch','get' & $username (mail)
    wachthond($extdebug,2, "drupal_loadbyname->uid",        $drupal_loadbyname->uid);   // via user_load_by_name            & $user_name            V
    wachthond($extdebug,3, "drupal_loadbymail_uid",         $drupal_loadbymail->uid);   // via user_load_by_name            & $user_name            V
    wachthond($extdebug,3, "########################################################################");
    wachthond($extdebug,3, "DRUPAL VIA CRM FOUND",          "$crm_drupal_account_found (UID: $crm_drupal_account_id)");
    wachthond($extdebug,3, "DRUPAL VIA CMS FOUND",          "$cms_drupal_account_found (UID: $cms_drupal_account_id)");
    wachthond($extdebug,3, "########################################################################"); 

    ### BEPAAL VALID DRUPAL ID EERST OBV UFMATCH CID / CMSID / MAIL
    if ($cid_ufmatch->ufid > 0) {

        if (            $cid_ufmatch->ufid      > 0
                    AND $cid_ufmatch->ufid      == $cmsid_ufmatch->ufid     
                    AND $cmsid_ufmatch->ufid    == $mail_ufmatch->ufid)         {
            wachthond($extdebug,2,  "VALID DRUPALID VIA UFMATCH CID/CMSID/MAIL",    
                                    "VALID DRUPALID = $cid_ufmatch->ufid");
            $valid_drupalid     = $cid_ufmatch->ufid;

        } elseif (      $cid_ufmatch->ufid      > 0 
                    AND $cid_ufmatch->ufid      == $crm_drupal_account_id 
                    AND $crm_drupal_account_id  > 0)                            {
            ### BEPAAL VALID DRUPAL ID OBV UFMATCH CID ==  CRM_DRUPAL_ACCOUNTID
            wachthond($extdebug,2,  "VALID DRUPALID VIA UFMATCH & DRUPALACCOUNT",
                                    "VALID DRUPALID = $cid_ufmatch->ufid");
            $valid_drupalid     = $cid_ufmatch->ufid;
        }
    }

    ### ER IS GEEN VALID UFMATCH - ZOEK DRUPALID OP EEN ANDERE MANIER
    if ($cid_ufmatch_found == 0) {
        wachthond($extdebug,3, "CRM DRUPAL ACCOUNT DANGER",     $crm_drupal_account_danger);
        wachthond($extdebug,3, "CMS DRUPAL ACCOUNT DANGER",     $cms_drupal_account_danger);
        wachthond($extdebug,3, "CMS DRUPAL ACCOUNT NAME_SAFE",  $cms_drupal_account_name_safe);
        wachthond($extdebug,3, "CMS DRUPAL ACCOUNT MAIL_SAFE",  $cms_drupal_account_mail_safe);

        // A) ZOEK DRUPALID VIA CRM DRUPAL ACCOUNT
        if ($crm_drupal_account_found == 1 AND $crm_drupal_account_danger == 0) {

            $valid_drupalid = $crm_drupal_account_id;
            // M61 TODO: NOG CHECKEN OF DIT DRUPAL ID ERGENS VOORKOMT IN EEN UFMATCH

            wachthond($extdebug,3, "CID UFMATCH FOUND",             $cid_ufmatch_found);
            wachthond($extdebug,3, "CRM DRUPAL ACCOUNT ID",         $crm_drupal_account_id);
            wachthond($extdebug,3, "CRM DRUPAL ACCOUNT DANGER",     $crm_drupal_account_danger);
            wachthond($extdebug,2, "VALID DRUPALID ZONDER UFMATCH VIA CRM_UID","VALID DRUPALID = $valid_drupalid");
        }
    }

    ###

    If ($crm_drupal_account_id != $cms_drupal_account_id) {
        // M61: mogelijk duplicate drupal account met gelijkende username
        // M61: oplossing: mogelijk beste 1 van beide te verwijderen als het een orphan is
        wachthond($extdebug,3, "########################################################################");
        wachthond($extdebug,2, "CRM DRUPAL ACCOUNT ID != CMS DRUPAL ACCOUNT ID", "[WRS DUBBEL DRUPAL ACCOUNT]");
        wachthond($extdebug,3, "########################################################################");
        wachthond($extdebug,2, "CRM DRUPAL ACCOUNT ID",         $crm_drupal_account_id);
        wachthond($extdebug,2, "CRM DRUPAL ACCOUNT NAME",       $crm_drupal_account_name);
        wachthond($extdebug,2, "CRM DRUPAL ACCOUNT MAIL",       $crm_drupal_account_mail);
        wachthond($extdebug,3, "CRM DRUPAL ACCOUNT NAME_SAFE",  $crm_drupal_account_name_safe);
        wachthond($extdebug,3, "CRM DRUPAL ACCOUNT MAIL_SAFE",  $crm_drupal_account_mail_safe);
        wachthond($extdebug,2, "CRM DRUPAL ACCOUNT DANGER",     $crm_drupal_account_danger);

        $check_findcontact          = NULL;
        $check_findcontact          = find_contact('drupalid',  $crm_drupal_account_id) ?? NULL;
        $check_findcontact_found    = (is_object($check_findcontact) == true ?  1 : 0 );
        wachthond($extdebug,4, "check_findcontact",         $check_findcontact);
        wachthond($extdebug,3, "check_findcontact_found",   $check_findcontact_found);

        if ($check_findcontact_found == 1) {

            wachthond($extdebug,3, "check_findcontact_contactid",               $check_findcontact->cid);
            wachthond($extdebug,3, "check_findcontact_externalid  (drupal id)", $check_findcontact->cmsid);
            wachthond($extdebug,3, "check_findcontact_drupalnaam  (user_name)", $check_findcontact->name);
            wachthond($extdebug,3, "check_findcontact_displayname (civicrm)",   $check_findcontact->naam);

            $crm_drupal_account_prefer = 1;
        }

        if ($check_findcontact_found == 0) {
            wachthond($extdebug,2,  "GEEN CRM CONTACT GEVONDEN GEKOPPELD AAN DRUPALID $crm_drupal_account_id",                  
                                    "CHECK! ($crm_drupal_account_name)");
            $crm_drupal_account_prefer = 0;
        }

        wachthond($extdebug,3, "########################################################################");

        wachthond($extdebug,2, "CMS DRUPAL ACCOUNT ID",         $cms_drupal_account_id);
        wachthond($extdebug,2, "CMS DRUPAL ACCOUNT NAME",       $cms_drupal_account_name);
        wachthond($extdebug,2, "CMS DRUPAL ACCOUNT MAIL",       $cms_drupal_account_mail);
        wachthond($extdebug,3, "CMS DRUPAL ACCOUNT NAME_SAFE",  $cms_drupal_account_name_safe);
        wachthond($extdebug,3, "CMS DRUPAL ACCOUNT MAIL_SAFE",  $cms_drupal_account_mail_safe);
        wachthond($extdebug,2, "CMS DRUPAL ACCOUNT DANGER",     $cms_drupal_account_danger);

        $check_findcontact          = NULL;
        $check_findcontact          = find_contact('drupalid',  $cms_drupal_account_id) ?? NULL;
        $check_findcontact_found    = (is_object($check_findcontact) == true ?  1 : 0 );
        wachthond($extdebug,4, "check_findcontact",         $check_findcontact);
        wachthond($extdebug,3, "check_findcontact_found",   $check_findcontact_found);

        if ($check_findcontact_found == 1) {

            wachthond($extdebug,3, "check_findcontact_contactid",               $check_findcontact->cid);
            wachthond($extdebug,3, "check_findcontact_externalid  (drupal id)", $check_findcontact->cmsid);
            wachthond($extdebug,3, "check_findcontact_drupalnaam  (user_name)", $check_findcontact->name);
            wachthond($extdebug,3, "check_findcontact_displayname (civicrm)",   $check_findcontact->naam);

            $cms_drupal_account_prefer = 1;

        }
        if ($check_findcontact_found == 0) {
            wachthond($extdebug,2,  "GEEN CRM CONTACT GEVONDEN GEKOPPELD AAN DRUPALID $cms_drupal_account_id", 
                                    "CHECK! ($cms_drupal_account_name)");

            $cms_drupal_account_prefer = 0;
        }

        if ($crm_drupal_account_prefer == 1 AND $cms_drupal_account_prefer == 0) {
            // M61 TODO: KIJK OF DIT CMS DRUPAL ACCOUNT VEILIG KAN WORDEN VERWIJDERD
            $valid_drupalid             = $crm_drupal_account_id;
            $need2update_ufmatch_ufid   = 1;
            $safe2update_ufmatch_ufid   = 1;

            wachthond($extdebug,2,  "VALID DRUPALID IS VAN CRM_DRUPAL_ACCOUNT ($crm_drupal_account_id)",    
                                    $crm_drupal_account_name);
        }
        if ($crm_drupal_account_prefer == 0 AND $cms_drupal_account_prefer == 1) {
            // M61 TODO: KIJK OF DIT CRM DRUPAL ACCOUNT VEILIG KAN WORDEN VERWIJDERD
            $valid_drupalid             = $cms_drupal_account_id;
            $need2update_ufmatch_ufid   = 1;
            $safe2update_ufmatch_ufid   = 1;
            $need2update_ufmatch_mail   = 1;
            $safe2update_ufmatch_mail   = 1;
            $need2update_jobtitle       = 1;
            $need2update_extid          = 1;
            $safe2update_extid          = 1;
            wachthond($extdebug,2,  "VALID DRUPALID IS VAN CMS_DRUPAL_ACCOUNT ($cms_drupal_account_id)",
                                    $cms_drupal_account_name);
        }

    }

    ### WAT TE DOEN ALS ER ECHT GEEN VALID DRUPAL ID WORDT GEVONDEN?

    ### C) ZOEK EEN EVENTUELE ORPHAN DRUPAL ACCOUNT [ZONDER UFMATCH / NIET GEKOPPELD]

    if (    $crm_drupal_account_found   == 0
        AND $cms_drupal_account_found   == 0
        AND $cms_drupal_account_danger  == 0
        AND $drupal_loadbyname_found    == 0
        AND $drupal_loadbyname_conflict == 0) {

        if ($drupal_loadbyname_found == 0 AND $drupal_loadbyname_conflict == 0) {
            wachthond($extdebug,3, "DRUPAL BYNAME  FOUND",          $drupal_loadbyname_found);
            wachthond($extdebug,3, "DRUPAL NAME    CONFLICT",       $drupal_loadbyname_conflict);
        }
        if ($crm_drupal_account_found == 0 AND $cms_drupal_account_found == 0) {
            wachthond($extdebug,3, "DRUPAL VIA CRM FOUND",          $crm_drupal_account_found);
            wachthond($extdebug,3, "DRUPAL VIA CMS FOUND",          $cms_drupal_account_found);
        }
        if ($cms_drupal_account_danger == 0) {
            wachthond($extdebug,3, "DRUPAL ID      DANGER",         $crm_drupal_account_danger);
        }

        if ($drupal_loadbyname_orphan == 1) {
            wachthond($extdebug,3, "DRUPAL ORPHAN UID",             $drupal_loadbyname_orphan_uid);
            wachthond($extdebug,3, "DRUPAL ORPHAN NAME",            $drupal_loadbyname_orphan_name);
            wachthond($extdebug,3, "NEED2  REPAIR UFMATCH",         $need2repair_ufmatch);
            wachthond($extdebug,3, "SAFE2  REPAIR UFMATCH UID",     $need2repair_ufmatch_uid);

            $need2create_account        = 0;
            $safe2create_account        = 0;
            $need2repair_ufmatch        = 1;
            $need2repair_ufmatch_uid    = $drupal_loadbyname_orphan_uid;
//          $valid_drupalid             = $drupal_loadbyname_orphan_uid; // M61: PAS AAN INDIEN 100% SAFE

            wachthond($extdebug,1, "SAFE2 CREATE ACCOUNT FOR $displayname", "NOT! [REPAIR ORPHAN]");

        } else {
            $need2create_account        = 1;
            $safe2create_account        = 1;

            wachthond($extdebug,1, "SAFE2 CREATE ACCOUNT FOR $displayname", "YES! [username: $user_name]");
        }
    }

    if ($valid_drupalid == $crm_externalid AND $valid_drupalid == $cms_drupal_account_id) {
        $need2update_extid = 0;
    }

    if ($valid_drupalid > 0 AND $safe2update_account == 1 AND $need2update_extid == 1 AND $safe2update_extid == 1)  {
        $params_contact['values']['external_identifier']    = $valid_drupalid;
        wachthond($extdebug,2, "DOE UPDATE VAN EXTERNAL ID (CMSEXTID) MET DRUPALID", $valid_drupalid);
    }       

    wachthond($extdebug,3, "########################################################################");
    wachthond($extdebug,2, "### DRUPAL 4.2 DETERMINE VALID VALUE CMS KOPPELING", "FIND VALID UFMATCHID");
    wachthond($extdebug,3, "########################################################################");
    wachthond($extdebug,2, "FOUND MATCH CID",   "$found_cid_ufmatch ($cid_ufmatch->id)\tROGUE: $rogue_cid_ufmatch");
    wachthond($extdebug,2, "FOUND MATCH CMSID", "$found_cmsid_ufmatch ($cmsid_ufmatch->id)\tROGUE: $rogue_cmsid_ufmatch");
    wachthond($extdebug,2, "FOUND MATCH MAIL",  "$found_mail_ufmatch ($mail_ufmatch->id)\tROGUE: $rogue_mail_ufmatch");
    wachthond($extdebug,3, "########################################################################");

    // M61: TODO - VRAAG IS OF UFMATCH GEVONDEN VIA MAIL ALTIJD ROGUE IS OF GEWOON VAN ANDER GEZINSLID KAN ZIJN

    if ($rogue_cid_ufmatch == 1 OR $rogue_cmsid_ufmatch == 1 OR $rogue_mail_ufmatch == 1) {
        $rogue_ufmatch_found    = 1;
        wachthond($extdebug,2, "UFMATCH GEVONDEN MET ROGUE KOPPELING",      "ERROR");
    }

    if ($found_cid_ufmatch == 1 AND $found_cmsid_ufmatch == 1 AND $found_mail_ufmatch == 1) {
        $drupalufmatch_found    = 1;
        $valid_ufmatchid        = $cid_ufmatch->id;
        wachthond($extdebug,2, "UFMATCH GEVONDEN VIA CID + CMSID + MAIL",   "PRIMA ($valid_ufmatchid)");
    }

    if ($found_cid_ufmatch == 1 AND $found_cmsid_ufmatch != 1 AND $found_mail_ufmatch == 1) {
        $drupalufmatch_found    = 1;
        $valid_ufmatchid        = $cid_ufmatch->id;
        $need2update_extid      = 1;
//      $safe2update_extid      = 1;    // M61: TODO: KLOPT DEZE AFWEGING?
        wachthond($extdebug,2, "WEL UFMATCH GEVONDEN VIA CID + MAIL",       "MAAR GEEN UFMATCH VIA CMSID");
        // CHECKEN OF DE DRUPAL ID VIA CID & MAIL WEL GEKOPPELD IS AAN CRM ACCOUNT EN UPDATE DAN CMSEXTID
        wachthond($extdebug,2, "WRS OPLOSSING > UPDATE EXTERNALID NAAR $cid_ufmatch->ufid");
        wachthond($extdebug,3, "NEED2 UPDATE EXTERNALID",       $need2update_extid);
        wachthond($extdebug,3, "SAFE2 UPDATE EXTERNALID",       $safe2update_extid);
    }

    // INDIEN GEEN UFMATCH OF GELINKTE DRUPAL ACCOUNT GEVONDEN MAAR WEL DRUPAL ORPHAN ACCOUNT
    if ($found_cid_ufmatch != 1 AND $found_cmsid_ufmatch != 1 AND $found_mail_ufmatch != 1) {

        if ($drupal_loadbyname_orphan == 1) {

            $need2repair_ufmatch        = 1;
            $need2repair_ufmatch_uid    = $drupal_loadbyname_orphan_uid;
//          $safe2repair_ufmatch        = 1;    // M61: TODO: KLOPT DEZE AFWEGING?

            $need2create_ufmatch        = 1;
//          $safe2create_ufmatch        = 1;    // M61: TODO: KLOPT DEZE AFWEGING?

//          M61: TODO: EERST NOG EXTRA CHECKEN VOOR DEZE AAN KAN
//          $valid_drupalid             = $drupal_loadbyname_orphan_uid;
            wachthond($extdebug,2, "GEEN UFMATCH, WEL ORPHAN DRUPAL ACCOUNT", " $drupal_loadbyname_orphan_name]");
            wachthond($extdebug,2, "WRS OPLOSSING > CREATE UFMATCH MET UID $drupal_loadbyname_orphan_uid");
            wachthond($extdebug,3, "NEED2 CREATE UFMATCH",          $need2update_ufmatch);
            wachthond($extdebug,3, "SAFE2 CREATE UFMATCH",          $safe2update_ufmatch);
        }
    }

    ##########################################################################################

    wachthond($extdebug,3, "CRM DRUPAL ACCOUNT DANGER",     $crm_drupal_account_danger);
    wachthond($extdebug,3, "CMS DRUPAL ACCOUNT DANGER",     $cms_drupal_account_danger);
    wachthond($extdebug,3, "CMS DRUPAL ACCOUNT NAME_SAFE",  $cms_drupal_account_name_safe);
    wachthond($extdebug,3, "CMS DRUPAL ACCOUNT MAIL_SAFE",  $cms_drupal_account_mail_safe);

    // A) ZOEK EERST VIA BESTAANDE CID_UFMATCH & ANDERE MATCHES
    if ($cid_ufmatch->cid > 0) {

        if (    $cid_ufmatch->cid    === $cmsid_ufmatch->cid 
            AND $cmsid_ufmatch->cid  === $mail_ufmatch->cid    AND $rogue_cid_ufmatch == 0) {
            wachthond($extdebug,2, "UFMATCH GEVONDEN VIA CID + CMSID + MAIL",       "UFMATCHID: $cid_ufmatch->id");
            $valid_ufmatchid    = $cid_ufmatch->id;
        } elseif ($crm_drupal_account_id == $cid_ufmatch->ufid AND $rogue_cid_ufmatch == 0) {
            wachthond($extdebug,2, "UFMATCH GEVONDEN VIA CID_UFMATCH / NOT ROGUE",  "UFMATCHID: $cid_ufmatch->id");
            $valid_ufmatchid    = $cid_ufmatch->id;
        }
    }

    // B) ZOEK VERVOLGENS VIA BESTAANDE CID_UFMATCH & CRM DRUPAL ACCOUNT
    if ($drupal_loadbyname_conflict == 0 AND $cid_ufmatch_found         == 0 
                                         AND $crm_drupal_account_danger == 0
                                         AND $cms_drupal_account_danger == 0) {

        if ($crm_drupal_account_id == $cid_ufmatch->ufid) {
            $valid_ufmatchid    = $cid_ufmatch->id;
            wachthond($extdebug,2, "UFMATCH GEVONDEN DIE KLOPT MET CRM UID", "UFMATCHID: $cid_ufmatch->id");
        }
    }

    if ($valid_ufmatchid) {
        wachthond($extdebug,2, "SET VALID UFMATCHID TO", $valid_ufmatchid);
    }

    // C) WAT ALS ER ECHT GEEN JUISTE UFMATCH GEVONDEN KAN WORDEN?

    if (empty($valid_ufmatchid) AND $cid_ufmatch_found          == 0 
                                AND $crm_drupal_account_danger  == 0) {

        if ($valid_drupalid > 0 ) {
            $need2create_account = 0;
            $safe2create_account = 0;
            $need2create_ufmatch = 1;
            $safe2create_ufmatch = 1;
            wachthond($extdebug,2, "WEL DRUPALID / NO VALID UFMATCHID",         "[SAFE2CREATE]");
            wachthond($extdebug,2, "OPLOSSING > CREATE UFMATCH MET DRUPALID",   $valid_drupalid);
            wachthond($extdebug,3, "NEED2 CREATE UFMATCH",                      $need2create_ufmatch);
            wachthond($extdebug,3, "SAFE2 CREATE UFMATCH",                      $safe2create_ufmatch);
        } else {
            wachthond($extdebug,2, "GEEN DRUPALID / GEEN VALID UFMATCHID",      "[SAFE2CREATE]");
            wachthond($extdebug,2, "OPLOSSING > CREATE DRUPALID & UFMATCH",     "[SAFE2CREATE]");               
        }


    } else {
        wachthond($extdebug,3, "VALID UFMATCHID",           $valid_ufmatchid);
        wachthond($extdebug,3, "CID UFMATCH FOUND",         $cid_ufmatch_found);
        wachthond($extdebug,3, "CRM DRUPALACCOUNT DANGER",  $crm_drupal_account_danger);
    }

    wachthond($extdebug,3, "########################################################################");
    wachthond($extdebug,2, "### DRUPAL 4.3 DETERMINE VALID VALUE FOR USERNAME", "FIND VALID USERNAME");
    wachthond($extdebug,3, "########################################################################");
    wachthond($extdebug,2, "DRUPAL BYNAME  FOUND",          $drupal_loadbyname_found);
    wachthond($extdebug,2, "DRUPAL NAME    DANGER",         $crm_drupal_account_danger);
    wachthond($extdebug,2, "DRUPAL NAME    CONFLICT",       $drupal_loadbyname_conflict);
    wachthond($extdebug,3, "########################################################################");
    wachthond($extdebug,3, "user_name (constructed)",       $user_name);

    if ($crm_externalid > 0) {
        wachthond($extdebug,3, "crm_drupalnaam (job_title)",$crm_drupalnaam);
        wachthond($extdebug,3, "cms_drupal_account_name",   $cms_drupal_account_name);
    }
    wachthond($extdebug,3, "########################################################################");

    $valid_username = $user_name;       // M61: om te updaten later in job_title

    if (empty($crm_drupalnaam) OR $user_name != $crm_drupalnaam) {

        # SET JOB_TITLE IF MISSING
        $need2update_jobtitle   = 1;
        $safe2update_jobtitle   = 1;
        wachthond($extdebug,2, "SET JOB_TITLE IF MISSING TO",   $user_name);
    }

    if ($valid_username AND $need2update_jobtitle   AND $safe2update_jobtitle)      {
        $params_contact['values']['job_title']              = $valid_username;
        $contact_values['job_title']                        = $valid_username;
        wachthond($extdebug,2, "DOE UPDATE VAN CRMUSERNAME (JOBTITLE) MET USERNAME", $valid_username);
    }

    wachthond($extdebug,2, "SET VALID USERNAME TO", $valid_username);

    wachthond($extdebug,3, "########################################################################");
    wachthond($extdebug,2, "### DRUPAL 4.4 DETERMINE VALID VALUE FOR CMS MAIL", "FIND VALID USERMAIL");
    wachthond($extdebug,3, "########################################################################");

    wachthond($extdebug,2, "user_mail (constructed)",       $user_mail);
    wachthond($extdebug,2, "crm_drupal_account_name",       $crm_drupal_account_mail);
    wachthond($extdebug,3, 'email_home_email',              $email_home_email);
    wachthond($extdebug,3, 'email_plac_email',              $email_plac_email);
    wachthond($extdebug,3, 'cms_drupal_account_mail',       $cms_drupal_account_mail);
    wachthond($extdebug,3, "########################################################################");

    // 1. Bepaal wat het doel-mailadres moet zijn
    $target_mail = $user_mail; // Gebruik de $user_mail die we hierboven hebben vastgesteld

    // Bepaal het type voor de logging
    $mail_type = ($target_mail == $email_plac_email) ? "PLACEHOLDER" : "HOME";

    if ($target_mail) {

        // 2. Check of dit mailadres al ergens in CiviCRM UFMatch staat
        $check_match            = find_ufmatch('usermail', $target_mail);
        $mail_in_ufmatch        = (is_object($check_match) && $check_match->cid != $contact_id) ? 1 : 0;

        // 3. Check of dit mailadres al in Drupal bij een ANDER account staat
        $drupal_user_with_mail  = user_load_by_mail($target_mail);
        $mail_in_cms_other      = ($drupal_user_with_mail && $drupal_user_with_mail->uid != $valid_drupalid) ? 1 : 0;

        // Log resultaten van de veiligheidscheck
        if ($mail_in_ufmatch) {
            wachthond($extdebug, 1, "STOP: Mail $target_mail al in UFMatch bij CID: " . $check_match->cid);
        }

        if ($mail_in_cms_other) {
            $other_name = 'unknown'; // Fallback
            
            if (is_object($drupal_user_with_mail)) {
                $other_uid  = $drupal_user_with_mail->uid;
                $other_name = $drupal_user_with_mail->name; // Drupal gebruikersnaam ophalen
            } elseif (is_array($drupal_user_with_mail)) {
                $other_uid  = $drupal_user_with_mail['uid'];
                $other_name = $drupal_user_with_mail['name'];
            } else {
                $other_uid = 'unknown';
            }
            
            // Uitgebreidere logging met de naam erbij
            wachthond($extdebug, 1, "STOP: Mail $target_mail al in Drupal bij UID: $other_uid (Naam: $other_name)");            
        }

        // 4. Uitvoeren van de 'Need' en 'Safe' bepaling
        if ($cms_drupal_account_mail != $target_mail) {
            $need2update_drupal_mail  = 1;
            $need2update_ufmatch_mail = 1;
            
            // Het is safe als het nergens anders voorkomt
            if (!$mail_in_ufmatch && !$mail_in_cms_other) {
                $safe2update_drupal_mail  = 1;
                $safe2update_ufmatch_mail = 1;
                wachthond($extdebug, 1, "INFO: Update naar $mail_type mail is SAFE.");
            } else {
                $safe2update_drupal_mail  = 0;
                $safe2update_ufmatch_mail = 0;
                wachthond($extdebug, 1, "WARNING: Update naar $mail_type mail is NIET SAFE.");
            }
        } else {
            wachthond($extdebug, 2, "INFO: Drupal mail is al correct ($mail_type).");
        }
    }

    wachthond($extdebug,3, "########################################################################");
    wachthond($extdebug,3, "### DRUPAL 5.X CONCLUSIE");
    wachthond($extdebug,3, "########################################################################");     

    if ($cid_ufmatch->cid > 0 AND $crm_drupal_account_danger    == 0
                              AND $cms_drupal_account_danger    == 0
                              AND $drupal_loadbyname_conflict   == 0
                              AND $rogue_cid_ufmatch            == 0
                              AND $mail_ufmatch_found           == 0) {

        // INDIEN ER EEN UFMATCH IS EN DIE IS OP GEEN ENKELE MANIER ROGUE DAN ALTIJD EMAIL UPDATEN
        $need2update_ufmatch_mail   = 1;
        $safe2update_ufmatch_mail   = 1;    // M61: TODO: KLOPT DEZE AFWEGING?

        wachthond($extdebug,2,  "OPLOSSING > UPDATE UFMATCH MAIL VAN $mail_ufmatch->name", 
                                "NAAR $valid_usermail");
    }

    $valid_usermail = $user_mail;
    wachthond($extdebug,2, "SET VALID USERMAIL TO",         $valid_usermail);

/*
    REPAIR LOGIC:

    9.1 A   CHECK VIA CIVICRM API

            - WHEN CMS EXTERNAL ID HAS NO CONNECTED DRUPAL ACCOUNT
                a) drupal uid  != civicrm cmsextid
                b) drupal name != civicrm username

    9.1 B   CHECK VIA DRUPAL API

            - WHEN CMS EXTERNAL ID HAS NO CONNECTED DRUPAL ACCOUNT

    -------------------------------------------------------------------------------------------------------------

    DEZE 4 CHECKS INDIEN $crm_drupal_account_id != $crm_externalid

    9.2 A       CHECK VIA DRUPAL  API IF CMSEXTID HAS A ROGUE MATCH (with different CRM account)

    9.2 B       CHECK VIA CIVICRM API IF CMSEXTID HAS A ROGUE MATCH (with different CRM account)

    9.2 C       CHECK IF CONNECTED DRUPALID IS IN USE BY ANOTHER ACCOUNT AS CRMEXTID

    9.2 D       CHECK IF CRMEXTID CAN BE UPDATED WITH FOUND DRUPAL ID

    -------------------------------------------------------------------------------------------------------------

    9.3     - CHECK VIA CIVICRM API IF CID: $contact_id HAS A CONNECTED UF MATCH

    9.4     - CHECK IF CONSTRUCTED MAIL ($user_mail) HAS A CONFLICTING UF MATCH

    9.5     - CHECK IF CONSTRUCTED USERNAME ($user_name) IS ALREADY PRESENT (WITH user_load_by_name)

    -------------------------------------------------------------------------------------------------------------

    9.6     BEPAAL OF DRUPAL ACCOUNT CREATE OF RERPAIR SAFE IS

    SCENARIO A;
    1. GEEN CONNECTED DRUPAL ACCOUNT                $crm_drupal_account_found == 0
    2. GEEN CID UFMATCH / CMSID UF MATCH            $found_cmsid_ufmatch == 0           / $found_cid_ufmatch    == 0
    3. WEL  MAIL UFMATCH (MET ANDER ACCOUNT)        $found_mail_ufmatch                 / $rogue_cmsid_ufmatch  == 1
    4. LOADBYNAME LEVERT GEEN DRUPAL ACCOUNT OP     $drupal_loadbyname_found == 0

    OPLOSSSING:
    1. SAFE OM DRUPAL ACCOUNT AAN TE MAKEN
    2. SAFE OM DAT TE KOPPELEN MET EEN UF MATCH
*/

    ##########################################################################################
    // CHECK - DETERMINE IF DRUPAL USERNAME OR DRUPAL USERMAIL NEED TO BE UPDATED
    ##########################################################################################

    if ($valid_drupalid > 0) {

        wachthond($extdebug,3, "########################################################################");

        $existinguser = user_load($valid_drupalid);
        wachthond($extdebug,3, "existinguser_uid",  $existinguser->uid);
        wachthond($extdebug,2, "existinguser",      $existinguser);

        $existinguser_name_org  = $existinguser->name;
        $existinguser_mail_org  = $existinguser->mail;
        $existinguser_name_new  = $valid_username;
        $existinguser_mail_new  = $valid_usermail;

        if ($existinguser_name_org != $valid_username) {
            $need2update_drupal_name = 1;
            wachthond($extdebug,1, "NEED2UPDATE DRUPAL NAME", "[YES]");
            wachthond($extdebug,2, "existinguser_name_org",     $existinguser_name_org);
            wachthond($extdebug,2, "existinguser_name_new",     $existinguser_name_new);
        } else {
            wachthond($extdebug,1, "NEED2UPDATE DRUPAL NAME", "[AL OK]");               
            wachthond($extdebug,3, "existinguser_name_org",     $existinguser_name_org);
            wachthond($extdebug,3, "existinguser_name_new",     $existinguser_name_new);
        }

        if ($valid_username == $crm_drupalnaam) {   // GAAT ER VANUIT DAT CRM_DRUPALNAAM (jobtitle) EEN UNIEKE NAAM IS
            $safe2update_drupal_name = 1;
            wachthond($extdebug,2, "SAFE2UPDATE DRUPAL NAME", "[YES]");
        }

        if ($existinguser_mail_org != $valid_usermail) {
            $need2update_drupal_mail = 1;
            wachthond($extdebug,1, "NEED2UPDATE DRUPAL MAIL", "[YES]");
            wachthond($extdebug,2, "existinguser_mail_org",     $existinguser_mail_org);
            wachthond($extdebug,2, "existinguser_mail_new",     $existinguser_mail_new);

            if ($mail_notinany_cmsaccount == 1) {
                $safe2update_drupal_mail = 1;
                wachthond($extdebug,1, "MAIL NOT IN OTHER CMS ACCOUNT", "[SAFE2UPDATE = YES]");
            } else {
                wachthond($extdebug,1, "MAIL IN EXISTING CMS ACCOUNT",  "[SAFE2UPDATE = NOT]");
            }

        } elseif ($existinguser_name_org == $valid_username) {
            $need2update_drupal_name = 0;
            wachthond($extdebug,1, "NEED2UPDATE DRUPAL MAIL", "[AL OK]");           
            wachthond($extdebug,3, "existinguser_mail_org",     $existinguser_mail_org);
            wachthond($extdebug,3, "existinguser_mail_new",     $existinguser_mail_new);
        }
    }

    ##########################################################################################
    // CHECK - DETERMINE IF UFMATCH NAME OR UFMATCH MAIL NEED TO BE UPDATED
    ##########################################################################################

    $check_ufmatch          = NULL;
    $check_ufmatch          = find_ufmatch('contactid', $contact_id) ?? NULL;
    $check_ufmatch_found    = (is_object($check_ufmatch) == true ?  1 : 0 );

    if ($check_ufmatch_found == 1) {

        wachthond($extdebug,3, "check_ufmatch",                 $check_ufmatch);
        wachthond($extdebug,3, "check_ufmatch_found",           $check_ufmatch_found);

        $old_ufmatch_mail = $check_ufmatch->name;
        $new_ufmatch_mail = $valid_usermail;

        if ($old_ufmatch_mail != $new_ufmatch_mail) {

            $need2update_ufmatch_mail = 1;
            wachthond($extdebug,1, "NEED2UPDATE UFMATCH MAIL", "[YES]");            
            wachthond($extdebug,3, "old_ufmatch mail (ufmatch name)",   $old_ufmatch_mail);
            wachthond($extdebug,3, "new_ufmatch mail (ufmatch name)",   $new_ufmatch_mail);

            if ($mail_notinany_ufmatch == 1) {
                $safe2update_ufmatch_mail = 1;
                wachthond($extdebug,1, "MAIL NOT IN OTHER UFMATCH", "[SAFE2UPDATE = YES]");
            } else {
                wachthond($extdebug,1, "MAIL IN EXISTING UFMATCH",  "[SAFE2UPDATE = NOT]");
            }

        } else {

            $need2update_ufmatch_mail = 0;
            wachthond($extdebug,1, "NEED2UPDATE UFMATCH MAIL", "[AL OK]");
            wachthond($extdebug,3, "old_ufmatch mail (ufmatch name)",   $old_ufmatch_mail);
            wachthond($extdebug,3, "new_ufmatch mail (ufmatch name)",   $new_ufmatch_mail);
        }
    }

    wachthond($extdebug,3, "########################################################################");
    wachthond($extdebug,2, "VALID  DRUPALID",               $valid_drupalid);
    wachthond($extdebug,2, "VALID  UFMATCH",                $valid_ufmatchid);
    wachthond($extdebug,2, "VALID  USER_NAME",              $valid_username);
    wachthond($extdebug,2, "VALID  USER_MAIL",              $valid_usermail);
    wachthond($extdebug,3, "########################################################################");
    wachthond($extdebug,2, "NEED2  CREATE DRUPALACCOUNT",   $need2create_account);
    wachthond($extdebug,2, "NEED2  UPDATE DRUPALACCOUNT",   $need2update_account);
    wachthond($extdebug,2, "NEED2  UPDATE DRUPAL_MAIL",     $need2update_drupal_mail);
//  wachthond($extdebug,2, "NEED2  REPAIR DRUPALACCOUNT",   $need2repair_account);
    wachthond($extdebug,2, "NEED2  CREATE UFMATCH",         $need2create_ufmatch);
//  wachthond($extdebug,2, "NEED2  UPDATE UFMATCH",         $need2update_ufmatch);
    wachthond($extdebug,2, "NEED2  UPDATE UFMATCH_UFID",    $need2update_ufmatch_ufid);
    wachthond($extdebug,2, "NEED2  UPDATE UFMATCH_MAIL",    $need2update_ufmatch_mail);
//  wachthond($extdebug,2, "NEED2  REPAIR UFMATCH",         $need2repair_ufmatch);
    wachthond($extdebug,2, "NEED2  UPDATE EXTERNALID",      $need2update_extid);
    wachthond($extdebug,2, "NEED2  UPDATE JOBTITLE",        $need2update_jobtitle);
    wachthond($extdebug,3, "########################################################################");
    wachthond($extdebug,2, "SAFE2  CREATE DRUPALACCOUNT",   $safe2create_account);
    wachthond($extdebug,2, "SAFE2  UPDATE DRUPALACCOUNT",   $safe2update_account);
    wachthond($extdebug,2, "SAFE2  UPDATE DRUPAL_MAIL",     $safe2update_drupal_mail);
//  wachthond($extdebug,2, "SAFE2  REPAIR DRUPALACCOUNT",   $safe2repair_account);
    wachthond($extdebug,2, "SAFE2  CREATE UFMATCH",         $safe2create_ufmatch);
//  wachthond($extdebug,2, "SAFE2  UPDATE UFMATCH",         $safe2update_ufmatch);
    wachthond($extdebug,2, "SAFE2  UPDATE UFMATCH_UFID",    $safe2update_ufmatch_ufid);
    wachthond($extdebug,2, "SAFE2  UPDATE UFMATCH_MAIL",    $safe2update_ufmatch_mail);
//  wachthond($extdebug,2, "SAFE2  REPAIR UFMATCH",         $safe2repair_ufmatch);
    wachthond($extdebug,2, "SAFE2  UPDATE EXTERNALID",      $safe2update_extid);
    wachthond($extdebug,2, "SAFE2  UPDATE JOBTITLE",        $safe2update_jobtitle);
    wachthond($extdebug,3, "########################################################################");

    wachthond($extdebug,3, "########################################################################");
    wachthond($extdebug,2, "### DRUPAL 5.1 - CHECKING FOR PENDING UPDATES",           "[NEED2UPDATE]");
    wachthond($extdebug,3, "########################################################################");

    $issues = [];
    
    if ($need2update_drupal_mail == 1) {
        $status     = ($safe2update_drupal_mail == 1) ? "INFO: Wordt hersteld" : "STOP: Actie nodig";
        $message    = "$status: Drupal mail is verkeerd ($cms_drupal_account_mail moet worden: $valid_usermail)";
        $issues[]   = $message;
        wachthond($extdebug, 1, $message, "[DETECTIE]");
    }

    if ($need2update_ufmatch_ufid == 1) {
        $status     = ($safe2update_ufmatch_ufid == 1) ? "INFO: Wordt hersteld" : "STOP: Actie nodig";
        $message    = "$status: Koppeling (UFMatch) verkeerd Drupal ID ($old_ufmatch_ufid moet worden: $new_ufmatch_ufid).";
        $issues[]   = $message;
        wachthond($extdebug, 1, $message, "[DETECTIE]");
    }

    if ($need2update_ufmatch_mail == 1) {
        $status     = ($safe2update_ufmatch_mail == 1) ? "INFO: Wordt hersteld" : "STOP: Actie nodig";
        $message    = "$status: Koppeling (UFMatch) mailadres mismatch ($old_ufmatch_mail moet worden: $new_ufmatch_mail).";
        $issues[]   = $message;
        wachthond($extdebug, 1, $message, "[DETECTIE]");
    }

    if ($need2update_extid == 1) {
        $status     = ($safe2update_extid == 1) ? "INFO: Wordt hersteld" : "STOP: Actie nodig";
        $message    = "$status: Civi External ID mismatch met Drupal UID ($crm_externalid moet worden: $valid_drupalid).";
        $issues[]   = $message;
        wachthond($extdebug, 1, $message, "[DETECTIE]");
    }

    if ($need2update_jobtitle == 1) {
        $status     = ($safe2update_jobtitle == 1) ? "INFO: Wordt hersteld" : "STOP: Actie nodig";
        $message    = "$status: Civi Gebruikersnaam wijkt af ($crm_drupalnaam moet worden: $user_name).";
        $issues[]   = $message;
        wachthond($extdebug, 1, $message, "[DETECTIE]");
    }

    wachthond($extdebug,3, "########################################################################");
    wachthond($extdebug,2, "### DRUPAL 5.2 - HARDE BLOKADES",                    "[SAFE2UPDATE == 0]");
    wachthond($extdebug,3, "########################################################################");

    if ($safe2update_account == 0) {
        if ($drupal_loadbyname_conflict == 1) {
            $message    = "BLOKKADE: Drupal account met zelfde naam bestaat al ($user_name)";
            $issues[]   = $message;
            wachthond($extdebug, 1, $message, "[SKIPPED]");
        }
        if ($rogue_ufmatch_found == 1) {
            $val        = $cid_ufmatch->id ?? $mail_ufmatch->id ?? 'onbekend';
            $message    = "BLOKKADE: Conflicterende UF Match gevonden (ID: $val)";
            $issues[]   = $message;
            wachthond($extdebug, 1, $message, "[SKIPPED]");
        }
        if ($crm_drupal_account_danger == 1) {
            if ($crm_drupal_account_name != $user_name) {
                $message    = "BLOKKADE: CMS Naam ($crm_drupal_account_name) != Verwachte Naam ($user_name)";
                $issues[]   = $message;
                wachthond($extdebug, 1, $message, "[SKIPPED]");
            }
        }
    }

    wachthond($extdebug,3, "########################################################################");
    wachthond($extdebug,2, "### DRUPAL 5.3 - SYSTEM INTEGRITY & EMAIL COLLISIONS");
    wachthond($extdebug,3, "########################################################################");

    if ($need2update_drupal_mail == 1 && $safe2update_drupal_mail == 0) {
        $message    = "BLOKKADE: '$user_mail' is al in gebruik bij Drupal-account: $other_uid ($other_name)";
        $issues[]   = $message;
        wachthond($extdebug, 1, $message, "[SKIPPED]");
    }

    if ($rogue_cid_ufmatch == 1) {
        $message    = "SYSTEEMFOUT: UID mismatch in UFMatch tabel (Gekoppeld aan UID " . ($cid_ufmatch->ufid ?? '??') . ")";
        $actual_uid = $cid_ufmatch->uf_id ?? 'onbekend'; // API4 gebruikt vaak uf_id in de koppeltabel
        $message    = "SYSTEEMFOUT: CID $contact_id heeft UFMatch met UID $actual_uid, maar Drupal verwacht UID " . ($drupal_uid ?? '??') . ".";
        $issues[]   = $message;
        wachthond($extdebug, 1, $message, "[DETECTIE]");
    }

    if ($drupal_loadbyname_orphan == 1) {
        $found_uid  = $account_by_name->id() ?? 'onbekend';
        $message    = "SYSTEEMFOUT: Orphan gedetecteerd. Drupal account '$user_name' (UID: $found_uid) bestaat, maar zonder CiviCRM koppeling.";
        $issues[]   = $message;
        wachthond($extdebug, 1, $message, "[DETECTIE]");
    }

    wachthond($extdebug,3, "########################################################################");
    wachthond($extdebug,2, "### DRUPAL 5.4 - SENDING REPORT TO CIVICRM");
    wachthond($extdebug,3, "########################################################################");

    if (!empty($issues)) {
        $rapport = "<b>Systeem-diagnose op " . date('d-m-Y H:i') . "</b>\n\n";
        foreach ($issues as $issue) {
            $rapport .= "• " . $issue . "\n";
        }
        $rapport .= "\n<hr><b>Details voor herstel</b>\n";
        $rapport .= "- Civi Naam: $displayname\n";
        $rapport .= "- Doel Username: $user_name\n";
        $rapport .= "- Doel Mail: $user_mail\n";
        $rapport .= "- Civi CID: $contact_id\n";
        $rapport .= "- Civi Ext ID: $crm_externalid\n";
        $rapport .= "- Drupal UID: " . ($crm_drupal_account_id ?? 'geen') . "\n";
        $rapport .= "- Leeftijd: " . ($leeftijd_nextkamp ?? '-');        

        // 1. Eerst de \n omzetten naar <br />
        $rapport_html = nl2br($rapport);
        // 2. Eventuele dubbele <br /> tags opschonen naar een enkele
        $rapport_html = preg_replace('/(<br\s*\/?>\s*){2,}/', '<br />', $rapport_html);        

        // GEBRUIK nl2br() om de tekst leesbaar te maken in CiviCRM HTML
        add_activity($contact_id, "Sync Problemen", nl2br($rapport));
        
        wachthond($extdebug, 1, "Activity 159 (D7:) bijgewerkt met " . count($issues) . " punten.");
    }

    wachthond($extdebug,3, "########################################################################");
    wachthond($extdebug,2, "### DRUPAL 6.X CREATE DRUPAL ACCOUNT WHEN NOT FOUND", "[ACCOUNT: $drupal_loadbyname_found / UFMATCH: $drupalufmatch_found]");
    wachthond($extdebug,3, "########################################################################");
    wachthond($extdebug,2, "NEED2  CREATE DRUPALACCOUNT",   "$need2create_account [1]");
    wachthond($extdebug,2, "SAFE2  CREATE DRUPALACCOUNT",   "$safe2create_account [1]");
    wachthond($extdebug,3, "########################################################################");
    wachthond($extdebug,3, "VALID  DRUPALID",               $valid_drupalid);
    wachthond($extdebug,3, "VALID  UFMATCH",                $valid_ufmatchid);
    wachthond($extdebug,3, "VALID  USER_NAME",              $user_name);
    wachthond($extdebug,3, "VALID  USER_MAIL",              $user_mail);
    wachthond($extdebug,3, "########################################################################");

    wachthond($extdebug,3, "########################################################################");
    wachthond($extdebug,2, "### DRUPAL 6.1 CREATE DRUPAL ACCOUNT [IF NOT FOUND]",    "[$drupal_loadbyname_found]");
    wachthond($extdebug,3, "########################################################################");

    $newaccount_drupalid = NULL;

    if ($need2create_account == 1 AND $safe2create_account == 1) {

        // 1. Controleer of de naam écht niet bestaat om Duplicate Entry te voorkomen
        $account_check = db_query("SELECT uid FROM {users} WHERE name = :name", array(':name' => $user_name))->fetchField();
        
        if ($account_check) {
            // Gebruiker bestaat al, we pakken het bestaande ID
            wachthond($extdebug, 1, "CRASH VOORKOMEN: Gebruikersnaam '$user_name' bestond al (UID: $account_check). We koppelen in plaats van maken.");
            $newaccount_drupalid = $account_check;
        } else {
            // De naam is echt vrij, we kunnen veilig INSERTEN
            $params_newaccount = array(
                'name'   => $user_name,
                'pass'   => $user_pwd,
                'mail'   => $email_plac_email,
                'status' => 1,
                'init'   => $email_plac_email,
                'roles'  => array(
                    DRUPAL_AUTHENTICATED_RID => 'authenticated user',
                    11 => 'custom role',
                ),
            );

            wachthond($extdebug, 2, "Drupal user create params", $params_newaccount);

            if ($user_name AND $user_pwd AND $email_plac_email) {
                // Maak de nieuwe user aan - SLECHTS ÉÉN KEER AANROEPEN
                $newaccount = user_save(NULL, $params_newaccount);
                $newaccount_drupalid = $newaccount->uid;
                wachthond($extdebug, 1, "SUCCESS: NIEUWE DRUPAL USER AANGEMAAKT: $user_name (UID: $newaccount_drupalid)");
            }
        }

        // 2. Als we een ID hebben (nieuw of gevonden), zet de vlaggen goed voor de UFMatch
        if ($newaccount_drupalid > 0) {
            $valid_drupalid           = $newaccount_drupalid;
            $need2update_account      = 1;
            $need2update_account_mail = 1;
            $safe2update_account      = 1;
            $safe2update_account_mail = 1;
            $need2create_ufmatch      = 1; 
            $safe2create_ufmatch      = 1;

            wachthond($extdebug, 2, "Vlaggen gezet voor koppeling (UID: $newaccount_drupalid)");
        } else {
            wachthond($extdebug, 1, "SKIPPED CREATE DRUPAL ACCOUNT: Geen valid ID verkregen [username: $user_name]");
        }

    } elseif ($need2create_account != 1) {
        wachthond($extdebug, 1, "SKIPPED CREATE DRUPAL ACCOUNT", "[AL OK]");
    } elseif ($safe2create_account != 1) {
        wachthond($extdebug, 1, "SKIPPED CREATE DRUPAL ACCOUNT", "[NOT SAFE]");
    }

    wachthond($extdebug,3, "########################################################################");
    wachthond($extdebug,2, "### DRUPAL 6.2 VERWIJDER UFMATCH (WANT WIJST NU NAAR LEGE CIVICRM ACCOUNT");
    wachthond($extdebug,3, "########################################################################");

    // M61: TODO: kan deze ufmatch misschien ook updaten naar juiste info?

    if ($newaccount_drupalid > 0) {

        $check_ufmatch      = NULL;
        $check_ufmatch      = find_ufmatch('drupalid', $newaccount_drupalid) ?? NULL;
        wachthond($extdebug,4, "check_ufmatch", $check_ufmatch);

        if ($check_ufmatch->id > 0) {

            $params_ufmatchdelete = [
                'id' => $check_ufmatch->id,
            ];
            wachthond($extdebug,7, 'params_ufmatchdelete',              $params_ufmatchdelete);
            $result_ufmatchdelete = civicrm_api3('UFMatch','delete',    $params_ufmatchdelete);
            wachthond($extdebug,9, 'result_newaccount_ufmatchdelete',   $result_ufmatchdelete);

            wachthond($extdebug,2, "EMPTY UFMATCH DELETED", "UFMATCHID: $check_ufmatch->id");

            $need2create_ufmatch    = 1;
            $safe2create_ufmatch    = 1;
        }
    }

    wachthond($extdebug,3, "########################################################################");
    wachthond($extdebug,2, "### DRUPAL 6.3 CREATE DRUPAL UF_MATCH WHEN NOT FOUND", "[AND DRUPALACCOUNT FOUND ($drupal_loadbyname_found)]");
    wachthond($extdebug,3, "########################################################################");
    wachthond($extdebug,2, "NEED2  CREATE UFMATCH",         "$need2create_ufmatch [1]");
    wachthond($extdebug,2, "SAFE2  CREATE UFMATCH",         "$safe2create_ufmatch [1]");
    wachthond($extdebug,3, "########################################################################");
    wachthond($extdebug,3, "VALID  DRUPALID",               $valid_drupalid);
    wachthond($extdebug,3, "VALID  UFMATCH",                $valid_ufmatchid);
    wachthond($extdebug,3, "VALID  USER_NAME",              $user_name);
    wachthond($extdebug,3, "VALID  USER_MAIL",              $user_mail);
    wachthond($extdebug,3, "########################################################################");

    if ($need2create_ufmatch == 1 AND $safe2create_ufmatch == 1) {

        ##########################################################################################
        # CREATE NEW UF MATCH WITH EXISTING DRUPAL USER
        // M61: veroorzaakt evt koppeling met verkeerd gevonden drupal account !!!
        ##########################################################################################

        $params_ufmatchcreate = [
            'checkPermissions' => FALSE,
            'debug' => $apidebug,
            'values' => [
                'uf_id'         => $valid_drupalid,
//              'uf_name'       => $valid_usermail,
                'uf_name'       => $email_plac_email,
                'contact_id'    => $contact_id,
            ],
        ];

        wachthond($extdebug,3, 'params_ufmatchcreate',                  $params_ufmatchcreate);

        if ($valid_drupalid > 0 AND $user_mail AND $contact_id) {

            $result_ufmatchcreate   = civicrm_api4('UFMatch','create',  $params_ufmatchcreate);
            wachthond($extdebug,9, 'result_ufmatchcreate',              $result_ufmatchcreate);
            $valid_ufmatchid        = $result_ufmatchcreate[0]['id'];
            wachthond($extdebug,2, "NEW UFMATCH CREATED",               "UFMATCHID: $valid_ufmatchid");

            $need2update_ufmatch_mail   = 1;
            $safe2update_ufmatch_mail   = 1;

        } else {
            wachthond($extdebug,3, 'valid_drupalid',                    $valid_drupalid);
            wachthond($extdebug,3, 'user_mail',                         $user_mail);
            wachthond($extdebug,3, 'contact_id',                        $contact_id);
        }

    } elseif ($need2create_ufmatch != 1) {
        wachthond($extdebug,1, "SKIPPED CREATE UFMATCH", "[AL OK]");
    } elseif ($safe2create_ufmatch != 1) {
        wachthond($extdebug,1, "SKIPPED CREATE UFMATCH", "[NOT SAFE]");
    }       

    wachthond($extdebug,3, "########################################################################");
    wachthond($extdebug,2, "### DRUPAL 6.4 CHECK IF A CURRENT UF MATCH IS NOW PRESENT FOR $displayname");
    wachthond($extdebug,3, "########################################################################");

    $check_ufmatch          = NULL;
    $check_ufmatch          = find_ufmatch('contactid', $contact_id) ?? NULL;
    $check_ufmatch_found    = (is_object($check_ufmatch) == true ?  1 : 0 );
    wachthond($extdebug,3, "check_ufmatch",                 $check_ufmatch);
    wachthond($extdebug,3, "check_ufmatch_found",           $check_ufmatch_found);

    if ($check_ufmatch_found == 1) {

        $diag_ufmatch           = diag_ufmatch('contactid', $contact_id, $check_ufmatch, $user_name, $crm_drupalnaam, $crm_externalid);
        $diag_ufmatch_found     = (is_object($diag_ufmatch) == true ?  1 : 0 );
        wachthond($extdebug,2, "diag_ufmatch",              $diag_ufmatch);
        wachthond($extdebug,3, "diag_ufmatch_found",        $diag_ufmatch_found);

        $need2update_extid      = $diag_ufmatch->need2_update_extid;
        $need2update_jobtitle   = $diag_ufmatch->need2_update_jobtitle;
        wachthond($extdebug,2, "need2update_extid",         $need2update_extid);
        wachthond($extdebug,2, "need2update_jobtitle",      $need2update_jobtitle);

        $safe2update_extid      = $diag_ufmatch->safe2_update_extid;
        $safe2update_jobtitle   = $diag_ufmatch->safe2_update_jobtitle;
        wachthond($extdebug,2, "safe2update_extid",         $safe2update_extid);
        wachthond($extdebug,2, "safe2update_jobtitle",      $safe2update_jobtitle);

        // M61: TODO: HIER NOG MEER OBV DE DIAG VD UFMATCH
        if ($diag_ufmatch_found == 1 AND $diag_ufmatch->cid == $contact_id) {
            wachthond($extdebug,2, "FIND UF_MATCH VOOR CONTACT_ID $contact_id", "PRIMA! [username: $diag_ufmatch->name]");

            $need2update_ufmatch    = 1;
            $safe2update_ufmatch    = 1;
        }

        // M61: DEZE 2 REGELS HIERONDER ZIJN EEN TIJDELIJKE OVERRULE. ZOU UIT DIAG_UFMATCH MOETEN KOMEN

//          $need2update_extid = 1;
//          $safe2update_extid = 1;

//  if ($valid_drupalid > 0 AND $safe2update_account == 1 AND $need2update_extid == 1 AND $safe2update_extid == 1)  {

        if ($valid_drupalid > 0 AND $need2update_extid == 1 AND $safe2update_extid == 1)    {
            $params_contact['values']['external_identifier']    = $valid_drupalid;
            $contact_values['external_identifier']              = $valid_drupalid;
            wachthond($extdebug,2, "DOE UPDATE VAN EXTERNAL ID (CMSEXTID) MET DRUPALID", $valid_drupalid);
        } else {

            wachthond($extdebug,2, "valid_drupalid",            $valid_drupalid);
            wachthond($extdebug,2, "safe2update_account",       $safe2update_account);
            wachthond($extdebug,2, "need2update_extid",         $need2update_extid);
            wachthond($extdebug,2, "safe2update_extid",         $safe2update_extid);

            wachthond($extdebug,2, "DOE geen UPDATE VAN EXTERNAL ID (CMSEXTID) MET DRUPALID", $valid_drupalid);             
        }

    } else {
        wachthond($extdebug,2, "FIND UF_MATCH VOOR CONTACT_ID $contact_id",     "ERROR! [NO UFMATCH FOUND]");

        $drupalufmatch_found    = 0;
    }

    wachthond($extdebug,3, "########################################################################");
    wachthond($extdebug,3, "### DRUPAL 7.X UPDATE DRUPAL ACCOUNT & UF_MATCH",             "[IF AVAILABLE]");

    wachthond($extdebug,3, "########################################################################");
    wachthond($extdebug,2, "### DRUPAL 7.1 UPDATE DRUPAL ACCOUNT WITH PROPER EMAIL & USERNAME");
    wachthond($extdebug,3, "########################################################################");
    wachthond($extdebug,3, "NEED2  UPDATE DRUPALACCOUNT",   "$need2update_account");
#   wachthond($extdebug,3, "NEED2  UPDATE UFMATCH",         "$need2create_ufmatch");
    wachthond($extdebug,2, "SAFE2  UPDATE DRUPALACCOUNT",   "$safe2update_account [1]");
#   wachthond($extdebug,3, "SAFE2  UPDATE UFMATCH",         "$safe2create_ufmatch");
    wachthond($extdebug,2, "NEED2  UPDATE DRUPAL_MAIL",     "$need2update_drupal_mail");
    wachthond($extdebug,2, "SAFE2  UPDATE DRUPAL_MAIL",     "$safe2update_drupal_mail [1]");
    wachthond($extdebug,2, "NEED2  UPDATE DRUPAL_NAME",     $need2update_drupal_name);
    wachthond($extdebug,2, "SAFE2  UPDATE DRUPAL_NAME",     $safe2update_drupal_name);
    wachthond($extdebug,3, "########################################################################");
    wachthond($extdebug,3, "VALID  DRUPALID",               $valid_drupalid);
#   wachthond($extdebug,3, "VALID  UFMATCH",                $valid_ufmatchid);
#   wachthond($extdebug,3, "       USER_NAME",              $user_name);
#   wachthond($extdebug,3, "       USER_MAIL",              $user_mail);
    wachthond($extdebug,3, "VALID  USER_NAME",              $valid_username);
    wachthond($extdebug,3, "VALID  USER_MAIL",              $valid_usermail);   
    wachthond($extdebug,3, "########################################################################");

    wachthond($extdebug,2, "mail_notinany_ufmatch",         $mail_notinany_ufmatch);
    wachthond($extdebug,2, "mail_notinany_cmsaccount",      $mail_notinany_cmsaccount);

    $existinguser = user_load($valid_drupalid);
    wachthond($extdebug,3, "existinguser_uid",  $existinguser->uid);
    wachthond($extdebug,2, "existinguser",      $existinguser);

    wachthond($extdebug,3, 'email_home_email',              $email_home_email);
    wachthond($extdebug,3, 'email_plac_email',              $email_plac_email);
    wachthond($extdebug,3, 'email_onvr_email',              $email_onvr_email);

    wachthond($extdebug,3, 'valid_usermail',                $valid_usermail);

wachthond($extdebug,3, "########################################################################");
    wachthond($extdebug,2, "### DRUPAL 7.2, 7.3 & 7.4 (OPTIMALIZED SINGLE SAVE)");
    wachthond($extdebug,3, "########################################################################");

    if ($existinguser->uid > 0) {

        $edit_fields = [];      // De wachtrij voor wijzigingen
        $needs_save  = false;   // Vlaggetje: moeten we de database lastigvallen?

        wachthond($extdebug,3, "########################################################################");
        wachthond($extdebug,2, "### DRUPAL 7.2 CHECK DRUPAL NAME");
        wachthond($extdebug,3, "########################################################################");

        if ($need2update_drupal_name == 1 AND $safe2update_drupal_name == 1) {

            $existinguser_name_org  = $existinguser->name;
            $existinguser_name_new  = $valid_username;

            wachthond($extdebug,3, "existinguser_name_org",     $existinguser_name_org);
            wachthond($extdebug,3, "existinguser_name_new",     $existinguser_name_new);

            if ($existinguser_name_org != $existinguser_name_new) {
                // Zet klaar in de wachtrij
                $edit_fields['name'] = $existinguser_name_new;
                $needs_save = true;

                wachthond($extdebug,1,  "QUEUE: VOOR UID $valid_drupalid NAME CHANGE", 
                                        "FROM '$existinguser_name_org' TO '$existinguser_name_new'");
            } else {
                wachthond($extdebug,1, "SKIPPED UPDATE DRUPAL NAME",  "[AL OK]");
            }
        } else {
            wachthond($extdebug,2, "SKIPPED NAME CHECK (FLAGS OFF)", "$need2update_drupal_name / $safe2update_drupal_name");
        }

        wachthond($extdebug,3, "########################################################################");
        wachthond($extdebug,2, "### DRUPAL 7.3 CHECK DRUPAL MAIL");
        wachthond($extdebug,3, "########################################################################");

        if ($need2update_drupal_mail == 1 AND $safe2update_drupal_mail == 1) {

            $existinguser_mail_org  = $existinguser->mail;
            $existinguser_mail_new  = $valid_usermail;

            wachthond($extdebug,3, "existinguser_mail_org",     $existinguser_mail_org);
            wachthond($extdebug,3, "existinguser_mail_new",     $existinguser_mail_new);

            if ($existinguser_mail_org != $existinguser_mail_new) {
                // Zet klaar in de wachtrij (inclusief init veld)
                $edit_fields['mail'] = $existinguser_mail_new;
                $edit_fields['init'] = $existinguser_mail_new; 
                $needs_save = true;

                wachthond($extdebug,1,  "QUEUE: VOOR UID $valid_drupalid MAIL CHANGE",
                                        "FROM '$existinguser_mail_org' TO '$existinguser_mail_new'");
            } else {
                wachthond($extdebug,1, "SKIPPED UPDATE DRUPAL MAIL",  "[AL OK]");
            }
        }

        wachthond($extdebug,3, "########################################################################");
        wachthond($extdebug,2, "### DRUPAL 7.4 CHECK DRUPAL ROLES");
        wachthond($extdebug,3, "########################################################################");

        if ($need2update_account == 1 AND $safe2update_account == 1) {
            
            // We werken met een kopie van de huidige rollen om te rekenen
            $current_roles = $existinguser->roles;
            $new_roles     = $current_roles; 

            // --- ROL 1: DITJAAR_ALLELEIDING ---
            $role_obj = user_role_load_by_name("ditjaar_alleleiding");
            if ($role_obj) {
                $rid = $role_obj->rid;
                if ($ditjaarleidyes == 1) {
                    if (!isset($new_roles[$rid])) {
                        $new_roles[$rid] = $role_obj->name;
                        wachthond($extdebug, 2, "Role Queue", "+ ditjaar_alleleiding");
                    }
                } else {
                    if (isset($new_roles[$rid])) {
                        unset($new_roles[$rid]);
                        wachthond($extdebug, 2, "Role Queue", "- ditjaar_alleleiding");
                    }
                }
            }

            // --- ROL 2: DITJAAR_KAMPSTAF ---
            $role_obj = user_role_load_by_name("ditjaar_kampstaf");
            if ($role_obj) {
                $rid = $role_obj->rid;
                if ($ditjaarstafyes == 1) {
                    if (!isset($new_roles[$rid])) {
                        $new_roles[$rid] = $role_obj->name;
                        wachthond($extdebug, 2, "Role Queue", "+ ditjaar_kampstaf");
                    }
                } else {
                    if (isset($new_roles[$rid])) {
                        unset($new_roles[$rid]);
                        wachthond($extdebug, 2, "Role Queue", "- ditjaar_kampstaf");
                    }
                }
            }

            // --- ROL 3: DITJAAR_HOOFDLEIDING ---
            $role_obj = user_role_load_by_name("ditjaar_hoofdleiding");
            if ($role_obj) {
                $rid = $role_obj->rid;
                if ($ditjaarhoofdyes == 1) {
                    if (!isset($new_roles[$rid])) {
                        $new_roles[$rid] = $role_obj->name;
                        wachthond($extdebug, 2, "Role Queue", "+ ditjaar_hoofdleiding");
                    }
                } else {
                    if (isset($new_roles[$rid])) {
                        unset($new_roles[$rid]);
                        wachthond($extdebug, 2, "Role Queue", "- ditjaar_hoofdleiding");
                    }
                }
            }

            // --- VERGELIJKING ---
            // Is er daadwerkelijk iets veranderd in de rollen-array?
            $diff1 = array_diff_key($current_roles, $new_roles);
            $diff2 = array_diff_key($new_roles, $current_roles);

            if (!empty($diff1) || !empty($diff2)) {
                $edit_fields['roles'] = $new_roles;
                $needs_save = true;
                wachthond($extdebug, 1, "QUEUE: ROL WIJZIGINGEN GEDETECTEERD", "Wordt meegenomen in save.");
            } else {
                wachthond($extdebug, 1, "SKIPPED UPDATE DRUPAL ROLES", "[AL OK]");
            }
        }

        wachthond($extdebug,3, "########################################################################");
        wachthond($extdebug,2, "### DRUPAL FINAL SAVE (SINGLE TRANSACTION)");
        wachthond($extdebug,3, "########################################################################");

        if ($needs_save) {
            $changed_keys = implode(', ', array_keys($edit_fields));
            wachthond($extdebug, 1, "EXECUTING SINGLE USER SAVE", "Fields: $changed_keys");
            
            // Drupal 7 user_save($account, $edit) -> Zeer efficiënt
            user_save($existinguser, $edit_fields);
            
            // Werk het lokale object bij voor de rest van het script
            foreach($edit_fields as $key => $val) {
                $existinguser->$key = $val;
            }
            
            wachthond($extdebug, 1, "SUCCESS: GEGEVENS EN ROLLEN OPGESLAGEN VOOR UID $valid_drupalid");

        } else {
            wachthond($extdebug, 1, "NO CHANGES DETECTED", "DB Save skipped completely (0.00s)");
        }

    } else {
        wachthond($extdebug,2, "SKIPPED 7.X UPDATE", "[NO VALID UID]");
    }

    wachthond($extdebug,3, "########################################################################");
    wachthond($extdebug,2, "### DRUPAL 8.X - UPDATE UF_MATCH WITH THE PROPER DRUPALID & EMAIL");
    wachthond($extdebug,3, "########################################################################");
    wachthond($extdebug,2, "NEED2  UPDATE UFMATCH MAIL",    "$need2update_ufmatch_mail");
    wachthond($extdebug,2, "SAFE2  UPDATE UFMATCH MAIL",    "$safe2update_ufmatch_mail [1]");
    wachthond($extdebug,2, "NEED2  UPDATE UFMATCH UFID",    "$need2update_ufmatch_ufid");
    wachthond($extdebug,2, "SAFE2  UPDATE UFMATCH UFID",    "$safe2update_ufmatch_ufid [1]");
    wachthond($extdebug,3, "########################################################################");
    wachthond($extdebug,3, "VALID  DRUPALID",               $valid_drupalid);
    wachthond($extdebug,3, "VALID  UFMATCH",                $valid_ufmatchid);
    wachthond($extdebug,3, "VALID  USER_NAME",              $user_name);
    wachthond($extdebug,3, "VALID  USER_MAIL",              $user_mail);
    wachthond($extdebug,3, "########################################################################");

    wachthond($extdebug,3, 'email_home_email',              $email_home_email);
    wachthond($extdebug,3, 'email_plac_email',              $email_plac_email);
    wachthond($extdebug,3, 'email_onvr_email',              $email_onvr_email);

    wachthond($extdebug,3, "########################################################################");
    wachthond($extdebug,2, "### DRUPAL 8.1 - UPDATE UFMATCH MAIL",                  "$valid_usermail");
    wachthond($extdebug,3, "########################################################################");

    $old_ufmatch_ufid = $check_ufmatch->ufid;
    $new_ufmatch_ufid = $valid_drupalid;
    $old_ufmatch_mail = $check_ufmatch->name;
    $new_ufmatch_mail = $valid_usermail;

    // We verzamelen alleen de waarden die door de 'safe' check zijn gekomen
    $update_values = [];

    // A. Check Mail wijziging
    if ($need2update_ufmatch_mail == 1 && $safe2update_ufmatch_mail == 1) {
        
        // 1. Eerst de "dirty" update (reset) om de trema-cache in CiviCRM te breken
        // Dit doen we alleen als we ook echt de mail gaan updaten.
        civicrm_api4('UFMatch', 'update', [
            'checkPermissions' => false,
            'where' => [['id', '=', $valid_ufmatchid]],
            'values' => ['uf_name' => 'reset.' . time() . '@placeholder.nl'],
        ]);
        
        $update_values['uf_name'] = $new_ufmatch_mail;
        wachthond($extdebug, 1, "ACTION: UF_NAME wordt bijgewerkt naar $new_ufmatch_mail");
    }
    wachthond($extdebug,3, "########################################################################");
    wachthond($extdebug,2, "### DRUPAL 8.2 - UPDATE UFMATCH UFID",                "$new_ufmatch_ufid");
    wachthond($extdebug,3, "########################################################################");

    // B. Check UID wijziging
    if ($need2update_ufmatch_ufid == 1 && $safe2update_ufmatch_ufid == 1) {
        $update_values['uf_id'] = $new_ufmatch_ufid;
        wachthond($extdebug, 1, "ACTION: UF_ID wordt bijgewerkt naar $new_ufmatch_ufid");
    }

    wachthond($extdebug,3, "########################################################################");
    wachthond($extdebug,2, "### DRUPAL 8.3 - UPDATE UFMATCH",                     "$new_ufmatch_ufid");
    wachthond($extdebug,3, "########################################################################");

    // --- Sectie 8.3: UPDATE UFMATCH & PROTECT PRIMARY ---
    if (!empty($update_values)) {
        
        // STAP A: Update de UFMatch tabel (Koppeling Drupal UID <-> Civi CID)
        try {
            civicrm_api4('UFMatch', 'update', [
                'checkPermissions' => FALSE,
                'where' => [['id', '=', $valid_ufmatchid]],
                'values' => $update_values,
            ]);
            wachthond($extdebug, 1, "SUCCESS: UFMatch bijgewerkt voor contact $contact_id");
        } catch (\Exception $e) {
            wachthond($extdebug, 1, "ERROR: UFMatch update mislukt: " . $e->getMessage());
        }

        // STAP B: Bescherm de primary status van oud1
        // Als we een placeholder mail gebruiken voor het Drupal account, 
        // forceren we dat dit record in Civi NIET de primary wordt.
        if ($valid_usermail == ($email_plac_email ?? 'no-placeholder-match')) {
            try {
                civicrm_api4('Email', 'update', [
                    'checkPermissions' => FALSE,
                    'where' => [
                        ['contact_id',       '=', $contact_id],
                        ['location_type_id', '=', 4], // 4 = 'Other' (Locatie van placeholder)
                    ],
                    'values' => [
                        'is_primary' => FALSE,
                    ],
                ]);
                wachthond($extdebug, 1, "PROTECTION: Placeholder mail geforceerd op NIET-primair.");
            } catch (\Exception $e) {
                // Soms bestaat record 4 nog niet, dat is geen ramp
                wachthond($extdebug, 3, "INFO: Geen Location 4 record gevonden om te demoten.");
            }
        }
        
        // STAP C: Synchronisatie verwijderd. 
        // We doen geen CRM_Core_BAO_UFMatch::synchronize meer omdat deze 
        // de neiging heeft om oud1 van zijn primary status te stoten.
        
    } else {
        wachthond($extdebug, 2, "SKIP: Geen wijzigingen nodig voor UFMatch ID $valid_ufmatchid");
    }
/*
    wachthond($extdebug,3, "########################################################################");
    wachthond($extdebug,2, "### DRUPAL 8.3 - UPDATE UFMATCH",                     "$new_ufmatch_ufid");
    wachthond($extdebug,3, "########################################################################");

    // C. De daadwerkelijke API call: alleen uitvoeren als er iets in de 'safe' lijst staat
    if (!empty($update_values)) {
        
        $params_ufmatchupdate = [
            'checkPermissions' => FALSE,
            'debug' => $apidebug,
            'where' => [['id', '=', $valid_ufmatchid]],
            'values' => $update_values,
        ];

        $result_ufmatchupdate = civicrm_api4('UFMatch', 'update', $params_ufmatchupdate);

        if ($old_ufmatch_ufid != $new_ufmatch_ufid) {
            wachthond($extdebug,1,  "SUCCESS: CHANGED UFMATCH UFID FOR CID ($contact_id)", 
                                    "FROM $old_ufmatch_ufid TO $new_ufmatch_ufid");
        }
        if ($old_ufmatch_mail != $new_ufmatch_mail) {
            wachthond($extdebug,1,  "SUCCESS: CHANGED UFMATCH MAIL FOR CID ($contact_id)", 
                                    "FROM $old_ufmatch_mail TO $new_ufmatch_mail");
        }

        // Optioneel: Forceer CiviCRM om de interne cache nu echt te verversen
        if (function_exists('civicrm_api4')) {
            // Forceer een herberekening van de synchronisatie
            civicrm_api4('UFMatch', 'get', [
              'checkPermissions' => false,
              'where' => [['contact_id', '=', $contact_id]],
            ]);
            
            // Maak expliciet een variabele aan voor het eerste argument
            $user_placeholder = NULL;

            // Roep de synchronisatie aan met de variabele in plaats van direct NULL
            CRM_Core_BAO_UFMatch::synchronize(
                $user_placeholder, // Dit kan nu veilig "by reference" worden doorgegeven
                false,             // $isTrigger (meestal false bij handmatige aanroep)
                'Drupal',          // $cmsName
                'CMS_User',        // $contactType
                true               // $force (dwingt synchronisatie af)
            );
        }
        
    } else {
        wachthond($extdebug, 2, "SKIP: Geen veilige updates gevonden voor UFMatch ID $valid_ufmatchid");
    }
*/
    wachthond($extdebug,3, "########################################################################");
    wachthond($extdebug,2, "### DRUPAL 8.4 - UPDATE EXTID ($valid_drupalid) / JOBTITLE ($valid_username)");
    wachthond($extdebug,3, "########################################################################");

    if (!empty($contact_values)) {
        
        $params_contact = [
            'checkPermissions' => FALSE,
            'debug'     => $apidebug,
            'where'     => [['id', '=', $contact_id]],
            'values'    => $contact_values,
        ];

        wachthond($extdebug,3, 'params_contact',           $params_contact);
        $result_contact = civicrm_api4('Contact', 'update', $params_contact);
        wachthond($extdebug, 9, "SUCCESS: Contact $contact_id bijgewerkt.", $result_contact);
        
    } else {
        wachthond($extdebug, 2, "SKIP: Geen veilige wijzigingen voor Contact ID $contact_id (Job Title of ExtID).");
    }

    wachthond($extdebug,3, "########################################################################");
    wachthond($extdebug,2, "### DRUPAL 8.5 - AUTO-CLEANUP (Sluit activiteit als alles nu OK is)");
    wachthond($extdebug,3, "########################################################################");

    wachthond($extdebug,2, "NEED2  UPDATE EXTID",           $need2update_extid);
    wachthond($extdebug,2, "NEED2  UPDATE JOBTITLE",        $need2update_jobtitle);
    wachthond($extdebug,2, "NEED2  UPDATE UFMATCH MAIL",    $need2update_ufmatch_mail);
    wachthond($extdebug,2, "NEED2  UPDATE UFMATCH UFID",    $need2update_ufmatch_ufid);

    // Bereken of er nog openstaande herstelacties nodig zijn
    $pending_issues = $need2update_extid + $need2update_jobtitle + $need2update_ufmatch_ufid + $need2update_ufmatch_mail;

    if ($pending_issues == 0) {
        
        // 1. Zoek openstaande activiteiten (Scheduled) van type 159
        $params_get_act = [
            'checkPermissions' => false,
            'select'    => ['id', 'subject'],
            'where'     => [
                ['activity_type_id',    '=', 159],
                ['status_id:name',      '=', 'Scheduled'],
                ['target_contact_id',   'CONTAINS', $contact_id],
                ['subject',             'LIKE', 'D7:%'],
            ],
        ];

        wachthond($extdebug, 7, 'params_get_act',       $params_get_act);
        $result_get_act = civicrm_api4('Activity','get',$params_get_act);
        wachthond($extdebug, 9, 'result_get_act',       $result_get_act);

        if (count($result_get_act) > 0) {
            foreach ($result_get_act as $activity) {
                
                // 2. Update de activiteit naar 'Completed'
                $params_update_act = [
                    'checkPermissions' => false,
                    'where' => [['id', '=', $activity['id']]],
                    'values' => [
                        'status_id:name' => 'Completed',
                        'subject'        => "[HERSTELD] " . $activity['subject'],
                        'location'       => "Hersteld op " . date('d-m-Y H:i') . ": Alle gedetecteerde mismatches zijn opgelost.",
                    ],
                ];

                $result_cleanup = civicrm_api4('Activity', 'update', $params_update_act);
                wachthond($extdebug, 1, "CLEANUP: Activiteit " . $activity['id'] . " op Voltooid gezet.", $result_cleanup);
            }
        }
    } else {
        wachthond($extdebug, 2, "CLEANUP: Er zijn nog $pending_issues openstaande punten. Melding blijft open.");
    }

    wachthond($extdebug,1, "########################################################################");
    wachthond($extdebug,1, "VALID  DRUPALID",               $valid_drupalid);
    wachthond($extdebug,1, "VALID  UFMATCH",                $valid_ufmatchid);
    wachthond($extdebug,1, "VALID  USER_NAME",              $user_name);
    wachthond($extdebug,1, "VALID  USER_MAIL",              $user_mail);
    wachthond($extdebug,1, "CURRENT DRUPAL NAME",           $cms_drupal_account_name);
    wachthond($extdebug,1, "CURRENT DRUPAL MAIL",           $cms_drupal_account_mail);

    wachthond($extdebug,1, "########################################################################");
    wachthond($extdebug,1, "### DRUPAL 8.X EINDE DRUPAL ACCOUNT INFO & REPAIR VOOR $displayname");
    wachthond($extdebug,1, "########################################################################");
}

function drupal_civicrm_username($contactid, $firstname = NULL, $middlename = NULL, $lastname = NULL, $displayname = NULL, $nickname = NULL) {

    $extdebug           = 0;          // 1 = basic // 2 = verbose // 3 = params / 4 = results
    $apidebug           = FALSE;

    $extdrupal          = 1;
    $extwrite           = 1;

    $contact_id         = $contactid;

    $org_first_name     = $firstname;
    $org_middle_name    = $middlename;
    $org_last_name      = $lastname;
    $org_displayname    = $displayname;
    $org_nick_name      = $nickname;

    wachthond($extdebug,2, "########################################################################");
    wachthond($extdebug,2, "### USERNAME - CONFIGURE PROPER (USER)NAMES",                $displayname);
    wachthond($extdebug,2, "########################################################################");

    wachthond($extdebug,3, "contact_id",                    $contact_id);

    $params_contact_update = [
//      'reload'            =>  TRUE,
        'checkPermissions'  =>  FALSE,
        'debug' => $apidebug,
        'where' => [
            ['id',         '=', $contact_id],
        ],
        'values' => [
            'id'            =>  $contact_id,
        ],
    ];

    wachthond($extdebug,2, "########################################################################");
    wachthond($extdebug,2, "### USERNAME 0.1 RETREIVE CURRENT VALUES",                   $displayname);
    wachthond($extdebug,2, "########################################################################");

    $params_contact_get = [
        'checkPermissions' => FALSE,
        'debug'  => $apidebug,        
        'select' => [
            'contact_type', 'contact_sub_type', 'first_name', 'middle_name', 'last_name', 'nick_name', 'display_name', 'external_identifier' 
        ],
        'where' => [
            ['id',        'IN', [$contact_id]],
        ],
    ];    

    wachthond($extdebug,3, 'params_contact_get',           $params_contact_get);
    $result_contact_get  = civicrm_api4('Contact','get',   $params_contact_get);
    wachthond($extdebug,3, 'result_contact_get',           $result_contact_get);

    $contact_type       = $result_contact_get[0]['contact_type'];
    $contact_subtype    = $result_contact_get[0]['contact_sub_type'];

    $first_name         = $result_contact_get[0]['first_name'];
    $middle_name        = $result_contact_get[0]['middle_name'];
    $last_name          = $result_contact_get[0]['last_name'];
    $nick_name          = $result_contact_get[0]['nick_name'];
    $displayname        = $result_contact_get[0]['display_name'];

    $crm_externalid     = $result_contact_get[0]['external_identifier'];

    wachthond($extdebug,3, 'contact_type',      $contact_type);
    wachthond($extdebug,3, 'contact_subtype',   $contact_subtype);

    wachthond($extdebug,3, 'contact_id',        $contact_id);
    wachthond($extdebug,3, 'first_name',        $first_name);
    wachthond($extdebug,3, 'middle_name',       $middle_name);
    wachthond($extdebug,3, 'last_name',         $last_name);
    wachthond($extdebug,3, 'nick_name',         $nick_name);
    wachthond($extdebug,3, 'displayname',       $displayname);
    wachthond($extdebug,3, 'crm_externalid',    $crm_externalid);

    $org_first_name     = $first_name;
    $org_middle_name    = $middle_name;
    $org_last_name      = $last_name;
    $org_nick_name      = $nick_name;
    $org_displayname    = $displayname;
    $org_crm_externalid = $crm_externalid;

    wachthond($extdebug,2, "########################################################################");
    wachthond($extdebug,2, "### USERNAME 1.1 CORRECT FIRST- / MIDDLE- / LASTNAME",       $displayname);
    wachthond($extdebug,2, "########################################################################");

    // An array of all prefixes, sorted from longest to shortest
    $prefixes = [
        'van der ',
        'van het ',
        'van de ',
        'ten ',
        'den ',
        'der ',
        'te ',
        'de ',
        'van '
    ];

    $last_name_lower    = strtolower(trim($last_name));
    $last_name_original = trim($last_name);

    // CHECK: Alleen prefixen uit de achternaam halen als middle_name nog leeg is
    if (empty($middle_name)) {
        foreach ($prefixes as $prefix) {
            if (str_starts_with($last_name_lower, $prefix)) {
                $middle_name    = strtolower(trim($prefix));
                $last_name      = trim(substr($last_name_original, strlen($prefix)));
                break; 
            }
        }
    } else {
        // middle_name was al bekend (bijv. "de"), 
        // we zorgen alleen dat last_name ook schoon is
        $last_name = $last_name_original;
    }

    // Clean up
    $last_name = trim($last_name);

/*
    #############################################################################################
    ### trim en lowercase naam
    #############################################################################################

    $first_name                 = strtolower(trim($first_name));
    $middle_name                = strtolower(trim($middle_name  ?? ''));
    $last_name                  = strtolower(trim($last_name));
    $nick_name                  = strtolower(trim($nick_name    ?? ''));

    #############################################################################################
    ### splits het tussenvoegsel als het ingevuld was bij de achternaam
    #############################################################################################

    // DE

    if (strtolower(substr($last_name, 0, 3)) == 'te ') {
        $middle_name            = 'te';
        $last_name              = substr($last_name, 4);
    }
    if (strtolower(substr($last_name, 0, 3)) == 'de ') {
        $middle_name            = 'de';
        $last_name              = substr($last_name, 4);
    }
    if (strtolower(substr($last_name, 0, 4)) == 'der ') {
        $middle_name            = 'der';
        $last_name              = substr($last_name, 5);
    }
    if (strtolower(substr($last_name, 0, 4)) == 'den ') {
        $middle_name            = 'den';
        $last_name              = substr($last_name, 5);
    }
    if (strtolower(substr($last_name, 0, 4)) == 'ten ') {
        $middle_name            = 'ten';
        $last_name              = substr($last_name, 5);
    }

    // VAN

    if (strtolower(substr($last_name, 0, 4)) == 'van ') {
        $middle_name            = 'van';
        $last_name              = substr($last_name, 3);
    }

    if (strtolower(substr($last_name, 0, 7)) == 'van de ') {
        $middle_name            = 'van de';
        $last_name              = substr($last_name, 7);
    }
    if (strtolower(substr($last_name, 0, 8)) == 'van der ') {
        $middle_name            = 'van der';
        $last_name              = substr($last_name, 8);
    }
    if (strtolower(substr($last_name, 0, 8)) == 'van het ') {
        $middle_name            = 'van het';
        $last_name              = substr($last_name, 8);
    }
*/
        #############################################################################################
        ### hoofdletter indien nodig (ook bij samengestelde namen met een streepje erin)
        #############################################################################################
/*
        $first_name     = implode('-', array_map('ucfirst', explode('-', $first_name)));
        $last_name      = implode('-', array_map('ucfirst', explode('-', $last_name)));
        $nick_name      = implode('-', array_map('ucfirst', explode('-', $nick_name)));
        $nick_name      = implode(' ', array_map('ucfirst', explode(' ', $nick_name)));
*/
        // Use ucwords to handle both hyphens and spaces in a single step
        $first_name     = ucwords(strtolower(trim($first_name)),    '- ');
        $last_name      = ucwords(strtolower(trim($last_name)),     '- ');
        $nick_name      = ucwords(strtolower(trim($nick_name)),     '- ');        

        # UPDATE civicrm_contact SET `middle_name` = REPLACE(`middle_name`, ' vd ', ' van der ');
        # UPDATE civicrm_contact SET `middle_name` = REPLACE(`middle_name`, ' vd ', ' van der ');
        # UPDATE civicrm_contact SET `middle_name` = REPLACE(`middle_name`, ' v/d ', ' van der ');
        # UPDATE civicrm_contact SET `middle_name` = REPLACE(`middle_name`, ' v.d. ', ' van der ');
        # UPDATE civicrm_contact SET `last_name`   = REPLACE(`last_name`, ' vd ', ' van der ');
        # UPDATE civicrm_contact SET `last_name`   = REPLACE(`last_name`, ' v/d ', ' van der ');
        # UPDATE civicrm_contact SET `last_name`   = REPLACE(`last_name`, ' v.d. ', ' van der ');
        # UPDATE civicrm_contact SET `nick_name`   = REPLACE(`nick_name`, ' vd ', ' van der ');
        # UPDATE civicrm_contact SET `nick_name`   = REPLACE(`nick_name`, ' v/d ', ' van der ');
        # UPDATE civicrm_contact SET `nick_name`   = REPLACE(`nick_name`, ' v.d. ', ' van der ');

        # UPDATE civicrm_contact SET last_name = REPLACE(last_name,' Ij',' IJ');
        # UPDATE civicrm_contact SET last_name = REPLACE(last_name,'-Ij','-IJ');
        # UPDATE civicrm_contact SET nick_name = REPLACE(nick_name,' Ij',' IJ');
        # UPDATE civicrm_contact SET nick_name = REPLACE(nick_name,'-Ij','-IJ');

        # UPDATE civicrm_value_gegevens_ouder_verzorger_26 SET achternaam_ouder2_59 = REPLACE(achternaam_ouder2_59,' -','-');
        # UPDATE civicrm_value_gegevens_ouder_verzorger_26 SET achternaam_ouder2_59 = REPLACE(achternaam_ouder2_59,'- ','-');
        # UPDATE civicrm_value_gegevens_ouder_verzorger_26 SET voornaam_ouder2_57   = REPLACE(voornaam_ouder2_57,' En ',' en ');

        # TODO: VIND MEISJESNAAM IN ACHTERNAAM EN SPLITS (NU IN SQL TASK)

        wachthond($extdebug,3, 'contact_id',        $contact_id);
        wachthond($extdebug,3, 'first_name',        $first_name);
        wachthond($extdebug,3, 'middle_name',       $middle_name);
        wachthond($extdebug,3, 'last_name',         $last_name);
        wachthond($extdebug,3, 'nick_name',         $nick_name);
        wachthond($extdebug,2, 'displayname',       $displayname);

        wachthond($extdebug,2, "########################################################################");
        wachthond($extdebug,2, "### USERNAME 1.2 CONSTRUCT USERNAME FROM FIRST & LASTNAME",  $displayname);
        wachthond($extdebug,2, "########################################################################");

        #############################################################################################
        ### CONSTRUCT THE USER_NAME
        #############################################################################################

        if ($first_name)    {   $firstname  = $first_name   ?? NULL;            }
        if ($middle_name)   {   $middlename = $middle_name  ?? NULL;            }
        if ($last_name)     {   $lastname   = $last_name    ?? NULL;            }
        if ($nick_name)     {   $nickname   = $nick_name    ?? NULL;            }

        if ($first_name)    {   wachthond($extdebug,3, "INITIAL VALUES");       }
        if ($firstname)     {   wachthond($extdebug,3, "0 FN", $firstname);     }
        if ($middlename)    {   wachthond($extdebug,3, "0 MN", $middlename);    }
        if ($lastname)      {   wachthond($extdebug,3, "0 LN", $lastname);      }
        if ($nickname)      {   wachthond($extdebug,3, "0 NN", $nickname);      }

        #############################################################################################
        // 1. REMOVE ACCENTS
        #############################################################################################

        if ($firstname)     {   $firstname  = transliterator_transliterate('NFKC; [:Nonspacing Mark:] Remove; Any-Latin; Latin-ASCII', $firstname); }
        if ($middlename)    {   $middlename = transliterator_transliterate('NFKC; [:Nonspacing Mark:] Remove; Any-Latin; Latin-ASCII', $middlename);}
        if ($lastname)      {   $lastname   = transliterator_transliterate('NFKC; [:Nonspacing Mark:] Remove; Any-Latin; Latin-ASCII', $lastname);  }
        if ($nickname)      {   $nickname   = transliterator_transliterate('NFKC; [:Nonspacing Mark:] Remove; Any-Latin; Latin-ASCII', $nickname);  }

        if ($firstname)     {   wachthond($extdebug,3, "A. TRANSLITERATE ACCENTS & SPECIAL ASCII CHARACTERS");      }
        if ($firstname)     {   wachthond($extdebug,3, "1 FN", $firstname)  ?? NULL;    }
        if ($middlename)    {   wachthond($extdebug,3, "1 MN", $middlename) ?? NULL;    }
        if ($lastname)      {   wachthond($extdebug,3, "1 LN", $lastname)   ?? NULL;    }
        if ($nickname)      {   wachthond($extdebug,3, "1 NN", $nickname)   ?? NULL;    }

        #############################################################################################
        // 2. KEEP ONLY LETTERS & NUMBERS & DASHES + STRTOLOWER
        // ^   = beginning of the line
        // \w- = match a "word" character (alphanumeric plus "_")
        #############################################################################################

        if ($firstname)     {   $firstname      = preg_replace('/[^ \w-]/','',strtolower(trim($firstname)));        }
        if ($middlename)    {   $middlename     = preg_replace('/[^ \w-]/','',strtolower(trim($middlename)));       }
        if ($lastname)      {   $lastname       = preg_replace('/[^ \w-]/','',strtolower(trim($lastname)));         }
        if ($nickname)      {   $nickname       = preg_replace('/[^ \w-]/','',strtolower(trim($nickname)));         }

        if ($firstname)             {   wachthond($extdebug,3, "B. PREG_REPLACE, KEEP ONLY LETTERS/NUMBERS/DASHES");}
        if ($firstname)             {   wachthond($extdebug,3, "2 FN", $firstname);     }
        if ($middlename)            {   wachthond($extdebug,3, "2 MN", $middlename);    }
        if ($lastname)              {   wachthond($extdebug,3, "2 LN", $lastname);      }
        if ($nickname)              {   wachthond($extdebug,3, "2 NN", $nickname);      }

        #############################################################################################
        // 3. REPLACE SPACES WITH UNDERSCORES
        #############################################################################################

        if ($firstname)             {   $underscore_firstname   = str_replace(' ', '_', $firstname)     ?? NULL;    }
        if ($middlename)            {   $underscore_middlename  = str_replace(' ', '_', $middlename)    ?? NULL;    }
        if ($lastname)              {   $underscore_lastname    = str_replace(' ', '_', $lastname)      ?? NULL;    }
        if ($nickname)              {   $underscore_nickname    = str_replace(' ', '_', $nickname)      ?? NULL;    }

        if ($underscore_firstname)  {   wachthond($extdebug,3, "C1. STR_REPLACE SPACES WITH UNDERSCORES");  }
        if ($underscore_firstname)  {   wachthond($extdebug,3, "3 FN", $underscore_firstname);              }
        if ($underscore_middlename) {   wachthond($extdebug,3, "3 MN", $underscore_middlename);             }
        if ($underscore_lastname)   {   wachthond($extdebug,3, "3 LN", $underscore_lastname);               }
        if ($underscore_nickname)   {   wachthond($extdebug,3, "3 NN", $underscore_nickname);               }

        #############################################################################################
        // 3. REPLACE UNDERSCORES WITH NOTHING
        #############################################################################################

        if ($firstname)             {   $nospace_firstname      = str_replace(' ', '', $firstname)      ?? NULL;    }
        if ($middlename)            {   $nospace_middlename     = str_replace(' ', '', $middlename)     ?? NULL;    }
        if ($lastname)              {   $nospace_lastname       = str_replace(' ', '', $lastname)       ?? NULL;    }
        if ($nickname)              {   $nospace_nickname       = str_replace(' ', '', $nickname)       ?? NULL;    }

        if ($nospace_firstname)     {   wachthond($extdebug,3, "C2. STR_REPLACE REMOVE");                   }
        if ($nospace_firstname)     {   wachthond($extdebug,3, "3 FN [nospace]", $nospace_firstname);       }
        if ($nospace_middlename)    {   wachthond($extdebug,3, "3 MN [nospace]", $nospace_middlename);      }
        if ($nospace_lastname)      {   wachthond($extdebug,3, "3 LN [nospace]", $nospace_lastname);        }
        if ($nospace_nickname)      {   wachthond($extdebug,3, "3 NN [nospace]", $nospace_nickname);        }

        #############################################################################################
        // 4. CONSTRUCT FINAL USER_NAME
        #############################################################################################

        if ($firstname AND $middlename AND $lastname) {

            $user_name      = $nospace_firstname.".".$nospace_middlename."".$nospace_lastname;
            $user_name_org  = $underscore_firstname.".".$underscore_middlename.".".$underscore_lastname;
            $displayname    = $first_name." ".$middle_name." ".$last_name;

        } elseif ($firstname AND empty($middlename) AND $lastname) {

            $user_name      = $nospace_firstname.".".$nospace_lastname;
            $user_name_org  = $underscore_firstname.".".$underscore_lastname;
            $displayname    = $first_name." ".$last_name;

        } elseif ($firstname AND empty($middlename) AND empty($lastname)) {

            $user_name      = $nospace_firstname;
            $user_name_org  = $underscore_firstname;
            $displayname    = $first_name;

        } elseif (empty($firstname)) {

            $user_name      = NULL;
            $user_name_org  = NULL;

        } else {

            $user_name      = NULL;
            $user_name_org  = NULL;
        }

        #############################################################################################
        // IF CONTACT SUBTYPE STAF > GEBRUIK DAN ALLEEN LASTNAME VOOR USERNAME
        #############################################################################################

        wachthond($extdebug,3, 'contact_type',      $contact_type);
        wachthond($extdebug,3, 'contact_subtype',   $contact_subtype);

//      $commonValues           = array_intersect($contact_subtype, array("Staf","TEST"));
//      wachthond($extdebug,3, 'commonValues',      $commonValues);

//      if (!empty($commonValues)) {
//          $user_name      = $nospace_lastname;
//          wachthond($extdebug,1, "1 IF CONTACT SUBTYPE STAF > GEBRUIK DAN ALLEEN LASTNAME VOOR USERNAME", "[$nospace_lastname]");
//      }

//      if (!empty(array_intersect($contact_subtype, array("staf","TEST")))) {
//          $user_name      = $nospace_lastname;
//          wachthond($extdebug,1, "2 IF CONTACT SUBTYPE STAF > GEBRUIK DAN ALLEEN LASTNAME VOOR USERNAME", "[$nospace_lastname]");
//      }

//      if ($contact_type == 'Organisation' AND !empty(array_intersect($contact_subtype, array("staf","TEST")))) {
//          $user_name      = $nospace_lastname;
//          wachthond($extdebug,1, "3 IF CONTACT SUBTYPE STAF > GEBRUIK DAN ALLEEN LASTNAME VOOR USERNAME", "[$nospace_lastname]");
//      }

        if (in_array($lastname, array("kinderkamp1","kinderkamp2","brugkamp1","brugkamp2","tienerkamp1","tienerkamp2","jeugdkamp1","jeugdkamp2","topkamp"))) {
            $user_name      = $nospace_lastname;
            wachthond($extdebug,2, "4 IF LASTNAME IS EEN KAMP (EG. TIENERKAMP2) > GEBRUIK DAN ALLEEN LASTNAME VOOR USERNAME", "[$nospace_lastname]");
        }

        #############################################################################################
        // CONSTRUCT NAAM INCL. MEISJESNAAM (OM IN TE KUNNEN ZOEKEN)
        #############################################################################################

        if (empty($nick_name)) {
            $contactname = $displayname;
        } else {
            $contactname = $displayname."-".$nick_name;     
        }
        wachthond($extdebug,3, "contactname", $contactname);        

        #############################################################################################
        // CONSTRUCT FAMILIENAAM (TUSSENVOEGSEL + ACHTERNAAM)
        #############################################################################################

        if (empty($middle_name)) {
            $familienaam = $last_name;
        } else {
            $familienaam = $middle_name." ".$last_name;
        }
        wachthond($extdebug,3, "familienaam", $familienaam);

        #############################################################################################
        // USERNAME OBV MEISJESNAAM (HANDIG OM TE CHECKEN OF DUBBBEL ACCOUNT TOCH ZELFDE PERSOON IS)
        #############################################################################################

        if (!empty($nick_name)) {
            $user_name_nick     = $firstname.".".$nospace_nickname;
        } else {
            $user_name_nick     = $user_name;
        }

        wachthond($extdebug,2, "user_name_org  [firstname.middle.last]",    "$user_name_org");
        wachthond($extdebug,2, "user_name_new  [firstname.middle_last]",    "$user_name\t[NO NICKNAME]");
        wachthond($extdebug,2, "user_name_nick [firstname.nick_name]",      "$user_name_nick\t[EVT + MEISJESNAAM]");

        $user_name_unique       = $user_name ."_". $contact_id;
        wachthond($extdebug,2, "user_name_unq  [firstname.middle.last_contactid]",  "$user_name_unique\t[+ CID]");

        #############################################################################################
        // M61: MAAK DE SWITCH van [firstname.middle.last] NAAR [firstname.middlelast]
        #############################################################################################

        if ($user_name_chk) { $user_name = $user_name_nos; }

        wachthond($extdebug,2, "########################################################################");
        wachthond($extdebug,2, "### USERNAME 1.3 CHECK IF CONSTRUCTED USERNAME IS UNIQUE", "[$user_name]");
        wachthond($extdebug,2, "########################################################################");

        ##########################################################################################
        # CHECK IF CONSTRUCTED USER_NAME IS IN USE AND/OR CONNECTED TO CURRENT CONTACT
        # M61: TODO: KAN ZO ZIJN DAT ER >1 JAN.JANSEN's ZIJN EN EEN UNIQUE USER_NAME MOET WORDEN GEGENEREERD
        ##########################################################################################

        $drupal_loadbyname          = NULL;
        $drupal_loadbyname          = user_load_by_name($user_name);
        $drupal_loadbyname_found    = 0;

        wachthond($extdebug,3, "drupal_loadbyname->uid",    $drupal_loadbyname->uid);
        wachthond($extdebug,3, "drupal_loadbyname->mail",   $drupal_loadbyname->mail);
        wachthond($extdebug,3, "drupal_loadbyname->name",   $drupal_loadbyname->name);
        wachthond($extdebug,4, "drupal_loadbyname",         $drupal_loadbyname);

        // M61: TODO moet er echt logic hier of kunnen we uitkomst van ufmatch_diag gebruiken?

        if ($drupal_loadbyname->uid > 0) {

            wachthond($extdebug,2, "VIA USERNAAM ($user_name) EEN DRUPAL ACCOUNT ($drupal_loadbyname->uid) GEVONDEN");

        } else {

            wachthond($extdebug,2, "VIA USERNAAM ($user_name) DRUPAL ACCOUNT NIET GEVONDEN", "[KAN WORDEN AANGEMAAKT]");

            // M61: TODO URGENT: moet hier niet safe2update UFMATCH ipv account?

            $safe2update_account = 1;
            
            wachthond($extdebug,2, "SAFE2  UPDATE DRUPALACCOUNT",   $safe2update_account);
        }

        if ($drupal_loadbyname->uid > 0 AND $drupal_loadbyname->uid == $crm_externalid) {
            $valid_username         = $user_name;
            $valid_drupalid         = $drupal_loadbyname->uid;
            $safe2update_account    = 1;
            wachthond($extdebug,2,  "SAFE2  UPDATE DRUPALACCOUNT",  $safe2update_account);
            wachthond($extdebug,2,  "VIA USERNAAM ($user_name) GEVONDEN DRUPAL ACCOUNT", 
                                    "PRIMA! [MATCH MET EXTID]");
        }

        if ($drupal_loadbyname->uid > 0 AND $drupal_loadbyname->uid != $crm_externalid) {

            wachthond($extdebug,2,  "VIA USERNAAM ($user_name) GEVONDEN DRUPAL ACCOUNT", 
                                    "CHECK! [GEEN EXTID MATCH]"); // MOGELIJK WEL UFMATCH

            // LOGIC: INDIEN GEVONDEN DRUPAL ACCOUNT VIA EXTID GEKOPPELD IS: LEUK!
            // LOGIC: INDIEN NIET DAN IS HET ALSNOG MOGELIJK DAT ER EEN UF_MATCH IS

            // LOGIC: INDIEN EEN GEVONDEN UFMATCH WIJST NAAR HEEL ANDERE CONTACT DAN NIET SAFE
            // LOGIC: INDIEN OOK GEEN UFMATCH DAN IS DRUPAL ACCOUNT EEN ORPHAN
            //       (EN KAN MOGELIJK VIA NIEUWE UFMATCH GEKOPPELD WORDEN)

            $drupal_loadbyname_findcontact  = find_contact('drupalid',  $drupal_loadbyname->uid);

            if ($drupal_loadbyname_findcontact->id > 0) {

                wachthond($extdebug,2, "VIA USERNAAM ($user_name) GEVONDEN DRUPAL ACCOUNT HEEFT WEL CIVICRM CONTACT $drupal_loadbyname_findcontact->naam"); // MOGELIJK WEL UFMATCH

                if ($drupal_loadbyname_findcontact->id == $contact_id) {
                    wachthond($extdebug,2, "VIA ($user_name) GEVONDEN ACCOUNT HEEFT EXTID MATCH MET DIT CONTACT",   "PRIMA! [WEL UPDATE EXTID NODIG VOOR [$check_did_ufmatch_findcontact->naam]");
                } else {
                    wachthond($extdebug,2, "VIA ($user_name) GEVONDEN ACCOUNT HEEFT EXTID MATCH MET ANDER CID",     "ERROR! [CONFLICT MET $check_did_ufmatch_findcontact->naam]");                      
                }
            }

            $check_did_ufmatch              = find_ufmatch('drupalid',  $drupal_loadbyname->uid) ?? NULL;
            $check_did_ufmatch_found        = (is_object($check_did_ufmatch) == true ?  1 : 0 );
            wachthond($extdebug,4, "check_did_ufmatch_result",          $check_did_ufmatch);
            wachthond($extdebug,3, "check_did_ufmatch_found",           $check_did_ufmatch_found);

            if ($check_did_ufmatch_found == 1) {

                $check_did_ufmatch_findcontact = find_contact('contactid', $check_did_ufmatch->cid);

                if ($check_did_ufmatch->cid != $contact_id) {

                    wachthond($extdebug,2,  "VIA USERNAAM ($user_name) GEVONDEN DRUPAL ACCOUNT HEEFT UFMATCH MET ANDER CID",
                                            "ERROR! [$check_did_ufmatch_findcontact->naam]");
                    // M61: IN DIT GEVAL MOET EEN ALTERNATIEVE USER_NAME GEBRUIKT WORDEN (KAN ZIJN DAT JUISTE USERNAME HANGT AAN EMPTY PLACEHOLDER VAN ZELFDE CONTACT)
                    $valid_username         = $user_name ."_". $contact_id;
                    $need2update_jobtitle   = 1;
                }

                if ($check_did_ufmatch->cid == $contact_id) {

                    wachthond($extdebug,2, "VIA USERNAAM ($user_name) GEVONDEN DRUPAL ACCOUNT HEEFT UFMATCH MET DIT CONTACT",                   "HOERA! [$check_did_ufmatch_findcontact->naam]");
                    // M61: IN DIT GEVAL MOET DE CRM_EXTERNALID WORDEN GEUPDATE NAAR $check_did_ufmatch->cid OF $drupal_loadbyname->uid;
                    $valid_username         = $user_name;
                    $valid_drupalid         = $drupal_loadbyname->uid;
                    $need2update_extid      = 1;
                }

            } else {

                wachthond($extdebug,2,  "VIA USERNAAM ($user_name) GEVONDEN DRUPAL ACCOUNT KAN GEKOPPELD WORDEN!", 
                                        "ORPHAN [GEEN UFMATCH / EXTID MATCH VOOR UID: $drupal_loadbyname->uid]");              
                // M61: IN DIT GEVAL MOET DE UF_MATCH WORDEN GEUPDATE OF AANGEMAAKT                     
                $valid_drupalid             = $drupal_loadbyname->uid;

                if ($cid_ufmatch->id > 0) {
                    $need2update_ufmatch    = 1;
                } else {
                    $need2create_ufmatch    = 1;
                    $safe2create_ufmatch    = 1;

                }
            }
        }

        wachthond($extdebug,2, "########################################################################");
        wachthond($extdebug,2, "### USERNAME 1.4 DIAGNOSE UFMATCH");
        wachthond($extdebug,2, "########################################################################");

        $cid_ufmatch            = NULL;
        $cid_ufmatch_found      = NULL;
        $cid_ufmatch            = find_ufmatch('contactid', $contact_id) ?? NULL;
        $cid_ufmatch_found      = (is_object($cid_ufmatch) == true ?  1 : 0 );
        wachthond($extdebug,3, "cid_ufmatch_result",        $cid_ufmatch);
        wachthond($extdebug,3, "cid_ufmatch_found",         $cid_ufmatch_found);

        if ($cid_ufmatch_found == 1) {

            $diag_ufmatch           = diag_ufmatch('contactid', $contact_id, $cid_ufmatch, $user_name, $crm_drupalnaam, $crm_externalid);
            $diag_ufmatch_found     = (is_object($diag_ufmatch) == true ?  1 : 0 );

            wachthond($extdebug,3, "diag_ufmatch",              $diag_ufmatch);
            wachthond($extdebug,3, "diag_ufmatch_found",        $diag_ufmatch_found);

            $need2update_extid      = $diag_ufmatch->need2_update_extid;
            $need2update_jobtitle   = $diag_ufmatch->need2_update_jobtitle;
            wachthond($extdebug,3, "need2update_extid",         $need2update_extid);
            wachthond($extdebug,3, "need2update_jobtitle",      $need2update_jobtitle);

            $safe2update_extid      = $diag_ufmatch->safe2_update_extid;
            $safe2update_jobtitle   = $diag_ufmatch->safe2_update_jobtitle;
            wachthond($extdebug,3, "safe2update_extid",         $safe2update_extid);
            wachthond($extdebug,3, "safe2update_jobtitle",      $safe2update_jobtitle);

            if ($valid_drupalid     AND $need2update_extid AND $safe2update_extid)          {
                $params_contact_update['values']['external_identifier']    = $valid_drupalid;
                wachthond($extdebug,3, "DOE UPDATE VAN EXTERNAL ID (CMSEXTID) MET DRUPALID", $valid_drupalid);
            }
            if ($valid_username     AND $need2update_jobtitle   AND $safe2update_jobtitle)  {
                $params_contact_update['values']['job_title']              = $valid_username;
                wachthond($extdebug,3, "DOE UPDATE VAN CRMUSERNAME (JOBTITLE) MET USERNAME", $valid_username);
            }

        } 

        wachthond($extdebug,2, "########################################################################");
        wachthond($extdebug,2, "### USERNAME 2.0 UPDATE CONTACT",                            $displayname);
        wachthond($extdebug,2, "########################################################################");

        if ($first_name)    { $params_contact_update['values']['first_name']           = $first_name;      }
        if ($middle_name)   { $params_contact_update['values']['middle_name']          = $middle_name;     }
        if ($last_name)     { $params_contact_update['values']['last_name']            = $last_name;       }
        if ($nick_name)     { $params_contact_update['values']['nick_name']            = $nick_name;       }
        if ($displayname)   { $params_contact_update['values']['PRIVACY.naam']         = $displayname;     }
        if ($contactname)   { $params_contact_update['values']['PRIVACY.contactnaam']  = $contactname;     }

        // M61: updaten van velden in PRIVACY triggert daar ook weer. Wel of niet handig?

        if ($contact_id)  {

            wachthond($extdebug,3, 'params_contact_update',             $params_contact_update);
            $result_contact_update = civicrm_api4('Contact','update',   $params_contact_update);
            wachthond($extdebug,3, 'result_contact_update',             $result_contact_update);

        }

        ##########################################################################################
        ### USERNAME 3.0 RETURN VALUES
        ##########################################################################################        

        wachthond($extdebug,3, "contact_id",                    $contact_id);

        wachthond($extdebug,3, "first_name",                    $first_name);
        wachthond($extdebug,3, "middle_name",                   $middle_name);
        wachthond($extdebug,3, "last_name",                     $last_name);

        wachthond($extdebug,3, "displayname",                   $displayname);
        wachthond($extdebug,3, "contactname",                   $contactname);

        wachthond($extdebug,3, "user_name (constructed)",       $user_name);
        wachthond($extdebug,3, "user_name_nick (meisjesnaam)",  $user_name_nick);

        wachthond($extdebug,3, "crm_drupalnaam (job_title)",    $crm_drupalnaam);
        wachthond($extdebug,3, "valid_username",                $valid_username);
        wachthond($extdebug,3, "valid_drupalid",                $valid_drupalid);

        wachthond($extdebug,3, "need2update_extid",             $need2update_extid);
        wachthond($extdebug,3, "need2update_jobtitle",          $need2update_jobtitle);

        wachthond($extdebug,3, "need2repair_ufmatch",           $need2repair_ufmatch);
        wachthond($extdebug,3, "need2update_ufmatch",           $need2update_ufmatch);
        wachthond($extdebug,3, "need2create_ufmatch",           $need2create_ufmatch);
        wachthond($extdebug,3, "safe2update_ufmatch",           $safe2update_ufmatch);
        wachthond($extdebug,3, "safe2create_ufmatch",           $safe2create_ufmatch);

        $username_array = array(
            'contact_id'                => $contact_id,

            'first_name'                => $first_name,
            'middle_name'               => $middle_name,
            'last_name'                 => $last_name,

            'displayname'               => $displayname,
            'contactname'               => $contactname,
            'familienaam'               => $familienaam,            

            'user_name'                 => $user_name,
            'user_name_nick'            => $user_name_nick,
            'valid_username'            => $valid_username,
            'valid_drupalid'            => $valid_drupalid,

            'need2update_extid'         => $need2update_extid,
            'need2update_jobtitle'      => $need2update_jobtitle,

            'need2repair_ufmatch'       => $need2repair_ufmatch,
            'need2update_ufmatch'       => $need2update_ufmatch,
            'need2create_ufmatch'       => $need2create_ufmatch,
            'safe2update_ufmatch'       => $safe2update_ufmatch,
            'safe2create_ufmatch'       => $safe2create_ufmatch,
        );

        wachthond($extdebug,1, "########################################################################");
        wachthond($extdebug,1, "### USERNAME 3.0 RETURN VALUES",                           "[$user_name]");
        wachthond($extdebug,1, "########################################################################");

        return $username_array;

}

/**
 * Verrijkte add_activity: Combineert Drupal Sync diagnose met Logger-metadata.
 */
function add_activity($contact_id, $subject, $details, $issues_count = 0) {
    $activity_type_id = 159;
    $today = date("Y-m-d H:i:s");

    // 1. Ophalen van 'DITJAAR' data specifiek voor de Logger-velden
    try {
        $contact_data = civicrm_api4('Contact', 'get', [
            'checkPermissions' => FALSE,
            'limit' => 1,
            'select' => [
                'display_name',
                'DITJAAR.DITJAAR_cid',
                'DITJAAR.DITJAAR_pid',
                'DITJAAR.DITJAAR_eid',
                'DITJAAR.DITJAAR_rol',
                'DITJAAR.DITJAAR_functie',
                'DITJAAR.DITJAAR_kampkort',
                'DITJAAR.DITJAAR_kampjaar',
                'DITJAAR.DITJAAR_event_start',
                'DITJAAR.DITJAAR_event_end',
            ],
            'where' => [['id', '=', $contact_id]],
        ])->first();

        // Voorbereiden van datumwaarden en kamp-codes (Logger stijl)
        $acteventstart = !empty($contact_data['DITJAAR.DITJAAR_event_start']) ? date('Y-m-d H:i:s', strtotime($contact_data['DITJAAR.DITJAAR_event_start'])) : NULL;
        $acteventeinde = !empty($contact_data['DITJAAR.DITJAAR_event_end'])   ? date('Y-m-d H:i:s', strtotime($contact_data['DITJAAR.DITJAAR_event_end'])) : NULL;
        $actkampkort   = $contact_data['DITJAAR.DITJAAR_kampkort'] ?? '';
        $actkampkort_low = preg_replace('/[^ \w-]/','', strtolower(trim($actkampkort)));
        $actkampkort_cap = preg_replace('/[^ \w-]/','', strtoupper(trim($actkampkort)));

    } catch (\Exception $e) {
        wachthond(1, 1, "Fout bij ophalen DITJAAR info voor CID $contact_id", $e->getMessage());
    }

    // 2. HTML Opschoning voor de details
    $html_details = nl2br($details);
    $html_details = preg_replace('/(<br\s*\/?>\s*){2,}/', '<br />', $html_details);

    try {
        // 3. Zoek bestaande openstaande D7 Sync melding
        $existing = civicrm_api4('Activity', 'get', [
            'checkPermissions' => FALSE,
            'select' => ['id'],
            'where' => [
                ['activity_type_id',    '=',        $activity_type_id],
                ['target_contact_id',   'CONTAINS', $contact_id],
                ['status_id:name',      '=',        'Scheduled'],
                ['subject',             'LIKE',     'D7: %'],
            ],
            'limit' => 1,
        ])->first();

        // 4. Bouw het complete record (Logger + Sync)
        $values = [
            'activity_type_id'      => $activity_type_id,
            'subject'               => "D7: " . $subject,
            'details'               => $html_details,
            'status_id:name'        => 'Scheduled',
            'activity_date_time'    => $today,
            'target_contact_id'     => [$contact_id],
            'source_contact_id'     => 1,
            'priority_id:name'      => ($issues_count > 2) ? 'Urgent' : 'Normaal',
            
            // Logger Custom Fields: Deelname info
            'ACT_ALG.actcontact_naam'   => $contact_data['display_name'] ?? NULL,
            'ACT_ALG.actcontact_cid'    => $contact_id ?? NULL,
            'ACT_ALG.actcontact_pid'    => $contact_data['DITJAAR.DITJAAR_pid'] ?? NULL,
            'ACT_ALG.actcontact_eid'    => $contact_data['DITJAAR.DITJAAR_eid'] ?? NULL,
            'ACT_ALG.kampnaam'          => $actkampkort_cap,
            'ACT_ALG.kampkort'          => $actkampkort_low,
            'ACT_ALG.kampfunctie'       => $contact_data['DITJAAR.DITJAAR_functie'] ?? NULL,
            'ACT_ALG.kampstart'         => $acteventstart,
            'ACT_ALG.kampeinde'         => $acteventeinde,
            'ACT_ALG.kampjaar'          => $contact_data['DITJAAR.DITJAAR_kampjaar'] ?? NULL,
            'ACT_ALG.modified'          => $today,

            // Logger Custom Fields: Log info
            'ACT_LOG.logtype'           => 'drupal_sync',
            'ACT_LOG.level'             => ($issues_count > 0) ? 'error' : 'info',
            'ACT_LOG.message'           => $html_details,
            'ACT_LOG.context'           => "Automatische D7 diagnose: $issues_count punten.",
        ];

        if ($existing) {
            $values['id'] = $existing['id'];
        }

        $result = civicrm_api4('Activity', 'save', [
            'checkPermissions' => FALSE,
            'records' => [$values],
        ]);

        if (function_exists('wachthond')) {
            $action = ($existing) ? "BIJGEWERKT" : "AANGEMAAKT";
            wachthond(3, 1, "Logger-Sync Activity $action", "CID: $contact_id | ID: " . ($result[0]['id'] ?? '??'));
        }

    } catch (\Exception $e) {
        if (function_exists('wachthond')) {
            wachthond(1, 1, "Fout bij Logger-Sync Activity save", $e->getMessage());
        }
    }
}