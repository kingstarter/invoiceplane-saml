<?php
if (!defined('BASEPATH')) exit('No direct script access allowed');

/*
 * InvoicePlane
 *
 * @author		InvoicePlane Developers & Contributors
 * @copyright	Copyright (c) 2012 - 2017 InvoicePlane.com
 * @license		https://invoiceplane.com/license.txt
 * @link		https://invoiceplane.com
 *
 * This is a modified Sessions file for IP 1.5.3 (git master).
 * Updated and modified for the kingstarter/invoiceplane-saml 
 * plugin. 
 */

require_once(APPPATH . '/modules/settings/controllers/SamlSettings.php');
require_once(FCPATH . '/vendor/onelogin/php-saml/_toolkit_loader.php');

/**
 * Class Sessions
 */
class Sessions extends Base_Controller
{
    public function index()
    {
        // Early redirect in case SAML is set as direct login method 
        if (get_setting('saml_sp_mode') == 1)
            redirect('sessions/samllogin');
        redirect('sessions/login');
    }

    public function login()
    {
        // Early redirect in case SAML is set as direct login method 
        if (get_setting('saml_sp_mode') == 1)
            redirect('sessions/samllogin');
        $view_data = array(
            'login_logo' => get_setting('login_logo')
        );

        if ($this->input->post('btn_login')) {

            $this->db->where('user_email', $this->input->post('email'));
            $query = $this->db->get('ip_users');
            $user = $query->row();

            // Check if the user exists
            if (empty($user)) {
                $this->session->set_flashdata('alert_error', trans('loginalert_user_not_found'));
                redirect('sessions/login');
            } else {

                // Check if the user is marked as active
                if ($user->user_active == 0) {
                    $this->session->set_flashdata('alert_error', trans('loginalert_user_inactive'));
                    redirect('sessions/login');
                } else {

                    if ($this->authenticate($this->input->post('email'), $this->input->post('password'))) {
                        if ($this->session->userdata('user_type') == 1) {
                            redirect('dashboard');
                        } elseif ($this->session->userdata('user_type') == 2) {
                            redirect('guest');
                        }
                    } else {
                        $this->session->set_flashdata('alert_error', trans('loginalert_credentials_incorrect'));
                        redirect('sessions/login');
                    }

                }

            }

        }

        $this->load->view('session_login', $view_data);
    }
    
    /**
     * Make a SAML login request against the IDP
     */
    public function samllogin()
    {
        $samlSettings = new SamlSettings();
        $authRequest = new \OneLogin_Saml2_Auth($samlSettings->getSamlSettings());
        $url = $authRequest->login();
        $this->response->redirect($url);
    }

    /**
     * Authenticate via SAML response from IDP
     */
    public function samlauth()
    {
        $this->load->model('mdl_sessions');

        $settings = new SamlSettings();
        try {
            if (isset($_POST['SAMLResponse'])) {
                $samlSettings = new \OneLogin_Saml2_Settings($settings->getSamlSettings(), true);
                $samlResponse = new \OneLogin_Saml2_Response($samlSettings, $_POST['SAMLResponse']);
                if ($samlResponse->isValid()) {
  
                    /**
                     * DEBUG OUTPUT
                     * Use this for debugging and checking your saml configuration
                     */
//                    /*
                    echo 'You are: ' . $samlResponse->getNameId() . '<br>';
                    $attributes = $samlResponse->getAttributes();
                    if (!empty($attributes)) {
                        echo 'You have the following attributes:<br>';
                        echo '<table><thead><th>Name</th><th>Values</th></thead><tbody>';
                        foreach ($attributes as $attributeName => $attributeValues) {
                           echo '<tr><td>' . htmlentities($attributeName) . '</td><td><ul>';
                           foreach ($attributeValues as $attributeValue) {
                               echo '<li>' . htmlentities($attributeValue) . '</li>';
                           }
                           echo '</ul></td></tr>';
                        }
                        echo '</tbody></table>';
                    }
                    echo '<br />Other attributes:<br /><ul>';
                    echo '<li>Config username: ' . get_setting('saml_map_username') . '</li>';
                    echo '<li>Config mailaddr: ' . get_setting('saml_map_mail') . '</li>';
                    echo '</ul><br />';
//                    */
  
                    // Get attributes for SAML from configModel
                    $atrb_email     = get_setting('saml_map_mail');
                    $atrb_username  = get_setting('saml_map_username');
  
                    // Get user information via specified attributes
                    $email      = $samlResponse->getAttributes()["$atrb_email"]['0'];
                    $username   = $samlResponse->getAttributes()["$atrb_username"]['0'];
  
                    // Check if username and email are set
                    if (!empty($username) && !empty($email)) {
                        // Create user by having email as username
                        $this->userInfo = new SamlUserProvider($username, $email, '');
                        return true;
    
                    } else {
                        die('Invalid username and email.');
                    }
    
                } else {
                    die('Invalid SAML Response.');
                }
            } else {
                // In case of no SAML response, go on with app
                return false;
            }
        } catch (Exception $e) {
            // In case of invalid SAML response:
            echo 'Invalid SAML Response: ' . $e->getMessage();
        }
    
        return false;
    }

