<?php
/**
 * @package beyondConnectPlugin
 */

namespace Inc\Base;

class Activate
{
    public static function activate(): void {
        flush_rewrite_rules();

        $default = array();

        if (!get_option('beyondconnect_option')) {
            update_option('beyondconnect_option', $default, false);
        }
    }
}
