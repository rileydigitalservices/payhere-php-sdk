<?php

namespace Payhere\models;

class ResourceFactory
{

    public static function requestToPayFromJson($jsonData)
    {
        $requestToPay = new \Payhere\models\RequestToPay($jsonData['payer'], $jsonData['payeeNote'], $jsonData['payerMessage'], $jsonData['externalId'], $jsonData['currency'], $jsonData['amount'], $jsonData['status'], $jsonData['financialTransactionId']);

        return $requestToPay;
    }


    public static function transactionFromJson($jsonData)
    {
        $transaction = new \Payhere\models\Transaction($jsonData['amount'], $jsonData['currency'], $jsonData['financialTransactionId'], $jsonData['externalId'], $jsonData['payer'], $jsonData['status'], $jsonData['reason']);

        return $transaction;
    }


    public static function transferFromJson($jsonData)
    {
        $transfer = new \Payhere\models\Transfer($jsonData['payee'], $jsonData['payeeNote'], $jsonData['payerMessage'], $jsonData['externalId'], $jsonData['currency'], $jsonData['amount']);

        return $transfer;
    }
}
