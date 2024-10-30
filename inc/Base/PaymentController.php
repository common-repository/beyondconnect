<?php

/**
 * @package beyondConnectPlugin
 */

namespace Inc\Base;

use Inc\Beyond;
use Inc\Base\BaseController;


/**
 *
 */
class PaymentController extends BaseController
{
    public function __construct()
    {
    }

    public function register()
    {
        $option = get_option('beyondconnect_option');

        if (empty($option['PG_Url']) || empty($option['PG_Username']) || empty($option['PG_Password']))
            return;

        $this->addActions();
    }

    public function addActions()
    {
        //Actions for admin-post.php
        //Actions for logged users
        add_action('admin_post_bc_addToCart', array($this, 'addToCart'));
        add_action('admin_post_bc_buyaddSubscriptionToCart', array($this, 'buyaddSubscriptionToCart'));
        add_action('admin_post_bc_removeFromCart', array($this, 'removeFromCart'));
        add_action('admin_post_bc_emptyCart', array($this, 'emptyCart'));
        add_action('admin_post_bc_saferpayInitialize', array($this, 'saferpayInitialize'));

        //Actions for not logged users
        add_action('admin_post_nopriv_bc_addToCart', array($this, 'addToCart'));
        add_action('admin_post_nopriv_bc_buyaddSubscriptionToCart', array($this, 'buyaddSubscriptionToCart'));
        add_action('admin_post_nopriv_bc_removeFromCart', array($this, 'removeFromCart'));
        add_action('admin_post_nopriv_bc_emptyCart', array($this, 'emptyCart'));
        add_action('admin_post_nopriv_bc_saferpayInitialize', array($this, 'saferpayInitialize'));

        //other Actions
        add_action('wp', array($this, 'wpAction'));
        add_action('bc_processPayment', array($this, 'processPayment'));
        add_action('bc_saferpayAssert', array($this, 'saferpayAssert'));
        add_action('bc_saferpayCapture', array($this, 'saferpayCapture'));
    }

    public function wpAction()
    {
        //Read Settings
        $option = get_option('beyondconnect_option');
        $paymentNotify = $option['PG_PaymentNotify'];

        if (empty($option['PG_PaymentNotify']))
            return;

        //Get PageUrl
        $pageUrl = 'https://' . $_SERVER['HTTP_HOST'] . parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);

