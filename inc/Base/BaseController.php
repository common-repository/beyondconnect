<?php
/**
 * @package beyondConnectPlugin
 */

namespace Inc\Base;

class BaseController
{
    public $plugin_path;

    public $plugin_url;

    public $plugin;

    public $widgets = array();
    public $shortcodes = array();

    public function __construct()
    {
        $this->plugin_path = plugin_dir_path(dirname(__FILE__, 2));
        $this->plugin_url = plugin_dir_url(dirname(__FILE__, 2));
        $this->plugin = plugin_basename(dirname(__FILE__, 3)) . '/beyondConnect.php';
        $this->plugin_language_path = plugin_basename(dirname(__FILE__, 3) . '/languages');

        $this->widgets = array(
            'teachers_widget' => __('Activate Teachers Widget', 'beyondconnect'),
        );
        $this->shortcodes = array(
            'shortcodes_helpers' => __('Activate Shortcodes Helpers', 'beyondconnect'),
            'shortcodes_addresses' => __('Activate Shortcodes Addresses', 'beyondconnect'),
            'shortcodes_courses' => __('Activate Shortcodes Courses', 'beyondconnect'),
            'shortcodes_openitems' => __('Activate Shortcodes Open Items', 'beyondconnect'),
            'shortcodes_payments' => __('Activate Shortcodes Payments', 'beyondconnect'),
            'shortcodes_registrations' => __('Activate Shortcodes Registrations', 'beyondconnect'),
            'shortcodes_subscriptions' => __('Activate Shortcodes Subscriptions', 'beyondconnect'),
        );
    }

    public function activated(string $key)
    {
        $option = get_option('beyondconnect_option');

        return isset($option[$key]) ? $option[$key] : false;
    }

}