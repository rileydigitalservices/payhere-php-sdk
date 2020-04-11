<?php

namespace Payhere;


use Payhere\HttpClient\ClientInterface;
use Payhere\models\ResourceFactory;




class Inpayment extends Request
{



    public $headers;


    public $authToken;



    public  $_baseUrl;


    //@var string target environment
    public  $_targetEnvironment;


    // @var string the currency of http calls
    public  $_currency;



    // @var string The Payhere Inpayments API Secret.
    public  $_collectionApiSecret;

    // @var string The Payhere collections primary Key
    public  $_collectionPrimaryKey;

    // @var string The Payhere collections User Id
    public  $_collectionUserId ;







    /**
     * @var HttpClient\ClientInterface
     */
    private static $_httpClient;





    /**
     * Inpayment constructor.
     *
     * @param string|null $apiKey
     * @param string|null $apiBase
     */
    public function __construct($currency=null,$baseUrl=null,$targetEnvironment=null, $collectionApiSecret=null,  $collectionPrimaryKey=null,$collectionUserId=null)
    {

        if (!$currency) {
            $currency = Payhere::getCurrency();
        }
        $this->_currency = $currency;


        if (!$baseUrl) {
            $baseUrl = Payhere::getBaseUrl();
        }
        $this->_baseUrl = $baseUrl;


        if (!$targetEnvironment) {
            $targetEnvironment = Payhere::getTargetEnvironment();
        }
        $this->_targetEnvironment = $targetEnvironment;


        if (!$collectionApiSecret) {
            $collectionApiSecret = Payhere::getInpaymentApiSecret();
        }
        $this->_collectionApiSecret = $collectionApiSecret;


        if (!$collectionPrimaryKey) {
            $collectionPrimaryKey = Payhere::getInpaymentPrimaryKey();
        }
        $this->_collectionPrimaryKey = $collectionPrimaryKey;


        if (!$collectionUserId) {
            $collectionUserId = Payhere::getInpaymentUserId();
        }
        $this->_collectionUserId = $collectionUserId;
    }






    /**
     * @param array|null $params
     * @param array|string|null $options
     *
     * @return AccessToken The OAuth Token.
     */
    public function getToken($params = null, $options = null)
    {


        $url = $this->_baseUrl . '/collection/token/';


        $encodedString = base64_encode(
            Payhere::getInpaymentUserId() . ':' . Payhere::getInpaymentApiSecret()
        );
        $headers = [
            'Authorization' => 'Basic ' . $encodedString,
            'Content-Type' => 'application/json',
            'Ocp-Apim-Subscription-Key' => Payhere::getInpaymentPrimaryKey()
        ];


        $response = self::request('post', $url, $params, $headers);




        $obj = ResourceFactory::accessTokenFromJson($response->json);

        return $obj;


    }





    /**
     * @param array|null $params
     * @param array|string|null $options
     *
     * @return Balance The account balance.
     */
    public function getBalance($params = null, $options = null)
    {

        $url = $this->_baseUrl . "/collection/v1_0/account/balance";

        $token = $this->getToken()->getToken();



        $headers = [
            'Authorization' => 'Bearer ' . $token,
            'Content-Type' => 'application/json',
            "X-Target-Environment" => $this->_targetEnvironment,
            'Ocp-Apim-Subscription-Key' => Payhere::getInpaymentPrimaryKey()
        ];


        $response = self::request('get', $url, $params, $headers);

        return $response;




        $obj = ResourceFactory::balanceFromJson($response->json);

        return $obj;

    }


    /**
     * @param array|null $params
     * @param array|string|null $options
     *
     * @return Transaction The transaction.
     */
    public function getTransaction($params = null, $options = null)
    {

    }


    /**
     * @param array|null $params
     * @param array|string|null $options
     *
     * @return Charge The refunded charge.
     */
    public function requestToPay($params = null, $options = null)
    {



        self::_validateParams($params);
        $url = "/collection/v1_0/requesttopay";

        $headers=[];


        $response = self::request('post', $url, $params, $headers);


        $obj = \Stripe\Util\Util::convertToStripeObject($response->json, $options);

        return $obj;

    }


    /**
     * @param array|null|mixed $params The list of parameters to validate
     *
     * @throws \Payhere\Error\PayhereError if $params exists and is not an array
     */
    protected static function _validateParams($params = null)
    {
        if ($params && !is_array($params)) {
            $message = "You must pass an array as the first argument to Payhere API "
                . "method calls.  (HINT: an example call to create a charge "
                . "would be: \"Payhere\\Charge::create(['amount' => 100, "
                . "'currency' => 'usd', 'source' => 'tok_1234'])\")";
            throw new \Payhere\Error\PayhereError($message);
        }
    }


    /**
     * @static
     *
     * @param HttpClient\ClientInterface $client
     */
    public static function setHttpClient($client)
    {
        self::$_httpClient = $client;
    }




    /**
     * @return HttpClient\ClientInterface
     */
    private function httpClient()
    {
        if (!self::$_httpClient) {
            self::$_httpClient = HttpClient\CurlClient::instance();
        }
        return self::$_httpClient;
    }







}