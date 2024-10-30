<?php
/**
 * @package beyondConnectPlugin
 */

namespace Inc\Base;

use Inc\Beyond;
use Inc\Base\ShortcodesBaseController;

class ShortcodesPaymentsController extends ShortcodesBaseController
{
    public function register()
    {
        if (!$this->activated('shortcodes_payments')) return;
        parent::register();
    }


    public function setShortcodes()
    {
        $this->codes = array(


            'beyondconnect_cart_button_emptycart',
            'beyondconnect_cart_button_paycart',
            'beyondconnect_cart_list',
            'beyondconnect_cart_list_element',
            'beyondconnect_cart_list_button_removefromcart',
            'beyondconnect_saferpay_iframe',
        );
    }

    public function beyondconnect_cart_list_button_removefromcart($atts = [], $content = null, $tag = '')
    {
        // normalize attribute keys, lowercase
        $wporg_atts = $this->getShortcodeAtts($atts, $tag);

        $o = '';

        $o .= '<form action="' . admin_url() . '/admin-post.php/" method="post">';
        $o .= '<input type="hidden" name="action" value="bc_removeFromCart">';
        $o .= '<input type="hidden" name="offenePostenRowguid" value="' . $wporg_atts['offenepostenrowguid'] . '">';
        $o .= '<input type="hidden" name="redirect" value="' . bc_getUrl($wporg_atts['redirect']) . '">';
        $o .= '<button type="submit" id="submit" class="bc_button removefromcart">' . (empty($wporg_atts['text']) ? 'l&ouml;schen' : $wporg_atts['text']) . '</button>';
        $o .= '</form>';

        return $o;
    }

    public function beyondconnect_cart_button_emptycart($atts = [], $content = null, $tag = '')
    {
        // normalize attribute keys, lowercase
        $wporg_atts = $this->getShortcodeAtts($atts, $tag);

        $exists = false;
        if (get_transient('bc_cart' . Beyond::getVisitorID()) !== false) {
            //eine Position in Cart vorhanden
            $bc_cart = get_transient('bc_cart' . Beyond::getVisitorID());
            if (count($bc_cart) > 0) {
                $exists = true;
            }
        }

        if (!empty($wporg_atts['hide']) && $wporg_atts['hide'] === 'true' && !$exists)
            return '';

        $o = '';

        $o .= '<form action="' . admin_url() . '/admin-post.php/" method="post">';
        $o .= '<input type="hidden" name="action" value="bc_emptyCart">';
        $o .= '<input type="hidden" name="redirect" value="' . bc_getUrl($wporg_atts['redirect']) . '">';
        $o .= '<button type="submit" id="submit" class="bc_button emptycart" ' . ($exists ? '' : 'disabled="true"') . ' >' . (empty($wporg_atts['text']) ? 'alle l&ouml;schen' : $wporg_atts['text']) . '</button>';
        $o .= '</form>';

        return $o;
    }

    public function beyondconnect_cart_button_paycart($atts = [], $content = null, $tag = '')
    {
        $option = get_option('beyondconnect_option');

        $initSuccess = $option['PG_InitSuccess'];
        $initFail = $option['PG_InitFail'];
        $paymentSuccess = $option['PG_PaymentSuccess'];
        $paymentFail = $option['PG_PaymentFail'];

        // normalize attribute keys, lowercase
        $wporg_atts = $this->getShortcodeAtts($atts, $tag);

        $exists = false;
        if (get_transient('bc_cart' . Beyond::getVisitorID()) !== false) {
            //eine Position in Cart vorhanden
            $bc_cart = get_transient('bc_cart' . Beyond::getVisitorID());
            if (count($bc_cart) > 0) {
                $exists = true;
            }
        }

        if (!empty($wporg_atts['hide']) && $wporg_atts['hide'] === 'true' && !$exists)
            return '';

        $o = '';

        $o .= '<form action="' . admin_url() . '/admin-post.php/" method="post">';
        $o .= '<input type="hidden" name="action" value="bc_saferpayInitialize">';
        $o .= '<input type="hidden" name="initSuccessUrl" value="' . (empty($initSuccess) ? '' : bc_getUrl($initSuccess)) . '">';
        $o .= '<input type="hidden" name="initFailUrl" value="' . (empty($initFail) ? '' : bc_getUrl($initFail)) . '">';
        $o .= '<input type="hidden" name="paymentSuccessUrl" value="' . (empty($paymentSuccess) ? '' : bc_getUrl($paymentSuccess)) . '">';
        $o .= '<input type="hidden" name="paymentFailUrl" value="' . (empty($paymentFail) ? '' : bc_getUrl($paymentFail)) . '">';
        $o .= '<input type="hidden" name="language" value="' . Beyond::getLanguage() . '">';
        $o .= '<button type="submit" id="submit" class="bc_button paycart" ' . ($exists ? '' : 'disabled="true"') . ' >' . (empty($wporg_atts['text']) ? 'zur Kasse' : $wporg_atts['text']) . '</button>';
        $o .= '</form>';

        return $o;
    }

    public function beyondconnect_cart_list($atts = [], $content = null, $tag = '')
    {
        $o = $this->execListShortcode($atts, $content, $tag, 'cart', 'warenkorb', 'offenePostenRowguid');
        $o = $this->replaceFormula($o);
        $o = do_shortcode($o);
        return $o;
    }

    public function beyondconnect_cart_list_element($atts = [], $content = null, $tag = '')
    {
        $wporg_atts = $this->getShortcodeAtts($atts, $tag);

        global $bc_global;

        $bc_global['bc_cart_list_element_array'][] = array_merge($wporg_atts, array('content' => trim($content)));

        return '';
    }

    public function beyondconnect_saferpay_iframe($atts = [], $content = null, $tag = '')
    {
        // normalize attribute keys, lowercase
        $wporg_atts = $this->getShortcodeAtts($atts, $tag);

        if (get_transient('bc_saferpay') === false || empty($_GET['requestId'])) {
            return '';
        }

        $bc_saferpay = get_transient('bc_saferpay');

        $key = array_search($_GET['requestId'], array_column($bc_saferpay, 'requestId'));

        if ($key === false)
            return '';

        $saferpayResponse = $bc_saferpay[$key];

        if (empty($saferpayResponse['returnUrl']))
            return '';

        return '<iframe src= "' . $saferpayResponse['returnUrl'] . '" height="900" > </iframe>';
    }

}