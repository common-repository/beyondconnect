<?php

namespace Inc\Base;

use Inc\Beyond;

final class NinjaFormsActions_AddParent extends NinjaFormsBaseAction
{
    protected $_name = 'BC_AddParent';
    protected $_timing = 'late';
    protected $_priority = '0';

    public function __construct()
    {
        parent::__construct();
        $this->_nicename = __('BC Add Parent', 'beyondconnect');

        $settings = include 'NinjaFormsAddAddressActionSettings.php';

        $this->_settings = array_merge($this->_settings, $settings);
    }

    public function process($action_settings, $form_id, $data)
    {
        $adresse = $this->getLinkedFieldValues($action_settings, $form_id, $data);

        $response = Beyond::setValues('Adressen', 'POST', $adresse, 'adressenRowguid', false);

        if (is_wp_error($response) || !Beyond::isGuid($response)) {
            $data['errors']['form']['parent'] = __('Error saving parent', 'beyondconnect') . $response;
        } else {
            if (strcasecmp($adresse['geschlecht'], 'w') === 0)
                $data['bcactionresults']['mutter'] = $response;
            else
                $data['bcactionresults']['vater'] = $response;
        }
        if ( defined( 'WP_DEBUG' ) && WP_DEBUG )
            error_log("beyondConnect: NinjaForms: AddParent: " . print_r($data,true) . "\n", 3, $this->plugin_path . "/beyondConnect.log");
        return $data;
    }
}