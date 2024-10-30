<?php
/**
 * @package beyondConnectPlugin
 */

namespace Inc\Base;

use Inc\Beyond;
use Inc\Base\ShortcodesBaseController;

class ShortcodesHelpersController extends ShortcodesBaseController
{
    public function register()
    {
        if (!$this->activated('shortcodes_helpers')) return;
        parent::register();
    }


    public function setShortcodes()
    {
        $this->codes = array(
            'beyondconnect_setglobals',
            'beyondconnect_getglobals',
        );
    }

    public function beyondconnect_setglobals($atts = [], $content = null, $tag = '')
    {
        global $bc_global;

        $atts = array_change_key_case((array)$atts, CASE_LOWER);

        foreach ($atts as $key => $value) {
            $value = $this->replaceQueryStringValues($value);
        }

        if (empty($bc_global['bc_shortcode']))
            $bc_global['bc_shortcode'] = $atts;
        else
            $bc_global['bc_shortcode'] = array_replace($bc_global['bc_shortcode'], $atts);

        $content = do_shortcode($content, false);

        return $content;
    }

    public function beyondconnect_getglobals($atts = [], $content = null, $tag = '')
    {
        global $bc_global;

        $p = $atts[0];

        if (empty($p))
            return "please define param";

        if (empty($bc_global['bc_shortcode']))
            return "param " . $p . " not found";

        foreach ($bc_global['bc_shortcode'] as $key => $value) {
            if (strcasecmp($key, $p) === 0)
                $c = $value;
        }

        if (empty($c))
            return "param " . $p . " not found";

        return $c;
    }
}