        if (in_array($pageUrl, Beyond::getUrls($paymentNotify)))
            do_action('bc_processPayment');
    }

    public function processPayment()
    {
        do_action("bc_saferpayAssert");

        if (get_transient('bc_saferpay') === false || empty($_GET['requestId'])) {
            return '';
        }

        $bc_saferpay = get_transient('bc_saferpay');

        $key = array_search($_GET['requestId'], array_column($bc_saferpay, 'requestId'));

        if ($key === false)
            return '';

        $saferpayResponse = $bc_saferpay[$key];

        //keine Zahlung, nur Reservation
        if (empty($saferpayResponse['transactionId']) ||
            $saferpayResponse['transactionStatus'] !== 'AUTHORIZED' ||
            $saferpayResponse['type'] !== 'payment') {
            return;
        }

        do_action('bc_saferpayCapture');
        do_action("bc_saferpayAssert");
    }


    public function addToCart()
    {
        if (empty($_POST["offenePostenRowguid"])) {
            wp_safe_redirect($_POST["redirect"]);
            return;
        }

        $cartItem['offenePostenRowguid'] = $_POST['offenePostenRowguid'];
        $cartItem['bezeichnung'] = $_POST['bezeichnung'];
        $cartItem['betrag'] = $_POST['betrag'];
        $cartItem['type'] = $_POST['type'];

        if (get_transient('bc_cart' . Beyond::getVisitorID()) === false) {
            $bc_cart = array();
        } else {
            $bc_cart = get_transient('bc_cart' . Beyond::getVisitorID());
        }

        $key = array_search($_POST['offenePostenRowguid'], array_column($bc_cart, 'offenePostenRowguid'));

        //Rowguid bereits in Cart vorhanden
        if ($key !== false) {
            wp_safe_redirect($_POST["redirect"]);
            return;
        }

        $bc_cart[] = $cartItem;

        set_transient('bc_cart' . Beyond::getVisitorID(), $bc_cart, HOUR_IN_SECONDS);

        wp_safe_redirect($_POST["redirect"]);
    }

    public function buyaddSubscriptionToCart()
    {
        if (!isset(wp_get_current_user()->user_login)) {
            wp_die('User not logged in');
        }

        $abonnement = array();
        $abonnement['adressenRowguid'] = wp_get_current_user()->user_login;
        $abonnement['abonnementeTypenID'] = $_POST['abonnementeTypenId'];

        $response = Beyond::setValues('abonnemente', 'POST', $abonnement, 'offenePostenRowguid', false);

        if (is_wp_error($response) || !Beyond::isGuid($response)) {
            wp_safe_redirect($_POST["redirect"]);
            return;
        }

        $offenerPosten = Beyond::getValues('OffenePosten(' . $response . ')', '');

        if (empty($offenerPosten[0]["offenePostenRowguid"])) {
            wp_safe_redirect($_POST["redirect"]);
            return;
        }

        $cartItem['offenePostenRowguid'] = $offenerPosten[0]['offenePostenRowguid'];
        $cartItem['bezeichnung'] = $offenerPosten[0]['bezeichnung'];
        $cartItem['betrag'] = $offenerPosten[0]['betrag'];
        $cartItem['type'] = 'payment';

        if (get_transient('bc_cart' . Beyond::getVisitorID()) === false) {
            $bc_cart = array();
        } else {
            $bc_cart = get_transient('bc_cart' . Beyond::getVisitorID());
        }

        $key = array_search($_POST['offenePostenRowguid'], array_column($bc_cart, 'offenePostenRowguid'));

        //Rowguid bereits in Cart vorhanden
        if ($key !== false) {
            wp_safe_redirect($_POST["redirect"]);
            return;
        }

        $bc_cart[] = $cartItem;

        set_transient('bc_cart' . Beyond::getVisitorID(), $bc_cart, HOUR_IN_SECONDS);

        wp_safe_redirect($_POST["redirect"]);
    }

    public function removeFromCart()
    {
        //keine Rowguid übergeben
        if (empty($_POST["offenePostenRowguid"])) {
            wp_safe_redirect($_POST["redirect"]);
            return;
        }

        //kein Cart vorhanden
        if (get_transient('bc_cart' . Beyond::getVisitorID()) === false) {
            wp_safe_redirect($_POST["redirect"]);
            return;
        }

        $bc_cart = get_transient('bc_cart' . Beyond::getVisitorID());

        $key = array_search($_POST['offenePostenRowguid'], array_column($bc_cart, 'offenePostenRowguid'));

        //Rowguid nicht in Cart vorhanden
        if ($key === false) {
            wp_safe_redirect($_POST["redirect"]);
            return;
        }

        array_splice($bc_cart, $key, 1);
        set_transient('bc_cart' . Beyond::getVisitorID(), $bc_cart, HOUR_IN_SECONDS);

        wp_safe_redirect($_POST["redirect"]);
    }

    public function emptyCart()
    {
        delete_transient('bc_cart' . Beyond::getVisitorID());

        wp_safe_redirect($_POST["redirect"]);
    }

    public function saferpayInitialize()
    {
        $option = get_option('beyondconnect_option');

        $url = $option['PG_Url'];
        $username = $option['PG_Username'];
        $password = $option['PG_Password'];
        $initSuccess = empty($_POST["initSuccessUrl"]) ? $option['PG_InitSuccess'] : $_POST["initSuccessUrl"];
        $initFail = empty($_POST["initFailUrl"]) ? $option['PG_InitFail'] : $_POST["initFailUrl"];
        $paymentSuccess = empty($_POST["paymentSuccessUrl"]) ? $option['PG_PaymentSuccess'] : $_POST["paymentSuccessUrl"];
        $paymentFail = empty($_POST["paymentFailUrl"]) ? $option['PG_PaymentFail'] : $_POST["paymentFailUrl"];

        $header = array(
            'Content-Type' => 'application/json',
            'Authorization' => 'Basic ' . base64_encode($username . ':' . $password),
        );

        $requestId = Beyond::createGUID();

        //Kein Cart vorhanden
        if (get_transient('bc_cart' . Beyond::getVisitorID()) === false) {
            wp_safe_redirect($initFail);
            return;
        }

        $bc_cart = get_transient('bc_cart' . Beyond::getVisitorID());

        //Cart hat keine Elemente
        if (count($bc_cart) == 0) {
            wp_safe_redirect($initFail);
            return;
        }

        $betrag = 0;
        $description = '';
        $type = '';
        foreach ($bc_cart as $cartitem) {
            $betrag += floatval($cartitem['betrag']);
            $type = $cartitem['type'];
            $description .= (empty($description) ? '' : ' / ') . $cartitem['bezeichnung'];

        }

        $body = new SaferpayInitRequest();
        $body->RequestHeader->RequestId = $requestId;
        $body->Payment->Amount->Value = $betrag * 100;
        $body->Payment->OrderId = $requestId;
        $body->Payer->LanguageCode = empty($_POST["language"]) ? 'de' : $_POST["language"];
        $body->Notification->NotifyUrl .= '?requestId=' . $requestId;
        $body->ReturnUrls->Success = $paymentSuccess;
        $body->ReturnUrls->Fail = $paymentFail;
        $body->Payment->Description = substr($description, 0, 1000);

        $defaults = array(
            'method' => 'POST',
            'headers' => $header,
            'body' => json_encode($body),
        );

        $response = wp_remote_post($url . 'Payment/v1/PaymentPage/Initialize', $defaults);

        if (is_wp_error($response)) {
            wp_die('Saferpay Initialize failed:<br />' . $response);
            return;
        }

        $jsonRaw = json_decode(wp_remote_retrieve_body($response), true);

        if (empty($jsonRaw['RedirectUrl']) || empty($jsonRaw['Token'])) {
            wp_safe_redirect($initFail);
            return;
        }

        $returnUrl = $jsonRaw['RedirectUrl'];
        $token = $jsonRaw['Token'];

        if (get_transient('bc_saferpay') === false) {
            $bc_saferpay = array();
        } else {
            $bc_saferpay = get_transient('bc_saferpay');
        }
        $saferpayResponse = array();
        $saferpayResponse['requestId'] = $requestId;
        $saferpayResponse['type'] = $type;
        $saferpayResponse['returnUrl'] = $returnUrl;
        $saferpayResponse['token'] = $token;

        $bc_saferpay[] = $saferpayResponse;

        set_transient('bc_saferpay', $bc_saferpay, HOUR_IN_SECONDS);

        $paymentrequest = array();
        $paymentrequest["zahlungsAnforderungsRowguid"] = $requestId;

        $response = Beyond::setValues('ZahlungsAnforderungen', 'POST', $paymentrequest, '', true);

        foreach ($bc_cart as $cartitem) {
            $offenerPosten = array();
            $offenerPosten['zahlungsAnforderungsRowguid'] = $requestId;
            $response = Beyond::setValues('OffenePosten(' . $cartitem['offenePostenRowguid'] . ')', 'PATCH', $offenerPosten, '', true);

            if (is_wp_error($response)) {
                wp_safe_redirect($initFail);
                return;
            }
        }

        //Saferpay ohne iFrame anzeigen
        if (empty($initSuccess)) {
            wp_redirect($returnUrl);
        } //Saferpay als iFrame anzeigen
        else {
            wp_safe_redirect($initSuccess);
        }
    }

    public function saferpayAssert()
    {
        $option = get_option('beyondconnect_option');

        $url = $option['PG_Url'];
        $username = $option['PG_Username'];
        $password = $option['PG_Password'];

        if (get_transient('bc_saferpay') === false || empty($_GET['requestId'])) {
            return;
        }

        $bc_saferpay = get_transient('bc_saferpay');

        $key = array_search($_GET['requestId'], array_column($bc_saferpay, 'requestId'));

        if ($key === false)
            return;

        $saferpayResponse = $bc_saferpay[$key];

        //Assert Payment
        $header = array(
            'Content-Type' => 'application/json',
            'Authorization' => 'Basic ' . base64_encode($username . ':' . $password),
        );

        $body = new SaferpayAssertRequest();
        $body->RequestHeader->RequestId = $saferpayResponse['requestId'];
        $body->Token = $saferpayResponse['token'];

        $defaults = array(
            'method' => 'POST',
            'headers' => $header,
            'body' => json_encode($body),
        );

        $response = wp_remote_post($url . 'Payment/v1/PaymentPage/Assert', $defaults);

        if (is_wp_error($response)) {
            wp_die('Saferpay Assert failed:<br />' . $response);
            return;
        }

        $jsonRaw = json_decode(wp_remote_retrieve_body($response), true);


        //Update Database
        $paymentrequest = array();


        if (!empty($jsonRaw['Transaction']['Id'])) {
            $paymentrequest["transaktionsId"] = $jsonRaw['Transaction']['Id'];
            $saferpayResponse['transactionId'] = $jsonRaw['Transaction']['Id'];
        }

        if (!empty($jsonRaw['Transaction']['Type'])) {
            $paymentrequest["transaktionsTyp"] = $jsonRaw['Transaction']['Type'];
            $saferpayResponse['transactionType'] = $jsonRaw['Transaction']['Type'];
        }

        if (!empty($jsonRaw['Transaction']['Status'])) {
            $paymentrequest["transaktionsStatus"] = $jsonRaw['Transaction']['Status'];
            $saferpayResponse['transactionStatus'] = $jsonRaw['Transaction']['Status'];
        }

        if (!empty($jsonRaw['PaymentMeans']['Brand']['PaymentMethod'])) {
            $paymentrequest["zahlungsMethode"] = $jsonRaw['PaymentMeans']['Brand']['PaymentMethod'];
            $saferpayResponse['paymentMethod'] = $jsonRaw['PaymentMeans']['Brand']['PaymentMethod'];
        }

        if (!empty($jsonRaw['Transaction']['Date'])) {
            $paymentrequest["transaktionsDatum"] = $jsonRaw['Transaction']['Date'];
            $saferpayResponse['transactionDate'] = $jsonRaw['Transaction']['Date'];
        }

        $response = Beyond::setValues('ZahlungsAnforderungen(' . $saferpayResponse['requestId'] . ')', 'PATCH', $paymentrequest, '', true);

        array_splice($bc_saferpay, $key, 1);
        $bc_saferpay[] = $saferpayResponse;

        set_transient('bc_saferpay', $bc_saferpay, HOUR_IN_SECONDS);

        return;
    }

    public function saferpayCapture()
    {
        $option = get_option('beyondconnect_option');

        $url = $option['PG_Url'];
        $username = $option['PG_Username'];
        $password = $option['PG_Password'];

        if (get_transient('bc_saferpay') === false || empty($_GET['requestId'])) {
            return;
        }

        $bc_saferpay = get_transient('bc_saferpay');

        $key = array_search($_GET['requestId'], array_column($bc_saferpay, 'requestId'));

        if ($key === false)
            return;

        $saferpayResponse = $bc_saferpay[$key];

        $header = array(
            'Content-Type' => 'application/json',
            'Authorization' => 'Basic ' . base64_encode($username . ':' . $password),
        );

        $body = new SaferpayCaptureRequest();
        $body->RequestHeader->RequestId = $saferpayResponse['requestId'];
        $body->TransactionReference->TransactionId = $saferpayResponse['transactionId'];

        $defaults = array(
            'method' => 'POST',
            'headers' => $header,
            'body' => json_encode($body),
        );

        $response = wp_remote_post($url . 'Payment/v1/Transaction/Capture', $defaults);

        if (is_wp_error($response)) {
            wp_die('Saferpay Capture failed:<br />' . $response);
            return;
        }
    }
}