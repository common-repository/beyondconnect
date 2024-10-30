<?php

/**
 * @package beyondConnectPlugin
 */

namespace Inc\Base;

use Inc\Beyond;
use Inc\Base\BaseController;


/**
 *
 */
class AddressController extends BaseController
{
    public function __construct()
    {
    }

    public function register()
    {
        $option = get_option('beyondconnect_option');

        $this->addActions();
    }

    public function addActions()
    {
        add_action('wp', array($this, 'wpAction'));
        add_action('bc_subscribeToNewsletter', array($this, 'subscribeToNewsletter'), 10, 1);
        add_action('bc_unsubscribeFromNewsletter', array($this, 'unsubscribeFromNewsletter'), 10, 1);

    }

    public function wpAction()
    {
        //Read Settings
        $option = get_option('beyondconnect_option');

        if (empty($option['UrlUnsubscribeNewsletter']))
            return;

        $UrlUnsubscribeNewsletter = $option['UrlUnsubscribeNewsletter'];

        //Get PageUrl
        $pageUrl = 'https://' . $_SERVER['HTTP_HOST'] . parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);

        if (in_array($pageUrl, Beyond::getUrls($UrlUnsubscribeNewsletter)) && !empty($_GET['adressenEMailsNewsletterRowguid']))
        {
            do_action('bc_unsubscribeFromNewsletter', $_GET['adressenEMailsNewsletterRowguid']);
        }

    }

    public function subscribeToNewsletter($adresseEmailNewsletter)
    {
        global $bc_global;

        if (empty($adresseEmailNewsletter))
            $bc_global['bc_subscribeToNewsletter'] = 'No data passed to action';
        else
            $bc_global['bc_subscribeToNewsletter'] = Beyond::setValues('AdressenEmailsNewsletter', 'POST', $adresseEmailNewsletter, '', false);
    }

    public function unsubscribeFromNewsletter($adressenEMailsNewsletterRowguid)
    {
        global $bc_global;

        if (empty($adressenEMailsNewsletterRowguid))
            $bc_global['bc_unsubscribeFromNewsletter'] = 'No data passed to action';
        else if (!Beyond::isGuid($adressenEMailsNewsletterRowguid))
            $bc_global['bc_unsubscribeFromNewsletter'] = 'Wrong data passed to action';
        else
            $bc_global['bc_unsubscribeFromNewsletter'] = Beyond::setValues('adressenEmailsNewsletter(' . $adressenEMailsNewsletterRowguid . ')', 'DELETE', array(), '', true);
    }
}