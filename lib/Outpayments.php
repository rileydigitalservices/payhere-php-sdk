<?php

namespace Payhere;

use Payhere\models\ResourceFactory;
use Payhere\Util;

class Outpayments extends Payments
{
    /**
     * @param array|null        $params
     * @param array|string|null $options
     *
     * @return Outpayment The outpayment.
     */
    public function getOutpayment($outpayment_id, $params = null)
    {
        $url = $this->_baseUrl . "/outpayments" . $outpayment_id;

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
    public function transfer($params, $options = null)
    {
        
        self::_validateParams($params);

        $url = $this->_baseUrl . "/outpayments";
        
        $processingNumber = Util\Util::uuid();
        
        if (!$params['processing_number']) {
            $processingNumber = $params['processing_number'];
        }
        

        $data = [
            "processingNumber" => $processingNumber,
            "msisdn" => $params['mobile'],
            "narration" => $params['narration'],
            "amount" => $params['amount']];


        $response = self::request('post', $url, $data, $this->getHeader());


        return $outpayment;
    }
}
