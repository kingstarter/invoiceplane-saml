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

/**
 * Class Mdl_Sessions
 */
class Mdl_Sessions extends CI_Model
{
    /**
     * @param $email
     * @param $password
     * @return bool
     */
    public function auth($email, $password)
    {
        $this->db->where('user_email', $email);

        $query = $this->db->get('ip_users');

        if ($query->num_rows()) {
            $user = $query->row();

            $this->load->library('crypt');

            /**
             * Password hashing changed after 1.2.0
             * Check to see if user has logged in since the password change
             */
            if (!$user->user_psalt) {
                /**
                 * The user has not logged in, so we're going to attempt to
                 * update their record with the updated hash
                 */
                if (md5($password) == $user->user_password) {
                    /**
                     * The md5 login validated - let's update this user
                     * to the new hash
                     */
                    $salt = $this->crypt->salt();
                    $hash = $this->crypt->generate_password($password, $salt);

                    $db_array = array(
                        'user_psalt' => $salt,
                        'user_password' => $hash
                    );

                    $this->db->where('user_id', $user->user_id);
                    $this->db->update('ip_users', $db_array);

                    $this->db->where('user_email', $email);
                    $user = $this->db->get('ip_users')->row();

                } else {
                    /**
                     * The password didn't verify against original md5
                     */
                    return false;
                }
            }

            if ($this->crypt->check_password($user->user_password, $password)) {
                $session_data = array(
                    'user_type' => $user->user_type,
                    'user_id' => $user->user_id,
                    'user_name' => $user->user_name,
                    'user_email' => $user->user_email,
                    'user_company' => $user->user_company,
                    'user_language' => isset($user->user_language) ? $user->user_language : 'system',
                );

                $this->session->set_userdata($session_data);

                return true;
            }
        }

        return false;
    }
    
    /**
     * Extension method for the InvoicePlaneSaml plugin.
     * Check the given username and email against any database
     * entry. If there is no entry, create one. Set the session.
     *
     * @param $username
     * @param $email
     * @return true
     */
    public function samlauth($username, $email)
    {
        $this->db->where('user_email', $email);

        $query = $this->db->get('ip_users');

        if ($query->num_rows()) {
            $user = $query->row();

            $session_data = array(
                'user_type' => $user->user_type,
                'user_id' => $user->user_id,
                'user_name' => $user->user_name,
                'user_email' => $user->user_email,
                'user_company' => $user->user_company,
                'user_language' => isset($user->user_language) ? $user->user_language : 'system',
            );

            $this->session->set_userdata($session_data);
            
            return true;
        } else {
            // New user, create entry
            $user = array(
                'user_type' => 1,
                'user_name' => $username,
                'user_email' => $email,
                'user_company' => '',
                'user_language' => 'system',
            );
            
            $this->db->insert('ip_users', $user);
        }
        return false;
    }

}
