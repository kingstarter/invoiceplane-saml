<?php
if (!defined('BASEPATH')) exit('No direct script access allowed');

/*
 * InvoicePlaneSaml Plugin
 *
 * @author		Steve Hegenbart (steve.hegenbart@kingstarter.de)
 * @copyright	Copyright (c) 2017 KingStarter GbR
 * @link		https://github.com/kingstarter/invoiceplane-saml
 */

/**
 * Class SamlSettings
 */
class SamlSettings extends Base_Controller
{
    
    /**
     * Get saml settings as required for onelogin/saml-php
     * @return Array containing saml settings
     */
    public function getSamlSettings() {
    
        $sp = array();
        $sp['entityid']  = get_setting('saml_sp_entity_id');
        $sp['signon']    = get_setting('saml_sp_sso_url');
        $sp['signout']   = get_setting('saml_sp_slo_url');
        $sp['techname']  = empty(get_setting('saml_sp_contact_name')) ?
                        'undefined technical contact' : get_setting('saml_sp_contact_name');
        $sp['techemail'] = empty(get_setting('saml_sp_contact_email')) ?
                        'undefined@technical.mail' : get_setting('saml_sp_contact_email');
    
        // Get sp certificate and key either from default path or from user defined path
        $spCrtPath = empty(get_setting('saml_sp_cert_path')) ? 
                        '/var/ip_certs/ip-sp.crt' : get_setting('saml_sp_cert_path');
        $spKeyPath = empty(get_setting('saml_sp_key_path')) ? 
                        '/var/ip_certs/ip-sp.key' : get_setting('saml_sp_key_path');
        $sp['privatecert'] = file_get_contents($spKeyPath);
        $sp['publiccert']  = file_get_contents($spCrtPath);
    
        $login  = htmlspecialchars($sp['signon'],  ENT_XML1);
        $logout = htmlspecialchars($sp['signout'], ENT_XML1);
    
        $idp = array();
        $idp['entityid'] = get_setting('saml_idp_entity_id');
        $idp['signon']   = get_setting('saml_idp_sso_url');
        $idp['signout']  = get_setting('saml_idp_slo_url');
    
        // Get either printed cert within settings or default idp cert path
        $idp['publiccert'] = empty(get_setting('saml_idp_cert')) ? 
                        file_get_contents('/var/ip_certs/idp.crt') : get_setting('saml_idp_cert');
    
        // Define the saml nameID format
        $samlNameID = empty(get_setting('saml_map_nameid_format')) ?
                        'urn:oasis:names:tc:SAML:1.1:nameid-format:unspecified' : get_setting('saml_map_nameid_format');
    
        $settingsInfo = array(
          'debug' => true,
          'security' => array(
            'authnRequestsSigned' => true,
            'signatureAlgorithm' => 'http://www.w3.org/2001/04/xmldsig-more#rsa-sha256',
            //'wantAssertionsEncrypted' => true,
            'requestedAuthnContext' =>false,
          ),
    
          'contactPerson' => array(
              'technical' => array(
                  'givenName' => $sp['techname'],
                  'emailAddress' => $sp['techemail']
              ),
          ),
    
          'sp' => array(
              'entityId' => $sp['entityid'],
              //'entittyId' => $sp['entityid'],
              'assertionConsumerService' => array(
                  'url' => $login,
                  'binding' => 'urn:oasis:names:tc:SAML:2.0:bindings:HTTP-POST',
              ),
              'singleLogoutService' => array(
                  'url' => $logout,
                  'binding' => 'urn:oasis:names:tc:SAML:2.0:bindings:HTTP-Redirect',
              ),
              // Use either preconfigured nameID format or user-setting 
              'NameIDFormat' => $samlNameID,
              'x509cert' => $sp['publiccert'],
              'privateKey' => $sp['privatecert'],
          ),
          'idp' => array(
              'entityId' => $idp['entityid'],
    
              'singleSignOnService' => array(
                  'url' => $idp['signon'],
              ),
              'singleLogoutService' => array(
                  'url' => $idp['signout'],
              ),
              'x509cert' => $idp['publiccert'],
          ),
        );
        return $settingsInfo;
    }
}
