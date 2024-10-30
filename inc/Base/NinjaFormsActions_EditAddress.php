<?php

namespace Inc\Base;

use Inc\Beyond;

final class NinjaFormsActions_EditAddress extends NinjaFormsBaseAction
{
    protected $_name = 'BC_EditAddress';
    protected $_timing = 'late';
    protected $_priority = '0';

    public function __construct()
    {
        parent::__construct();
        $this->_nicename = __('BC Edit Address', 'beyondconnect');

        $settings = include 'NinjaFormsEditAddressActionSettings.php';

        $this->_settings = array_merge($this->_settings, $settings);
    }

    public function process($action_settings, $form_id, $data)
    {
        $adresse = $this->getLinkedFieldValues($action_settings, $form_id, $data);
        $adresseRowguid = $adresse['adressenRowguid'];
        unset($adresse['adressenRowguid']);

        $response = Beyond::setValues('Adressen(' . $adresseRowguid . ')', 'PATCH', $adresse, 'adressenRowguid', false);

        if (is_wp_error($response) || !Beyond::isGuid($response)) {
            $data['errors']['form']['adresse'] = __('Error saving address', 'beyondconnect') . $response;
        } else {
            $adresse['adressenRowguid'] = $response;

            if (empty($data['bcactionresults']['adressen'])) {
                $adressen = array();
            } else {
                $adressen = json_decode($data['bcactionresults']['adressen'], true);
            }

            array_push($adressen, $adresse);
            $data['bcactionresults']['adressen'] = json_encode($adressen);

            do_action('bc_assignRoles');
        }

        if ( defined( 'WP_DEBUG' ) && WP_DEBUG )
            error_log("beyondConnect: NinjaForms: EditAddress: " . print_r($data,true) . "\n", 3, $this->plugin_path . "/beyondConnect.log");

        return $data;
    }
}