<?php

namespace Payhere\models;
class Inpayment  implements \JsonSerializable
{

public $amount;
public $currency;

public $financialInpaymentId;

public $externalId;
public $payer;
public $status;
public $reason;


    public function __construct($amount,$currency,$financialInpaymentId,$externalId,  $payer,$status,$reason)
    {
        $this->amount = $amount;
        $this->currency = $currency;
        $this->financialInpaymentId = $financialInpaymentId;

        $this->externalId = $externalId;
        $this->payer = $payer;
        $this->status = $status;
        $this->reason = $reason;


    }


    public function jsonSerialize()
    {
        $data = array(
            'amount' => $this->amount,
            'currency' => $this->currency,
            'financialInpaymentId' => $this->financialInpaymentId,
            'externalId' => $this->externalId,
            'payer' => $this-> payer,
            'status' => $this-> status,
            'reason' => $this -> reason


        );

        return $data;
    }

}
