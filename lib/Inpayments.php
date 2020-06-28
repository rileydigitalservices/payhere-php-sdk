<?php

namespace Payhere;

use Payhere\models\ResourceFactory;
use Payhere\Util;

class Inpayments extends Payments
{
    /**
     * @param array|null        $params
     * @param array|string|null $options
     *
     * @return Inpayment The inpayment.
     */
    public function getInpayment($inpayment_id, $params = null)
    {
        $url = $this->_baseUrl . "/inpayments" . $inpayment_id;

        $response = self::request('get', $url, $params, $this->getHeader());

        $obj = ResourceFactory::requestToPayFromJson($response->json);

        return $obj;
    }


    /**
     * @param array|null        $params
     * @param array|string|null $options
     *
     * @return Charge The refunded charge.
     */
    public function requestToPay($params, $options = null)
    {

        self::_validateParams($params);
        
        $url = $this->_baseUrl . "/inpayments";

        $processingNumber = Util\Util::uuid();
        
        if (array_key_exists("processing_number", $params)) {
            $processingNumber = $params['processing_number'];
        }
        

        $data = [
            "processingNumber" => $processingNumber,
            "msisdn" => $params['mobile'],
            "narration" => $params['narration'],
            "amount" => $params['amount']];


        $inpayment = self::request('post', $url, $data, $this->getHeader());


        return $inpayment;
    }
}