    /**
     * @param $email_address
     * @param $password
     * @return bool
     */
    public function authenticate($email_address, $password)
    {
        $this->load->model('mdl_sessions');

        if ($this->mdl_sessions->auth($email_address, $password)) {
            return true;
        }

        return false;
    }

    public function logout()
    {
        $this->session->sess_destroy();

        redirect('sessions/login');
    }

    /**
     * @param null $token
     * @return mixed
     */
    public function passwordreset($token = null)
    {
        // Check if a token was provided
        if ($token) {
            $this->db->where('user_passwordreset_token', $token);
            $user = $this->db->get('ip_users');
            $user = $user->row();

            if (empty($user)) {
                // Redirect back to the login screen with an alert
                $this->session->set_flashdata('alert_error', trans('wrong_passwordreset_token'));
                redirect('sessions/passwordreset');
            }

            $formdata = array(
                'token' => $token,
                'user_id' => $user->user_id,
            );

            return $this->load->view('session_new_password', $formdata);
        }

        // Check if the form for a new password was used
        if ($this->input->post('btn_new_password')) {
            $new_password = $this->input->post('new_password');
            $user_id = $this->input->post('user_id');

            if (empty($user_id) || empty($new_password)) {
                $this->session->set_flashdata('alert_error', trans('loginalert_no_password'));
                redirect($_SERVER['HTTP_REFERER']);
            }

            $this->load->model('users/mdl_users');

            // Check for the reset token
            $user = $this->mdl_users->get_by_id($user_id);

            if (empty($user)) {
                $this->session->set_flashdata('alert_error', trans('loginalert_user_not_found'));
                redirect($_SERVER['HTTP_REFERER']);
            }

            if (empty($user->user_passwordreset_token) || $this->input->post('token') !== $user->user_passwordreset_token) {
                $this->session->set_flashdata('alert_error', trans('loginalert_wrong_auth_code'));
                redirect($_SERVER['HTTP_REFERER']);
            }

            // Call the save_change_password() function from users model
            $this->mdl_users->save_change_password(
                $user_id, $new_password
            );

            // Update the user and set him active again
            $db_array = array(
                'user_passwordreset_token' => '',
            );

            $this->db->where('user_id', $user_id);
            $this->db->update('ip_users', $db_array);

            // Redirect back to the login form
            redirect('sessions/login');

        }

        // Check if the password reset form was used
        if ($this->input->post('btn_reset')) {
            $email = $this->input->post('email');

            if (empty($email)) {
                $this->session->set_flashdata('alert_error', trans('loginalert_user_not_found'));
                redirect($_SERVER['HTTP_REFERER']);
            }

            // Test if a user with this email exists
            if ($this->db->where('user_email', $email)) {
                // Create a passwordreset token
                $email = $this->input->post('email');
                $token = md5(time() . $email);

                // Save the token to the database and set the user to inactive
                $db_array = array(
                    'user_passwordreset_token' => $token,
                );

                $this->db->where('user_email', $email);
                $this->db->update('ip_users', $db_array);

                // Send the email with reset link
                $this->load->helper('mailer');

                // Preprare some variables for the email
                $email_resetlink = site_url('sessions/passwordreset/' . $token);
                $email_message = $this->load->view('emails/passwordreset', array(
                    'resetlink' => $email_resetlink
                ), true);
                $email_from = 'system@' . preg_replace("/^[\w]{2,6}:\/\/([\w\d\.\-]+).*$/", "$1", base_url());

                // Mail the invoice with the pre-configured mailer if possible
                if (mailer_configured()) {

                    $this->load->helper('mailer/phpmailer');

                    if (!phpmail_send($email_from, $email, trans('password_reset'), $email_message)) {
                        $email_failed = true;
                    }

                } else {

                    $this->load->library('email');

                    // Set email configuration
                    $config['mailtype'] = 'html';
                    $this->email->initialize($config);

                    // Set the email params
                    $this->email->from($email_from);
                    $this->email->to($email);
                    $this->email->subject(trans('password_reset'));
                    $this->email->message($email_message);

                    // Send the reset email
                    if (!$this->email->send()) {
                        $email_failed = true;
                        log_message('error', $this->email->print_debugger());
                    }
                }

                // Redirect back to the login screen with an alert
                if (isset($email_failed)) {
                    $this->session->set_flashdata('alert_error', trans('password_reset_failed'));
                } else {
                    $this->session->set_flashdata('alert_success', trans('email_successfully_sent'));
                }

                redirect('sessions/login');
            }
        }

        return $this->load->view('session_passwordreset');
    }

}
