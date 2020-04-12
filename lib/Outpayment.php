<?php

namespace Payhere;

use Payhere\HttpClient\ClientInterface;
use Payhere\models\ResourceFactory;
use Payhere\Util\Util;

class Outpayment extends Request
{
    public $headers;


    public $authToken;


    public $_baseUrl;


    //@var string target environment
    public $_targetEnvironment;


    // @var string the currency of http calls
    public $_currency;


    // @var string The Payhere disbursements API Secret.
    public $_disbursementApiSecret;

    // @var string The Payhere disbursements primary Key
    public $_disbursementPrimaryKey;

    // @var string The Payhere disbursements User Id
    public $_disbursementUserId;


    /**
     * @var HttpClient\ClientInterface
     */
    private static $_httpClient;


    /**
     * Outpayment constructor.
     *
     * @param string|null $currency
     * @param string|null $baseUrl
     */
    public function __construct($currency = null, $baseUrl = null, $targetEnvironment = null, $disbursementApiSecret = null, $disbursementPrimaryKey = null, $disbursementUserId = null)
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


        if (!$disbursementApiSecret) {
            $disbursementApiSecret = Payhere::getOutpaymentApiSecret();
        }
        $this->_disbursementApiSecret = $disbursementApiSecret;


        if (!$disbursementPrimaryKey) {
            $disbursementPrimaryKey = Payhere::getOutpaymentPrimaryKey();
        }
        $this->_disbursementPrimaryKey = $disbursementPrimaryKey;


        if (!$disbursementUserId) {
            $disbursementUserId = Payhere::getOutpaymentUserId();
        }
        $this->_disbursementUserId = $disbursementUserId;
    }


    /**
     * @param array|null        $params
     * @param array|string|null $options
     *
     * @return AccessToken The OAuth Token.
     */
    public function getToken($params = null, $options = null)
    {
        $url = $this->_baseUrl . '/disbursement/token/';


        $encodedString = base64_encode(
            Payhere::getOutpaymentUserId() . ':' . Payhere::getOutpaymentApiSecret()
        );
        $headers = [
            'Authorization' => 'Basic ' . $encodedString,
            'Content-Type' => 'application/json',
            'Ocp-Apim-Subscription-Key' => Payhere::getOutpaymentPrimaryKey()
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
        $url = $this->_baseUrl . "/disbursement/v1_0/account/balance";

        $token = $this->getToken()->getToken();


        $headers = [
            'Authorization' => 'Bearer ' . $token,
            'Content-Type' => 'application/json',
            "X-Target-Environment" => $this->_targetEnvironment,
            'Ocp-Apim-Subscription-Key' => Payhere::getOutpaymentPrimaryKey()
        ];


        $response = self::request('get', $url, $params, $headers);

        return $response;


        $obj = ResourceFactory::balanceFromJson($response->json);

        return $obj;
    }


    /**
     * @param array|null        $params
     * @param array|string|null $options
     *
     * @return Transaction The transaction.
     */
    public function getTransaction($trasaction_id, $params = null)
    {
        $url = $this->_baseUrl . "/disbursement/v1_0/transfer/" . $trasaction_id;

        $token = $this->getToken()->getToken();

        $headers = [
            'Authorization' => 'Bearer ' . $token,
            'Content-Type' => 'application/json',
            "X-Target-Environment" => $this->_targetEnvironment,
            'Ocp-Apim-Subscription-Key' => Payhere::getOutpaymentPrimaryKey(),
        ];

        $response = self::request('get', $url, $params, $headers);

        $obj = ResourceFactory::transferFromJson($response->json);

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
        $url = $this->_baseUrl . "/disbursement/v1_0/transfer";

        $token = $this->getToken()->getToken();

        $transaction = Util\Util::uuid();

        $headers = [
            'Authorization' => 'Bearer ' . $token,
            'Content-Type' => 'application/json',
            "X-Target-Environment" => $this->_targetEnvironment,
            'Ocp-Apim-Subscription-Key' => Payhere::getOutpaymentPrimaryKey(),
            "X-Reference-Id" => $transaction
        ];


        $data = [
            "payee" => [
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


    public function isActive($mobile, $params = null)
    {
        $token = $this->getToken()->getToken();


        $headers = [
            'Authorization' => 'Bearer ' . $token,
            'Content-Type' => 'application/json',
            "X-Target-Environment" => $this->_targetEnvironment,
            'Ocp-Apim-Subscription-Key' => Payhere::getOutpaymentPrimaryKey()
        ];


        $url = $this->_baseUrl . "/disbursement/v1_0/accountholder/MSISDN/" . $mobile . "/active";


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