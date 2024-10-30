<?php


namespace Inc\Base;


class SaferpayCaptureRequest
{
    public $RequestHeader;
    public $TransactionReference;


    function __construct()
    {
        //Read Settings
        $option = get_option('beyondconnect_option');

        $customerId = $option['PG_CustomerId'];
        $terminalId = $option['PG_TerminalId'];

        //Initialize
        $this->TerminalId = $terminalId;

        $this->RequestHeader = new SaferpayRequestHeader();
        $this->RequestHeader->CustomerId = $customerId;

        $this->TransactionReference = new TransactionReference();
    }

}

class TransactionReference
{
    public $TransactionId;
}

