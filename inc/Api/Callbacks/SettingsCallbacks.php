<?php
/**
 * @package beyondConnectPlugin
 */

namespace Inc\Api\Callbacks;

use Inc\Base\CallbacksBaseController;

class SettingsCallbacks extends CallbacksBaseController
{
    public function setConnectionText()
    {
        _e('Set the connection details which you get from beyond software to connect your WordPress with the service', 'beyondconnect');
    }

    public function setPaymentgatewaysText()
    {
        _e('Set the details for your payment gateway which you get from Six Payment Services', 'beyondconnect');
    }

    public function setWidgetsText()
    {
        _e('Manage the Components of this Plugin by activating the checkboxes from the following list.', 'beyondconnect');
    }

    public function setShortcodesText()
    {
        _e('Manage the Shortcodes of this Plugin by activating the checkboxes from the following list.', 'beyondconnect');
    }

    public function setOptionsText()
    {
        _e('Manage the Options of this Plugin by activating the checkboxes.', 'beyondconnect');
    }

    public function SettingsPage()
    {
        return require_once("$this->plugin_path/templates/settings.php");
    }

    public function sanitize($input)
    {
        $output = array();


        //Widget Setting Fields
        foreach ($this->widgets as $key => $value) {
            $output[$key] = isset($input[$key]) ? true : false;
        }

        //Shortcode Setting Fields
        foreach ($this->shortcodes as $key => $value) {
            $output[$key] = isset($input[$key]) ? true : false;
        }

        //Connection Setting Fields
        $output['Url'] = isset($input['Url']) ? rtrim($input['Url'], '/') . '/' : '';
        $output['Username'] = isset($input['Username']) ? $input['Username'] : '';
        $output['Key'] = isset($input['Key']) ? $input['Key'] : '';

        // Paymentgateways Setting Fields
        $output['PG_Url'] = isset($input['PG_Url']) ? $input['PG_Url'] : '';
        $output['PG_Username'] = isset($input['PG_Username']) ? $input['PG_Username'] : '';
        $output['PG_Password'] = isset($input['PG_Password']) ? $input['PG_Password'] : '';
        $output['PG_CustomerId'] = isset($input['PG_CustomerId']) ? $input['PG_CustomerId'] : '';
        $output['PG_TerminalId'] = isset($input['PG_TerminalId']) ? $input['PG_TerminalId'] : '';
        $output['PG_InitSuccess'] = isset($input['PG_InitSuccess']) ? $input['PG_InitSuccess'] : '';
        $output['PG_InitFail'] = isset($input['PG_InitFail']) ? $input['PG_InitFail'] : '';
        $output['PG_PaymentSuccess'] = isset($input['PG_PaymentSuccess']) ? $input['PG_PaymentSuccess'] : '';
        $output['PG_PaymentFail'] = isset($input['PG_PaymentFail']) ? $input['PG_PaymentFail'] : '';
        $output['PG_PaymentAbort'] = isset($input['PG_PaymentAbort']) ? $input['PG_PaymentAbort'] : '';
        $output['PG_PaymentNotify'] = isset($input['PG_PaymentNotify']) ? $input['PG_PaymentNotify'] : '';

        // Options Setting Fields
        $output['ActivateUserManagement'] = isset($input['ActivateUserManagement']) ? true : false;
        $output['FieldsToCheckAddressDuplicate'] = isset($input['FieldsToCheckAddressDuplicate']) ? $input['FieldsToCheckAddressDuplicate'] : '';
        $output['FieldsToCheckAddressIntegrity'] = isset($input['FieldsToCheckAddressIntegrity']) ? $input['FieldsToCheckAddressIntegrity'] : '';
        $output['UrlUnsubscribeNewsletter'] = isset($input['UrlUnsubscribeNewsletter']) ? $input['UrlUnsubscribeNewsletter'] : '';
        $output['UrlAddContinuation'] = isset($input['UrlAddContinuation']) ? $input['UrlAddContinuation'] : '';

        return $output;
    }
}