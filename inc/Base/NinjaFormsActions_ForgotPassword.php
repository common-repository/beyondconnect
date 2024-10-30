<?php

namespace Inc\Base;

use Inc\Beyond;

final class NinjaFormsActions_ForgotPassword extends NinjaFormsBaseAction
{
    protected $_name = 'BC_ForgotPassword';
    protected $_timing = 'late';
    protected $_priority = '0';

    public function __construct()
    {
        parent::__construct();
        $this->_nicename = __('BC Forgot Password', 'beyondconnect');

        $settings = include 'NinjaFormsForgotPasswordActionSettings.php';

        $this->_settings = array_merge($this->_settings, $settings);
    }

    public function process($action_settings, $form_id, $data)
    {
        $adresse = $this->getLinkedFieldValues($action_settings, $form_id, $data);

        //Throws error if username or password field is empty.
        if (empty($adresse['email'])) {
            $data['errors']['form']['login'] = __('Please input email', 'beyondconnect');
            if ( defined( 'WP_DEBUG' ) && WP_DEBUG )
                error_log("beyondConnect: NinjaForms: ForgotPassword: " . print_r($data,true) . "\n", 3, $this->plugin_path . "/beyondConnect.log");
            return $data;
        }

        $querystring = 'Adressen/SendPassword(email=\'' . $adresse['email'] . '\')';
        Beyond::getValues($querystring, 'value');

        $data['actions']['redirect'] = $action_settings['BC_RedirectTo'];
        if ( defined( 'WP_DEBUG' ) && WP_DEBUG )
            error_log("beyondConnect: NinjaForms: ForgotPassword: " . print_r($data,true) . "\n", 3, $this->plugin_path . "/beyondConnect.log");
        return $data;
    }
}