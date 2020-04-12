<?php

namespace Payhere;

use Payhere\HttpClient\CurlClient;

class OutpaymentsTest extends TestCase
{
    public function testTransfer()
    {
        $coll = new Outpayments();

        $params = [
            'mobile' => "256782181656",
            'processing_number' => "ref",
            'payer_message' => "12",
            'narration' => "what is being paid for",
            'amount' => "500"];

        $t = $coll->transfer($params);

        $this->assertFalse(is_null($t));

        $outpayment = $coll->getOutpayment($t);

        $this->assertFalse(is_null($outpayment->getStatus()));
    }
}