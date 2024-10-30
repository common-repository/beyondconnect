<?php

namespace Inc\Base;

use Inc\Beyond;

final class NinjaFormsActions_AddAddressEmailNewsletter extends NinjaFormsBaseAction
{
    protected $_name = 'BC_AddAddressEmailNewsletter';
    protected $_timing = 'late';
    protected $_priority = '0';

    public function __construct()
    {
        parent::__construct();
        $this->_nicename = __('BC Add Email Newsletter', 'beyondconnect');

        $settings = include 'NinjaFormsAddAddressEmailNewsletterActionSettings.php';

        $this->_settings = array_merge($this->_settings, $settings);
    }

    public function process($action_settings, $form_id, $data)
    {
        global $bc_global;

        $adresseEmailNewsletter = $this->getLinkedFieldValues($action_settings, $form_id, $data);

        do_action('subscribeToNewsletter', $adresseEmailNewsletter);

        if (is_wp_error($bc_global['bc_subscribeToNewsletter'])
            || empty($bc_global['bc_subscribeToNewsletter']['adressenEMailsNewsletterRowguid'])
            || !Beyond::isGuid($bc_global['bc_subscribeToNewsletter']['adressenEMailsNewsletterRowguid']))
        {
            $data['errors']['form']['adresseEmailNewsletter'] = __('Error saving address email newsletter', 'beyondconnect') . $bc_global['bc_subscribeToNewsletter'];
        }
        else
            {
            $adresseEmailNewsletter['adressenEMailsNewsletterRowguid'] = $bc_global['bc_subscribeToNewsletter']['adressenEMailsNewsletterRowguid'];

            if (empty($data['bcactionresults']['adressenemailsnewsletter'])) {
                $adressenEmailsNewsletter = array();
            } else {
                $adressenEmailsNewsletter = json_decode($data['bcactionresults']['adressenemailsnewsletter'], true);
            }

            array_push($adressenEmailsNewsletter, $adresseEmailNewsletter);
            $data['bcactionresults']['adressenemailsnewsletter'] = json_encode($adressenEmailsNewsletter);
        }
        if ( defined( 'WP_DEBUG' ) && WP_DEBUG )
            error_log("beyondConnect: NinjaForms: AddAddressEmailNewsletter: " . print_r($data,true) . "\n", 3, $this->plugin_path . "/beyondConnect.log");
        return $data;
    }
}