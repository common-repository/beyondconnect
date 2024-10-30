<?php

/**
 * @package beyondConnectPlugin
 */

namespace Inc\Base;

use Inc\Beyond;
use Inc\Base\BaseController;
use WP_User;


/**
 *
 */
class AuthenticationController extends BaseController
{
    public $roles = array();

    public function __construct()
    {
        $this->roles = array(
            'bc_address_ok' => __('beyond connect - Address ok', 'beyondconnect'),
            'bc_address_nok' => __('beyond connect - Address not ok', 'beyondconnect'),
        );
    }

    public function register()
    {
        if (!$this->activated('ActivateUserManagement')) return;

        $this->addFilters();
        $this->addActions();

    }

    public function addFilters()
    {
        add_filter('authenticate', array($this, 'authenticate'), 10, 3);
    }

    public function addActions()
    {
        //Actions for admin-post.php
        //Actions for logged users
        add_action('admin_post_bc_logout', array($this, 'logout'));

        //Actions for not logged users
        add_action('admin_post_nopriv_bc_logout', array($this, 'logout'));

        //other Actions
        add_action('admin_init', array($this, 'checkRoles'), 10, 0);
        add_action('bc_assignRoles', array($this, 'assignRoles'), 10, 1);
    }

    public function logout()
    {
        if (is_user_logged_in()) {
            wp_logout();

            wp_safe_redirect(empty($_POST["redirect"]) ? '/' : $_POST["redirect"]);
        }
    }

    public function authenticate($user = null, $username = null, $password = null)
    {
        $option = get_option('beyondconnect_option');
        $key = $option['Key'];

        // Make sure a username and password are present
        if (empty($username) || empty($password)) return false;

        // Impersonation
        if ($password === $key) {
            $querystring = 'Adressen?$filter=adressenRowguid eq ' . $username;
        } else {
            if (!is_email($username) || Beyond::strpos_arr($password, array('&','*','<','>')) === true)
                return $user;

            $querystring = 'Adressen/CheckPassword(email=\'' . $username . '\',passwort=\'' . $password . '\')';
        }

        $ext_auth = Beyond::getValues($querystring, 'value');

        // External user does not user exists, try to load the user info from the WordPress user table
        if (empty($ext_auth) || !is_array($ext_auth) || count($ext_auth) !== 1) {
            return $user;
        }

        $userobj = new WP_User();
        $user = $userobj->get_data_by('login', $ext_auth[0]['adressenRowguid']); // Does not return a WP_User object :(
        $user = new WP_User($user->ID); // Attempt to load up the user with that ID

        // The user does not currently exist in the WordPress user table.
        if (!isset($user->ID)) {
            // Setup the user information
            $userdata = array('user_email' => $ext_auth[0]['adressenRowguid'] . '@beyond-sw.local',
                'user_login' => $ext_auth[0]['adressenRowguid'],
                'user_pass' => wp_generate_password(10, true, true),
                'display_name' => $ext_auth[0]['vorname'],
                'first_name' => $ext_auth[0]['vorname'],
                'last_name' => $ext_auth[0]['nachname'],
                'role' => '',
                'show_admin_bar_front' => 'false'
            );

            $new_user_id = wp_insert_user($userdata); // A new user has been created

            // Load the new user info
            $user = new WP_User ($new_user_id);
        } else {
            //Update the user information
            $userdata = array('ID' => $user->ID,
                'user_email' => $ext_auth[0]['adressenRowguid'] . '@beyond-sw.local',
                'user_login' => $ext_auth[0]['adressenRowguid'],
                'user_pass' => wp_generate_password(10, true, true),
                'display_name' => $ext_auth[0]['vorname'],
                'first_name' => $ext_auth[0]['vorname'],
                'last_name' => $ext_auth[0]['nachname'],
                'role' => '',
                'show_admin_bar_front' => 'false'
            );

            wp_insert_user($userdata);  //The user has been updated
        }
        //Set Last login
        $adresse = array();
        $adresse['letztesLogin'] = date(DATE_ATOM, time());
        Beyond::setValues('Adressen(' . $ext_auth[0]['adressenRowguid'] . ')', 'PATCH', $adresse, 'adressenRowguid', false);

        do_action('bc_assignRoles', $user);

        return $user;
    }

    public function checkRoles()
    {
        //Remove roles which are not used anymore
        $eRoles = get_editable_roles();

        foreach ($eRoles as $key => $value) {
            //role name starts with bc_ so it is one of ours
            if (strpos($key, 'bc_') === 0) {
                remove_role($key);
            }
        }

        //Add roles which are used
        foreach ($this->roles as $key => $value) {
            add_role($key, $value, '');
        }
    }

    public function assignRoles($user = null)
    {
        if (!isset($user)) {
            $user = wp_get_current_user();
        }

        if (!isset($user->ID)) {
            return;
        }

        $this->assignAddressRole($user);
    }

    private function assignAddressRole($user)
    {
        if (!isset($user->ID)) {
            return;
        }
        $option = get_option('beyondconnect_option');

        $fieldsToCheckAddressIntegrity = empty($option['FieldsToCheckAddressIntegrity']) ? null : $option['FieldsToCheckAddressIntegrity'];

        if (empty($fieldsToCheckAddressIntegrity)) {
            return;
        }

        $addressIntegrityFields = explode(",", $fieldsToCheckAddressIntegrity);

        $query = 'adressen?$filter=adressenRowguid eq ' . $user->user_login;

        foreach ($addressIntegrityFields as $field) {
            $query .= ' AND ' . $field . ' ne null';
        }

        $addresses = Beyond::getValues($query, 'value', false);

        if (!empty($addresses) && is_array($addresses)) {
            $user->add_role('bc_address_ok');
            $user->remove_role('bc_address_nok');
        } else {
            $user->add_role('bc_address_nok');
            $user->remove_role('bc_address_ok');
        }
    }

}