<?php


namespace Inc\Base;


class SaferpayInitRequest
{
    public $RequestHeader;
    public $TerminalId;
    public $Payment;
    public $Payer;
    public $ReturnUrls;
    public $Notification;

    function __construct()
    {
        //Read Settings
        $option = get_option('beyondconnect_option');

        $customerId = $option['PG_CustomerId'];
        $terminalId = $option['PG_TerminalId'];
        $paymentNotify = $option['PG_PaymentNotify'];

        //Initialize
        $this->TerminalId = $terminalId;

        $this->RequestHeader = new SaferpayRequestHeader();
        $this->RequestHeader->CustomerId = $customerId;

        $this->Payment = new Payment();

        $this->Payer = new Payer();

        $this->ReturnUrls = new ReturnUrls();

        $this->Notification = new Notification();
        $this->Notification->NotifyUrl = $paymentNotify;

    }

}

class ReturnUrls
{
    public $Success;
    public $Fail;
}

class Notification
{
    public $NotifyUrl;
}

class Payment
{
    public $Amount;
    public $OrderId;
    public $Description;

    function __construct()
    {
        $this->Amount = new Amount();
    }
}

class Payer
{
    public $LanguageCode;
}

class Amount
{
    public $Value;
    public $CurrencyCode = 'CHF';
}
