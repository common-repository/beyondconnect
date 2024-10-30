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
class RegistrationController extends BaseController
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
        add_action('bc_addContinuation', array($this, 'addContinuation'), 10, 1);
    }

    public function wpAction()
    {
        //Read Settings
        $option = get_option('beyondconnect_option');

        if (empty($option['UrlAddContinuation']))
            return;

        $UrlAddContinuation = $option['UrlAddContinuation'];

        //Get PageUrl
        $pageUrl = 'https://' . $_SERVER['HTTP_HOST'] . parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);

        if (in_array($pageUrl, Beyond::getUrls($UrlAddContinuation)) && !empty($_GET['paarRowguid']))
        {
            do_action('bc_addContinuation', $_GET['paarRowguid']);
        }

    }

    public function addContinuation($paarRowguid)
    {
        global $bc_global;

        if (empty($paarRowguid))
            $bc_global['bc_addContinuation'] = 'No data passed to action';
        else
            $bc_global['bc_addContinuation'] = Beyond::setValues('Anmeldungen/Fortsetzen(paarRowguid=' . $paarRowguid . ')', 'GET', array(), '', false);
    }
}