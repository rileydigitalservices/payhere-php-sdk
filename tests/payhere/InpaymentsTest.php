<?php

namespace Payhere;

use Payhere\HttpClient\CurlClient;

class InpaymentsTest extends TestCase
{
    public function testRequestToPay()
    {
        $coll = new Inpayments();

        $params = [
            'mobile' => "256782181656",
            'processing_number' => "ref",
            'payer_message' => "12",
            'narration' => "what is being paid for",
            'amount' => "500"];

        $t = $coll->requestToPay($params);

        $this->assertFalse(is_null($t));

        $inpayment = $coll->getInpayment($t);

        $this->assertFalse(is_null($inpayment->getStatus()));
    }
}
