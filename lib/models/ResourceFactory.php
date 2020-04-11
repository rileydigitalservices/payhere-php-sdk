<?php

namespace Payhere\models;
class ResourceFactory
{

    static public function requestToPayFromJson($jsonData)
    {
        $requestToPay = new \Payhere\models\RequestToPay($jsonData['payer'],$jsonData['payeeNote'],$jsonData['payerMessage'], $jsonData['externalId'],$jsonData['currency'],$jsonData['amount']);

        return $requestToPay;
    }


    static public function transactionFromJson($jsonData)
    {
        $transaction = new \Payhere\models\Transaction($jsonData['amount'],$jsonData['currency'],$jsonData['financialTransactionId'],$jsonData['externalId'],  $jsonData['payer'],$jsonData['status'],$jsonData['reason']);

        return $transaction;
    }


    static public function transferFromJson($jsonData)
    {
        $transfer = new \Payhere\models\Transfer($jsonData['payee'],$jsonData['payeeNote'],$jsonData['payerMessage'],$jsonData['externalId'], $jsonData['currency'], $jsonData['amount']);

        return $transfer;
    }






}