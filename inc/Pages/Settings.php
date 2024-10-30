<?php
/**
 * @package beyondConnectPlugin
 */

namespace Inc\Pages;

use Inc\Api\SettingsApi;
use Inc\Base\BaseController;
use Inc\Api\Callbacks\SettingsCallbacks;

class Settings extends BaseController
{
    public $settings;

    public $callbacks;

    public $callbacks_mngr;

    public $pages = array();

    public function register()
    {
        $this->settings = new SettingsApi();

        $this->callbacks = new SettingsCallbacks();

        $this->setSubpages();

        $this->setSettings();
        $this->setSections();
        $this->setFields();

        $this->settings->addSubPages($this->subpages)->register();
    }

    public function setSubpages()
    {
        $this->subpages = array(
            array(
                'parent_slug' => 'beyondconnect',
                'page_title' => __('Settings', 'beyondconnect'),
                'menu_title' => __('Settings', 'beyondconnect'),
                'capability' => 'manage_options',
                'menu_slug' => 'beyondconnect_settings',
                'callback' => array($this->callbacks, 'SettingsPage')
            )
        );
    }

    public function setSettings()
    {
        $args = array(
            array(
                'option_group' => 'beyondconnect_components_settings',
                'option_name' => 'beyondconnect_option',
                'callback' => array($this->callbacks, 'sanitize')
            )
        );

        $this->settings->setSettings($args);
    }

    public function setSections()
    {
        $args = array(
            array(
                'id' => 'beyondconnect_connection_section',
                'title' => __('Connection Settings', 'beyondconnect'),
                'callback' => array($this->callbacks, 'setConnectionText'),
                'page' => 'beyondconnect_connection_page'
            ),
            array(
                'id' => 'beyondconnect_paymentgateways_section',
                'title' => __('Payment Gateways Settings', 'beyondconnect'),
                'callback' => array($this->callbacks, 'setPaymentgatewaysText'),
                'page' => 'beyondconnect_paymentgateways_page'
            ),
            array(
                'id' => 'beyondconnect_widgets_section',
                'title' => __('Widgets Settings', 'beyondconnect'),
                'callback' => array($this->callbacks, 'setWidgetsText'),
                'page' => 'beyondconnect_widgets_page'
            ),
            array(
                'id' => 'beyondconnect_shortcodes_section',
                'title' => __('Shortcodes Settings', 'beyondconnect'),
                'callback' => array($this->callbacks, 'setShortcodesText'),
                'page' => 'beyondconnect_shortcodes_page'
            ),
            array(
                'id' => 'beyondconnect_options_section',
                'title' => __('Options Settings', 'beyondconnect'),
                'callback' => array($this->callbacks, 'setOptionsText'),
                'page' => 'beyondconnect_options_page'
            )
        );

        $this->settings->setSections($args);
    }

