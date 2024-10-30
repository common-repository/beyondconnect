<?php


namespace Inc\Base;


class SaferpayAssertRequest
{
    public $RequestHeader;
    public $Token;


    function __construct()
    {
        //Read Settings
        $option = get_option('beyondconnect_option');

        $customerId = $option['PG_CustomerId'];

        //Initialize
        $this->RequestHeader = new SaferpayRequestHeader();
        $this->RequestHeader->CustomerId = $customerId;
    }

}

