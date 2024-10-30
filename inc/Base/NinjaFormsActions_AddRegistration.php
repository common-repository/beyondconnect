<?php

namespace Inc\Base;

use Inc\Beyond;

final class NinjaFormsActions_AddRegistration extends NinjaFormsBaseAction
{
    protected $_name = 'BC_AddRegistration';
    protected $_timing = 'late';
    protected $_priority = '5';

    public function __construct()
    {
        parent::__construct();
        $this->_nicename = __('BC Add Registration', 'beyondconnect');

        $settings = include 'NinjaFormsAddRegistrationActionSettings.php';

        $this->_settings = array_merge($this->_settings, $settings);
    }

    public function process($action_settings, $form_id, $data)
    {
        $anmeldung = $this->getLinkedFieldValues($action_settings, $form_id, $data);

        $jsonAdressen = $data['bcactionresults']['adressen'];
        $adressen = json_decode($jsonAdressen, true);

        if (!empty($adressen) && !empty($adressen[0])) {
            $anmeldung['adresse1Rowguid'] = $adressen[0]['adressenRowguid'];

            //if no sex for subscription is given, then take sex of address
            //if sex of address is not m or w (e.g x) then set sex to m
            if (empty($anmeldung['geschlecht1']))
                $anmeldung['geschlecht1'] = ($adressen[0]['geschlecht'] === 'm' || $adressen[0]['geschlecht'] === 'w') ? $adressen[0]['geschlecht'] : 'm';
        }
        if (!empty($adressen) && !empty($adressen[1])) {
            $anmeldung['adresse2Rowguid'] = $adressen[1]['adressenRowguid'];

            //if no sex for subscription is given, then take sex of address
            //if sex of address is not m or w (e.g. x) then set sex to w
            if (empty($anmeldung['geschlecht2']))
                $anmeldung['geschlecht2'] = ($adressen[1]['geschlecht'] === 'm' || $adressen[1]['geschlecht'] === 'w') ? $adressen[1]['geschlecht'] : 'w';
        }

        $response = Beyond::setValues('Anmeldungen', 'POST', $anmeldung, 'person1Rowguid', false);

        if (is_wp_error($response) || empty($response) || !Beyond::isGuid($response)) {
            $data['errors']['form']['anmeldung'] = __('Error saving registration', 'beyondconnect') . $response;
        }

        if ( defined( 'WP_DEBUG' ) && WP_DEBUG )
            error_log("beyondConnect: NinjaForms: AddRegistration: " . print_r($data,true) . "\n", 3, $this->plugin_path . "/beyondConnect.log");

        return $data;
    }
}