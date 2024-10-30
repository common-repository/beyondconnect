<?php
/**
 * @package beyondConnectPlugin
 */

namespace Inc\Base;

use Inc\Beyond;
use Inc\Base\ShortcodesBaseController;

class ShortcodesAddressesController extends ShortcodesBaseController
{
    public function register()
    {
        if (!$this->activated('shortcodes_addresses')) return;

        parent::register();
    }

    public function setShortcodes()
    {
        $this->codes = array(
            'beyondconnect_addresses',
            'beyondconnect_addresses_element',
        );
    }

    public function beyondconnect_addresses($atts = [], $content = null, $tag = '')
    {
        $wporg_atts = $this->getShortcodeAtts($atts, $tag);

        $adressenId = $wporg_atts['adressenid'];
        if (!Beyond::isGuid($adressenId))
            return '';

        $o = $this->execDBShortcode($content, $wporg_atts, 'adressen(' . $adressenId . ')');
        $o = $this->replaceFormula($o);
        return $o;
    }

    public function beyondconnect_addresses_element($atts = [], $content = null, $tag = '')
    {
        $wporg_atts = $this->getShortcodeAtts($atts, $tag);

        $o = $this->execElementShortcode($content, $wporg_atts, 'bc_element addresses', 'title');

        $o = $this->replaceFormula($o);

        return $o;
    }
}