    public function setFields()
    {
        $args = array();

        //Widget Setting Fields
        foreach ($this->widgets as $key => $value) {
            $args[] = array(
                'id' => $key,
                'title' => $value,
                'callback' => array($this->callbacks, 'checkboxField'),
                'page' => 'beyondconnect_widgets_page',
                'section' => 'beyondconnect_widgets_section',
                'args' => array(
                    'option_name' => 'beyondconnect_option',
                    'label_for' => $key,
                    'class' => 'ui-toggle'
                )
            );
        }

        //Shortcode Setting Fields
        foreach ($this->shortcodes as $key => $value) {
            $args[] = array(
                'id' => $key,
                'title' => $value,
                'callback' => array($this->callbacks, 'checkboxField'),
                'page' => 'beyondconnect_shortcodes_page',
                'section' => 'beyondconnect_shortcodes_section',
                'args' => array(
                    'option_name' => 'beyondconnect_option',
                    'label_for' => $key,
                    'class' => 'ui-toggle'
                )
            );
        }

        //Connection Setting Fields
        $args[] = array(
            'id' => 'Url',
            'title' => 'Url',
            'callback' => array($this->callbacks, 'textField'),
            'page' => 'beyondconnect_connection_page',
            'section' => 'beyondconnect_connection_section',
            'args' => array(
                'option_name' => 'beyondconnect_option',
                'label_for' => 'Url',
                'required' => true
            )
        );
        $args[] = array(
            'id' => 'Username',
            'title' => __('Username', 'beyondconnect'),
            'callback' => array($this->callbacks, 'textField'),
            'page' => 'beyondconnect_connection_page',
            'section' => 'beyondconnect_connection_section',
            'args' => array(
                'option_name' => 'beyondconnect_option',
                'label_for' => 'Username',
                'required' => true
            )
        );
        $args[] = array(
            'id' => 'Key',
            'title' => __('Key/Password', 'beyondconnect'),
            'callback' => array($this->callbacks, 'passwordField'),
            'page' => 'beyondconnect_connection_page',
            'section' => 'beyondconnect_connection_section',
            'args' => array(
                'option_name' => 'beyondconnect_option',
                'label_for' => 'Key',
                'required' => true
            )
        );

        // Paymentgateways Setting Fields
        $args[] = array(
            'id' => 'PG_Url',
            'title' => 'Url',
            'callback' => array($this->callbacks, 'textField'),
            'page' => 'beyondconnect_paymentgateways_page',
            'section' => 'beyondconnect_paymentgateways_section',
            'args' => array(
                'option_name' => 'beyondconnect_option',
                'label_for' => 'PG_Url',
                'required' => false
            )
        );
        $args[] = array(
            'id' => 'PG_Username',
            'title' => __('Username', 'beyondconnect'),
            'callback' => array($this->callbacks, 'textField'),
            'page' => 'beyondconnect_paymentgateways_page',
            'section' => 'beyondconnect_paymentgateways_section',
            'args' => array(
                'option_name' => 'beyondconnect_option',
                'label_for' => 'PG_Username',
                'required' => false
            )
        );
        $args[] = array(
            'id' => 'PG_Password',
            'title' => __('Password', 'beyondconnect'),
            'callback' => array($this->callbacks, 'passwordField'),
            'page' => 'beyondconnect_paymentgateways_page',
            'section' => 'beyondconnect_paymentgateways_section',
            'args' => array(
                'option_name' => 'beyondconnect_option',
                'label_for' => 'PG_Password',
                'required' => false
            )
        );
        $args[] = array(
            'id' => 'PG_CustomerId',
            'title' => __('Customer ID', 'beyondconnect'),
            'callback' => array($this->callbacks, 'textField'),
            'page' => 'beyondconnect_paymentgateways_page',
            'section' => 'beyondconnect_paymentgateways_section',
            'args' => array(
                'option_name' => 'beyondconnect_option',
                'label_for' => 'PG_CustomerId',
                'required' => false
            )
        );
        $args[] = array(
            'id' => 'PG_TerminalId',
            'title' => __('Terminal ID', 'beyondconnect'),
            'callback' => array($this->callbacks, 'textField'),
            'page' => 'beyondconnect_paymentgateways_page',
            'section' => 'beyondconnect_paymentgateways_section',
            'args' => array(
                'option_name' => 'beyondconnect_option',
                'label_for' => 'PG_TerminalId',
                'required' => false
            )
        );
        $args[] = array(
            'id' => 'PG_InitSuccess',
            'title' => __('Url Init Success', 'beyondconnect'),
            'callback' => array($this->callbacks, 'textField'),
            'page' => 'beyondconnect_paymentgateways_page',
            'section' => 'beyondconnect_paymentgateways_section',
            'args' => array(
                'option_name' => 'beyondconnect_option',
                'label_for' => 'PG_InitSuccess',
                'required' => false
            )
        );
        $args[] = array(
            'id' => 'PG_InitFail',
            'title' => __('Url Init Fail', 'beyondconnect'),
            'callback' => array($this->callbacks, 'textField'),
            'page' => 'beyondconnect_paymentgateways_page',
            'section' => 'beyondconnect_paymentgateways_section',
            'args' => array(
                'option_name' => 'beyondconnect_option',
                'label_for' => 'PG_InitFail',
                'required' => false
            )
        );
        $args[] = array(
            'id' => 'PG_PaymentSuccess',
            'title' => __('Url Payment Success', 'beyondconnect'),
            'callback' => array($this->callbacks, 'textField'),
            'page' => 'beyondconnect_paymentgateways_page',
            'section' => 'beyondconnect_paymentgateways_section',
            'args' => array(
                'option_name' => 'beyondconnect_option',
                'label_for' => 'PG_PaymentSuccess',
                'required' => false
            )
        );
        $args[] = array(
            'id' => 'PG_PaymentFail',
            'title' => __('Url Payment Fail', 'beyondconnect'),
            'callback' => array($this->callbacks, 'textField'),
            'page' => 'beyondconnect_paymentgateways_page',
            'section' => 'beyondconnect_paymentgateways_section',
            'args' => array(
                'option_name' => 'beyondconnect_option',
                'label_for' => 'PG_PaymentFail',
                'required' => false
            )
        );
        $args[] = array(
            'id' => 'PG_PaymentAbort',
            'title' => __('Url Payment Abort', 'beyondconnect'),
            'callback' => array($this->callbacks, 'textField'),
            'page' => 'beyondconnect_paymentgateways_page',
            'section' => 'beyondconnect_paymentgateways_section',
            'args' => array(
                'option_name' => 'beyondconnect_option',
                'label_for' => 'PG_PaymentAbort',
                'required' => false
            )
        );
        $args[] = array(
            'id' => 'PG_PaymentNotify',
            'title' => __('Url Payment Notify', 'beyondconnect'),
            'callback' => array($this->callbacks, 'textField'),
            'page' => 'beyondconnect_paymentgateways_page',
            'section' => 'beyondconnect_paymentgateways_section',
            'args' => array(
                'option_name' => 'beyondconnect_option',
                'label_for' => 'PG_PaymentNotify',
                'required' => false
            )
        );


        //Options Setting Fields
        $args[] = array(
            'id' => 'ActivateUserManagement',
            'title' => __('User Management', 'beyondconnect'),
            'callback' => array($this->callbacks, 'checkboxField'),
            'page' => 'beyondconnect_options_page',
            'section' => 'beyondconnect_options_section',
            'args' => array(
                'option_name' => 'beyondconnect_option',
                'label_for' => 'ActivateUserManagement',
                'class' => 'ui-toggle'
            )
        );
        $args[] = array(
            'id' => 'FieldsToCheckAddressDuplicate',
            'title' => __('Fields to check address duplicate', 'beyondconnect'),
            'callback' => array($this->callbacks, 'textField'),
            'page' => 'beyondconnect_options_page',
            'section' => 'beyondconnect_options_section',
            'args' => array(
                'option_name' => 'beyondconnect_option',
                'label_for' => 'FieldsToCheckAddressDuplicate',
                'required' => false
            )
        );
        $args[] = array(
            'id' => 'FieldsToCheckAddressIntegrity',
            'title' => __('Fields to check address integrity', 'beyondconnect'),
            'callback' => array($this->callbacks, 'textField'),
            'page' => 'beyondconnect_options_page',
            'section' => 'beyondconnect_options_section',
            'args' => array(
                'option_name' => 'beyondconnect_option',
                'label_for' => 'FieldsToCheckAddressIntegrity',
                'required' => false
            )
        );
        $args[] = array(
            'id' => 'UrlUnsubscribeNewsletter',
            'title' => __('Url for unsubscribe from newsletter', 'beyondconnect'),
            'callback' => array($this->callbacks, 'textField'),
            'page' => 'beyondconnect_options_page',
            'section' => 'beyondconnect_options_section',
            'args' => array(
                'option_name' => 'beyondconnect_option',
                'label_for' => 'UrlUnsubscribeNewsletter',
                'required' => false
            )
        );
        $args[] = array(
            'id' => 'UrlAddContinuation',
            'title' => __('Url for adding registration continuation', 'beyondconnect'),
            'callback' => array($this->callbacks, 'textField'),
            'page' => 'beyondconnect_options_page',
            'section' => 'beyondconnect_options_section',
            'args' => array(
                'option_name' => 'beyondconnect_option',
                'label_for' => 'UrlAddContinuation',
                'required' => false
            )
        );

        $this->settings->setFields($args);
    }
}