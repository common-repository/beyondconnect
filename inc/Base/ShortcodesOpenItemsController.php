<?php
/**
 * @package beyondConnectPlugin
 */

namespace Inc\Base;

use Inc\Beyond;
use Inc\Base\ShortcodesBaseController;

class ShortcodesOpenItemsController extends ShortcodesBaseController
{
    public function register()
    {
        if (!$this->activated('shortcodes_openitems')) return;
        parent::register();
    }


    public function setShortcodes()
    {
        $this->codes = array(
            'beyondconnect_openitems_list',
            'beyondconnect_openitems_list_element',
            'beyondconnect_openitems_list_button_addtocart',
        );
    }


    public function beyondconnect_openitems_list($atts = [], $content = null, $tag = '')
    {
        $o = $this->execListShortcode($atts, $content, $tag, 'openitems', 'offeneposten', 'offenepostenRowguid');
        $o = $this->replaceFormula($o);
        $o = do_shortcode($o);
        return $o;
    }

    public function beyondconnect_openitems_list_element($atts = [], $content = null, $tag = '')
    {
        $wporg_atts = $this->getShortcodeAtts($atts, $tag);

        global $bc_global;

        //$bc_global['bc_openitems_list_element_array'][] = array_merge($wporg_atts, array('content' => trim(do_shortcode($content))));
        $bc_global['bc_openitems_list_element_array'][] = array_merge($wporg_atts, array('content' => trim($content)));

        return '';
    }

    public function beyondconnect_openitems_list_button_addtocart($atts = [], $content = null, $tag = '')
    {
        // normalize attribute keys, lowercase
        $wporg_atts = $this->getShortcodeAtts($atts, $tag);

        $o = '';

        if (empty($wporg_atts['offenepostenrowguid'])) {
            return $o;
        }

        $exists = false;
        if (get_transient('bc_cart' . Beyond::getVisitorID()) !== false) {
            //Rowguid in Cart vorhanden
            $bc_cart = get_transient('bc_cart' . Beyond::getVisitorID());
            $key = array_search($wporg_atts['offenepostenrowguid'], array_column($bc_cart, 'offenePostenRowguid'));
            if ($key !== false) {
                $exists = true;
            }
        }

        $o .= '<form action="' . admin_url() . '/admin-post.php/" method="post">';
        $o .= '<input type="hidden" name="action" value="bc_addToCart">';
        $o .= '<input type="hidden" name="offenePostenRowguid" value="' . $wporg_atts['offenepostenrowguid'] . '">';
        $o .= '<input type="hidden" name="bezeichnung" value="' . $wporg_atts['bezeichnung'] . '">';
        $o .= '<input type="hidden" name="betrag" value="' . $wporg_atts['betrag'] . '">';
        $o .= '<input type="hidden" name="type" value="' . (empty($wporg_atts['type']) ? 'payment' : $wporg_atts['type']) . '">';
        $o .= '<input type="hidden" name="redirect" value="' . Beyond::getUrl($wporg_atts['redirect']) . '">';
        $o .= '<button type="submit" id="submit" class="bc_button addtocart" ' . ($exists ? 'disabled="true"' : '') . ' >' . (empty($wporg_atts['text']) ? 'zahlen' : $wporg_atts['text']) . '</button>';
        $o .= '</form>';

        return $o;
    }
}