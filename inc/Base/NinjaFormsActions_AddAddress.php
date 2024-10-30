<?php

namespace Inc\Base;

use Inc\Beyond;

final class NinjaFormsActions_AddAddress extends NinjaFormsBaseAction
{
    protected $_name = 'BC_AddAddress';
    protected $_timing = 'late';
    protected $_priority = '2';

    public function __construct()
    {
        parent::__construct();

        $this->_nicename = __('BC Add Address', 'beyondconnect');

        $settings = include 'NinjaFormsAddAddressActionSettings.php';

        $this->_settings = array_merge($this->_settings, $settings);
    }

    public function process($action_settings, $form_id, $data)
    {
        $option = get_option('beyondconnect_option');

        $addAddress = true;
        $fieldsToCheckAddressDuplicate = empty($option['FieldsToCheckAddressDuplicate']) ? null : $option['FieldsToCheckAddressDuplicate'];

        $formDataAddress = $this->getLinkedFieldValues($action_settings, $form_id, $data);

        if (!empty($data['bcactionresults']['mutter'])) {
            $formDataAddress['mutterRowguid'] = $data['bcactionresults']['mutter'];
        }

        if (!empty($data['bcactionresults']['vater'])) {
            $formDataAddress['vaterRowguid'] = $data['bcactionresults']['vater'];
        }

        // Does setting for update address exist
        if (!empty($fieldsToCheckAddressDuplicate)) {
            if ( defined( 'WP_DEBUG' ) && WP_DEBUG )
                error_log("beyondConnect: NinjaForms: AddAddress: CheckAddressDuplicate: Checking\n", 3, $this->plugin_path . "/beyondConnect.log");
            $addressDuplicateFields = explode(",", $fieldsToCheckAddressDuplicate);

            $query = '';

            foreach ($addressDuplicateFields as $field) {
                if (empty($query))
                    $query .= $field . ' eq \'' . $formDataAddress[$field] . '\'';
                else
                    $query .= ' AND ' . $field . ' eq \'' . $formDataAddress[$field] . '\'';
            }

            $query = 'adressen?$filter=' . $query;

            $queryAddresses = Beyond::getValues($query, 'value', false);
            if ( defined( 'WP_DEBUG' ) && WP_DEBUG )
                error_log("beyondConnect: NinjaForms: AddAddress: CheckAddressDuplicate: " . print_r($queryAddresses,true) . "\n", 3, $this->plugin_path . "/beyondConnect.log");

            // Check if address already exists
            if (!empty($queryAddresses) && is_array($queryAddresses) && count($queryAddresses) === 1)
            {
                $queryAddress = $queryAddresses[0];
                $updateAddress = $formDataAddress;

                foreach ($updateAddress as $key => $value) {
                    if (empty($updateAddress[$key]))
                        unset($updateAddress[$key]);
                }
                $updateAddress['adressenRowguid'] = $queryAddress['adressenRowguid'];

                //Update existing address
                Beyond::setValues('Adressen(' . $updateAddress['adressenRowguid'] . ')', 'PATCH', $updateAddress, 'adressenRowguid', true);

                $formDataAddress = $updateAddress;
                $addAddress = false;
            }
        }

        //add address
        if ($addAddress) {
            if ( defined( 'WP_DEBUG' ) && WP_DEBUG )
                error_log("beyondConnect: NinjaForms: AddAddress: Adding\n", 3, $this->plugin_path . "/beyondConnect.log");
            $response = Beyond::setValues('Adressen', 'POST', $formDataAddress, 'adressenRowguid', false);

            //error while adding address
            if (is_wp_error($response) || !Beyond::isGuid($response)) {
                $data['errors']['form']['adresse'] = __('Error saving address', 'beyondconnect') . $response;
                if ( defined( 'WP_DEBUG' ) && WP_DEBUG )
                    error_log("beyondConnect: NinjaForms: AddAddress: " . print_r($data,true) . "\n", 3, $this->plugin_path . "/beyondConnect.log");
                return $data;
            }

            $formDataAddress['adressenRowguid'] = $response;
        }

        //set bcactionresult
        if (empty($data['bcactionresults']['adressen'])) {
            $actionResultsAddresses = array();
        } else {
            $actionResultsAddresses = json_decode($data['bcactionresults']['adressen'], true);
        }

        array_push($actionResultsAddresses, $formDataAddress);
        $data['bcactionresults']['adressen'] = json_encode($actionResultsAddresses);

        if ( defined( 'WP_DEBUG' ) && WP_DEBUG )
            error_log("beyondConnect: NinjaForms: AddAddress: " . print_r($data,true) . "\n", 3, $this->plugin_path . "/beyondConnect.log");
        return $data;
    }
}