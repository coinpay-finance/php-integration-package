<?php

namespace Coinpay\Finance;

class PaymentResponse
{
    public $url = "";
    public $transactionId = null;

    public function __construct(string $url, string $transactionId) {
        $this->url = $url;
        $this->transactionId = $transactionId;
    }
}