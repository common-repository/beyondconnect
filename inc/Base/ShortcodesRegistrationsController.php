<?php
/**
 * @package beyondConnectPlugin
 */

namespace Inc\Base;

use Inc\Beyond;
use Inc\Base\ShortcodesBaseController;

class ShortcodesRegistrationsController extends ShortcodesBaseController
{
    public function register()
    {
        if (!$this->activated('shortcodes_registrations')) return;

        parent::register();
    }

    public function setShortcodes()
    {
        $this->codes = array(
            'beyondconnect_registrations_list',
            'beyondconnect_registrations_list_element',
        );
    }


    public function beyondconnect_registrations_list($atts = [], $content = null, $tag = '')
    {
        $o = $this->execListShortcode($atts, $content, $tag, 'registrations', 'anmeldungen', 'person1Rowguid');
        $o = $this->replaceFormula($o);
        $o = do_shortcode($o);
        return $o;
    }

    public function beyondconnect_registrations_list_element($atts = [], $content = null, $tag = '')
    {
        $wporg_atts = $this->getShortcodeAtts($atts, $tag);

        global $bc_global;

        //$bc_global['bc_registrations_list_element_array'][] = array_merge($wporg_atts, array('content' => trim(do_shortcode($content))));
        $bc_global['bc_registrations_list_element_array'][] = array_merge($wporg_atts, array('content' => trim($content)));

        return '';
    }

}