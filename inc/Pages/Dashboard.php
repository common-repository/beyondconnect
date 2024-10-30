<?php
/**
 * @package beyondConnectPlugin
 */

namespace Inc\Pages;

use Inc\Api\SettingsApi;
use Inc\Api\Callbacks\DashboardCallbacks;
use Inc\Base\BaseController;

class Dashboard extends BaseController
{
    public $settings;

    public $callbacks;

    public $callbacks_mngr;

    public $pages = array();

    public function register()
    {
        $this->settings = new SettingsApi();

        $this->callbacks = new DashboardCallbacks();

        $this->setPages();

        $this->settings->addPages($this->pages)->withSubPage(__('Dashboard', 'beyondconnect'))->register();
    }

    public function setPages()
    {
        $this->pages = array(
            array(
                'page_title' => 'BeyondConnect',
                'menu_title' => 'BeyondConnect',
                'capability' => 'manage_options',
                'menu_slug' => 'beyondconnect',
                'callback' => array($this->callbacks, 'DashboardPage'),
                'icon_url' => 'dashicons-cloud',
                'position' => 110
            )
        );
    }
}