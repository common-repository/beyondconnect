<?php

namespace Inc\Base;

use Inc\Beyond;

final class NinjaFormsActions_Login extends NinjaFormsBaseAction
{
    protected $_name = 'BC_Login';
    protected $_timing = 'late';
    protected $_priority = '0';

    public function __construct()
    {
        parent::__construct();
        $this->_nicename = __('BC Login', 'beyondconnect');

        $settings = include 'NinjaFormsLoginActionSettings.php';

        $this->_settings = array_merge($this->_settings, $settings);
    }

    public function process($action_settings, $form_id, $data)
    {
        $adresse = $this->getLinkedFieldValues($action_settings, $form_id, $data);
        $fieldnames = $this->getLinkedFieldNames($action_settings, $form_id, $data);

        //Throws error if username or password field is empty.
        if (empty($adresse['email']) || empty($adresse['passwort'])) {
            $data['errors']['form']['login'] = __('Please input username and password', 'beyondconnect');
            if ( defined( 'WP_DEBUG' ) && WP_DEBUG )
                error_log("beyondConnect: NinjaForms: Login: " . print_r($data,true) . "\n", 3, $this->plugin_path . "/beyondConnect.log");
            return $data;
        }

        /*
         * If the users site is on HTTPS we want to set the secure
         * login to true, if not leave it as false.
         */
        $secure_cookie_value = false;
        if ('https' === $_SERVER['REQUEST_SCHEME'] || 'on' === $_SERVER['HTTPS']) {
            $secure_cookie_value = true;
        }

        $login = wp_signon(
            array(
                'user_login' => $adresse['email'],
                'user_password' => $adresse['passwort']
            ),
            $secure_cookie_value
        );

        //Checks for errors in username and password fields and throws field errors.
        if (isset($login->errors['invalid_email'])) {
            $data['errors']['fields'][$fieldnames['email']] = __('invalid email address', 'beyondconnect');
        } elseif (isset($login->errors['incorrect_password'])) {
            $data['errors']['fields'][$fieldnames['passwort']] = __('invalid password', 'beyondconnect');
        } else {
            $data['actions']['redirect'] = $action_settings['BC_RedirectTo'];
        }
        if ( defined( 'WP_DEBUG' ) && WP_DEBUG )
            error_log("beyondConnect: NinjaForms: Login: " . print_r($data,true) . "\n", 3, $this->plugin_path . "/beyondConnect.log");
        return $data;
    }
}