<?php
/**
 * @package beyondConnectPlugin
 */

namespace Inc\Base;

use Inc\Beyond;
use Inc\Base\ShortcodesBaseController;

class ShortcodesSubscriptionsController extends ShortcodesBaseController
{
    public function register()
    {
        if (!$this->activated('shortcodes_subscriptions')) return;

        parent::register();
    }

    public function setShortcodes()
    {
        $this->codes = array(
            'beyondconnect_subscriptions',
            'beyondconnect_subscriptions_element',
            'beyondconnect_subscriptions_attendances',
            'beyondconnect_subscriptions_attendances_element',
            'beyondconnect_subscriptions_list',
            'beyondconnect_subscriptions_list_element',
            'beyondconnect_subscriptionsattendances_list',
            'beyondconnect_subscriptionsattendances_list_element',
            'beyondconnect_subscriptions_list_collapsible',
            'beyondconnect_subscriptions_list_popupable',
            'beyondconnect_subscriptiontypes_list',
            'beyondconnect_subscriptiontypes_list_element',
            'beyondconnect_subscriptiontypes_list_button_addtocart',
        );
    }

    public function beyondconnect_subscriptions($atts = [], $content = null, $tag = '')
    {
        $wporg_atts = $this->getShortcodeAtts($atts, $tag);

        $o = $this->execDBShortcode($content, $wporg_atts, 'abonnemente', 'value');
        $o = $this->replaceFormula($o);
        return $o;
    }

    public function beyondconnect_subscriptions_element($atts = [], $content = null, $tag = '')
    {
        $wporg_atts = $this->getShortcodeAtts($atts, $tag);

        $o = $this->execElementShortcode($content, $wporg_atts, 'bc_element subscriptions', 'title');
        $o = $this->replaceFormula($o);
        return $o;
    }

    public function beyondconnect_subscriptions_attendances($atts = [], $content = null, $tag = '')
    {
        $wporg_atts = $this->getShortcodeAtts($atts, $tag);

        $o = $this->execDBShortcode($content, $wporg_atts, 'abonnementeAnwesenheiten', 'value');
        $o = $this->replaceFormula($o);
        return $o;
    }

    public function beyondconnect_subscriptions_attendances_element($atts = [], $content = null, $tag = '')
    {
        $wporg_atts = $this->getShortcodeAtts($atts, $tag);

        $o = $this->execElementShortcode($content, $wporg_atts, 'bc_element subscriptions_attendances', 'title');
        $o = $this->replaceFormula($o);
        return $o;
    }

    public function beyondconnect_subscriptions_list($atts = [], $content = null, $tag = '')
    {
        $o = $this->execListShortcode($atts, $content, $tag, 'subscriptions', 'abonnemente', 'abonnementeRowguid');
        $o = $this->replaceFormula($o);
        $o = do_shortcode($o);
        return $o;
    }

    public function beyondconnect_subscriptions_list_element($atts = [], $content = null, $tag = '')
    {
        $wporg_atts = $this->getShortcodeAtts($atts, $tag);

        global $bc_global;

        $bc_global['bc_subscriptions_list_element_array'][] = array_merge($wporg_atts, array('content' => trim($content)));

        return '';
    }

    public function beyondconnect_subscriptiontypes_list($atts = [], $content = null, $tag = '')
    {
        $o = $this->execListShortcode($atts, $content, $tag, 'subscriptiontypes', 'abonnementetypen', 'abonnementeTypenId');
        $o = $this->replaceFormula($o);
        $o = do_shortcode($o);
        return $o;
    }

    public function beyondconnect_subscriptiontypes_list_element($atts = [], $content = null, $tag = '')
    {
        $wporg_atts = $this->getShortcodeAtts($atts, $tag);

        global $bc_global;

        $bc_global['bc_subscriptiontypes_list_element_array'][] = array_merge($wporg_atts, array('content' => trim($content)));

        return '';
    }

    public function beyondconnect_subscriptions_list_collapsible($atts = [], $content = null, $tag = '')
    {
        $wporg_atts = $this->getShortcodeAtts($atts, $tag);
        $o = '';

        if ($wporg_atts['abonnementerowguid'] != null)
            $content = str_ireplace('%AbonnementeRowguid%', $wporg_atts['abonnementerowguid'], $content);

        $o .= do_shortcode($content);

        $o = $this->replaceFormula($o);
        return $o;
    }

    public function beyondconnect_subscriptiontypes_list_button_addtocart($atts = [], $content = null, $tag = '')
    {
        // normalize attribute keys, lowercase
        $wporg_atts = $this->getShortcodeAtts($atts, $tag);

        $o = '';

        $o .= '<form action="' . admin_url() . '/admin-post.php/" method="post">';
        $o .= '<input type="hidden" name="action" value="bc_buyaddSubscriptionToCart">';
        $o .= '<input type="hidden" name="abonnementeTypenId" value="' . $wporg_atts['abonnementetypenid'] . '">';
        $o .= '<input type="hidden" name="redirect" value="' . bc_getUrl($wporg_atts['redirect']) . '">';
        $o .= '<button type="submit" id="submit" class="bc_button buyaddtocart" >' . (empty($wporg_atts['text']) ? 'kaufen' : $wporg_atts['text']) . '</button>';
        $o .= '</form>';

        return $o;
    }

    public function beyondconnect_subscriptions_list_popupable($atts = [], $content = null, $tag = '')
    {
        $wporg_atts = $this->getShortcodeAtts($atts, $tag);
        $o = '';

        if ($wporg_atts['abonnementerowguid'] != null)
            $content = str_ireplace('%AbonnementeRowguid%', $wporg_atts['abonnementerowguid'], $content);

        $o .= do_shortcode($content);

        $o = $this->replaceFormula($o);
        return $o;
    }

    public function beyondconnect_subscriptionsattendances_list($atts = [], $content = null, $tag = '')
    {
        $o = $this->execListShortcode($atts, $content, $tag, 'subscriptionsattendances', 'abonnementeanwesenheiten', 'abonnementeAnwesenheitenRowguid');
        $o = $this->replaceFormula($o);
        $o = do_shortcode($o);
        return $o;
    }

    public function beyondconnect_subscriptionsattendances_list_element($atts = [], $content = null, $tag = '')
    {
        $wporg_atts = $this->getShortcodeAtts($atts, $tag);

        global $bc_global;

        $bc_global['bc_subscriptionsattendances_list_element_array'][] = array_merge($wporg_atts, array('content' => trim($content)));

        return '';
    }
}