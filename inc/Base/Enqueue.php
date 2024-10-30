<?php
/**
 * @package beyondConnectPlugin
 */

namespace Inc\Base;

use Inc\Base\BaseController;

/**
 *
 */
class Enqueue extends BaseController
{
    public function register()
    {
        add_action('wp_enqueue_scripts', array($this, 'enqueueFrontEnd'));
        add_action('admin_enqueue_scripts', array($this, 'enqueueBackEnd'));
        add_action('plugins_loaded', array($this, 'loadTextDomain'));
    }

    function loadTextDomain()
    {
        $result = load_plugin_textdomain('beyondconnect', false, $this->plugin_language_path);
    }

    function enqueueBackEnd()
    {
        wp_enqueue_style('beyondconnect_backend_style', $this->plugin_url . 'assets/beyondconnect_backend.css');
        wp_enqueue_script('beyondconnect_backend_script', $this->plugin_url . 'assets/beyondconnect_backend.js', array('jquery'), null, false);
    }

    function enqueueFrontEnd()
    {
        wp_enqueue_style('beyondconnect_style', $this->plugin_url . 'assets/beyondconnect.css');
        wp_enqueue_script('beyondconnect_script', $this->plugin_url . 'assets/beyondconnect.js', array('jquery'), null, false);
    }
}