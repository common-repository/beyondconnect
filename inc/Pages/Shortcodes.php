<?php
/**
 * @package beyondConnectPlugin
 */

namespace Inc\Pages;

use Inc\Api\SettingsApi;
use Inc\Base\BaseController;
use Inc\Api\Callbacks\ShortcodesCallbacks;

class Shortcodes extends BaseController
{
    public $settings;

    public $callbacks;

    public $callbacks_mngr;

    public $pages = array();

    public function register()
    {
        $this->settings = new SettingsApi();

        $this->callbacks = new ShortcodesCallbacks();

        $this->setSubpages();

        $this->settings->addSubPages($this->subpages)->register();
    }

    public function setSubpages()
    {
        $this->subpages = array(
            array(
                'parent_slug' => 'beyondconnect',
                'page_title' => __('Shortcodes', 'beyondconnect'),
                'menu_title' => __('Shortcodes', 'beyondconnect'),
                'capability' => 'manage_options',
                'menu_slug' => 'beyondconnect_shortcodes_menu',
                'callback' => array($this->callbacks, 'ShortcodesPage')
            )
        );
    }
}