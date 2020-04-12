<?php

namespace Payhere;

class Payments extends Request
{
    public $headers;

    public $authToken;

    public $_baseUrl;

    // @var string The Payhere username
    public $_username;

    // @var string The Payhere password
    public $_password;

    // @var string The Payhere remittance API Secret.
    public $_appId;


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
    public function __construct($baseUrl = null, $targetEnvironment = null, $username = null, $password = null, $appId = null)
    {
        if (!$baseUrl) {
            $baseUrl = Payhere::getBaseUrl();
        }
        $this->_baseUrl = $baseUrl;

        if (!$username) {
            $username = Payhere::getUsername();
        }
        $this->_username = $username;


        if (!$password) {
            $password = Payhere::getPassword();
        }
        $this->_password = $password;


        if (!$appId) {
            $appId = Payhere::getAppId();
        }
        $this->_appId = $appId;
    }

     /**
     *
     * @return string The encoded string for the basic http authorization
     */
    public function getAuthorization()
    {
        $encodedString = base64_encode(
            Payhere::getUsername() . ':' . Payhere::getPassword()
        );

        return $encodedString;
    }
    
     /**
     *
     * @return array The associative array of the header
     */
    public function getHeader()
    {
        $authorization = $this->getAuthorization();

        $headers = [
            'Authorization' => 'Basic ' . $authorization,
            'Content-Type' => 'application/json',
            "APP-ID" => $this->_appId
        ];
        
        return $headers;
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