<?php


namespace Inc\Base;


class SaferpayRequestHeader
{
    public $SpecVersion = '1.18';
    public $CustomerId;
    public $RequestId;
    public $RetryIndicator = 0;
}