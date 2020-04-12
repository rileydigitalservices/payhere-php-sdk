<?php

namespace Payhere;

use Payhere\HttpClient\ClientInterface;
use Payhere\models\ResourceFactory;

class Inpayment extends Request
{
    public $headers;

    public $authToken;

    public $_baseUrl;

    //@var string target environment
    public $_targetEnvironment;

    // @var string the currency of http calls
    public $_currency;

    // @var string The Payhere Inpayments API Secret.
    public $_collectionApiSecret;

    // @var string The Payhere collections primary Key
    public $_collectionPrimaryKey;

    // @var string The Payhere collections User Id
    public $_collectionUserId;


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
    public function __construct($currency = null, $baseUrl = null, $targetEnvironment = null, $collectionApiSecret = null, $collectionPrimaryKey = null, $collectionUserId = null)
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
     * @param array|null        $params
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
     * @param array|null        $params
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
    }


    /**
     * @param array|null        $params
     * @param array|string|null $options
     *
     * @return Transaction The transaction.
     */
    public function getTransaction($trasaction_id, $params = null)
    {
        $url = $this->_baseUrl . "/collection/v1_0/requesttopay/" . $trasaction_id;

        $token = $this->getToken()->getToken();

        $headers = [
            'Authorization' => 'Bearer ' . $token,
            'Content-Type' => 'application/json',
            "X-Target-Environment" => $this->_targetEnvironment,
            'Ocp-Apim-Subscription-Key' => Payhere::getInpaymentPrimaryKey(),
        ];

        $response = self::request('get', $url, $params, $headers);

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
        $url = $this->_baseUrl . "/collection/v1_0/requesttopay";

        $token = $this->getToken()->getToken();

        $transaction = Util\Util::uuid();

        $headers = [
            'Authorization' => 'Bearer ' . $token,
            'Content-Type' => 'application/json',
            "X-Target-Environment" => $this->_targetEnvironment,
            'Ocp-Apim-Subscription-Key' => Payhere::getInpaymentPrimaryKey(),
            "X-Reference-Id" => $transaction
        ];


        $data = [
            "payer" => [
                "partyIdType" => "MSISDN",
                "partyId" => $params['mobile']],
            "payeeNote" => $params['payee_note'],
            "payerMessage" => $params['payer_message'],
            "externalId" => $params['external_id'],
            "currency" => $params['currency'],
            "amount" => $params['amount']];


        $response = self::request('post', $url, $data, $headers);


        return $transaction;
    }


    public function isActive($mobile, $params = [])
    {

        $token = $this->getToken()->getToken();


        $headers = [
            'Authorization' => 'Bearer ' . $token,
            'Content-Type' => 'application/json',
            "X-Target-Environment" => $this->_targetEnvironment,
            'Ocp-Apim-Subscription-Key' => Payhere::getInpaymentPrimaryKey()
        ];

        $url = $this->_baseUrl . "/collection/v1_0/accountholder/MSISDN/" . $mobile . "/active";

        $response = self::request('get', $url, $params, $headers);

        return $response;
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
}