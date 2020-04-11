<?php

namespace Payhere;

/**
 * Class Request
 *
 * @package Payhere
 */
class Request
{

    public  $_baseUrl;


    //@var string target environment
    public  $_targetEnvironment;


    // @var string The Payhere Collections API Secret.
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
     * Request constructor.
     *
     * @param string|null $apiKey
     * @param string|null $apiBase
     */
    public function __construct()
    {

    }




    /**
     * @return string The base URL.
     */
    public static function baseUrl()
    {
        return Payhere::$baseUrl;
    }





    /**
     * @static
     *
     * @param ApiResource|bool|array|mixed $d
     *
     * @return ApiResource|array|string|mixed
     */
    private static function _encodeObjects($d)
    {
        if ($d instanceof ApiResource) {
            return Util\Util::utf8($d->id);
        } elseif ($d === true) {
            return 'true';
        } elseif ($d === false) {
            return 'false';
        } elseif (is_array($d)) {
            $res = [];
            foreach ($d as $k => $v) {
                $res[$k] = self::_encodeObjects($v);
            }
            return $res;
        } else {
            return Util\Util::utf8($d);
        }
    }

    /**
     * @param string     $method
     * @param string     $url
     * @param array|null $params
     * @param array|null $headers
     *
     * @return array An array whose first element is an API response and second
     *    element is the API key used to make the request.
     * @throws Error\Api
     * @throws Error\Authentication
     * @throws Error\Card
     * @throws Error\InvalidRequest
     * @throws Error\OAuth\InvalidClient
     * @throws Error\OAuth\InvalidGrant
     * @throws Error\OAuth\InvalidRequest
     * @throws Error\OAuth\InvalidScope
     * @throws Error\OAuth\UnsupportedGrantType
     * @throws Error\OAuth\UnsupportedResponseType
     * @throws Error\Permission
     * @throws Error\RateLimit
     * @throws Error\Idempotency
     * @throws Error\ApiConnection
     */
    public function request($method, $url, $params = null, $headers = null)
    {
        $params = $params ?: [];
        $headers = $headers ?: [];


        $rawHeaders = [];

        foreach ($headers as $header => $value) {
            $rawHeaders[] = $header . ': ' . $value;
        }


        list($rbody, $rcode, $rheaders) = $this->httpClient()->request(
            $method,
            $url,
            $rawHeaders,
            $params
        );



        $json = $this->_interpretResponse($rbody, $rcode, $rheaders);
        $resp = new ApiResponse($rbody, $rcode, $rheaders, $json);
        return $resp;
    }

    /**
     * @param string $rbody A JSON string.
     * @param int $rcode
     * @param array $rheaders
     * @param array $resp
     *
     * @throws Error\InvalidRequest if the error is caused by the user.
     * @throws Error\Authentication if the error is caused by a lack of
     *    permissions.
     * @throws Error\Permission if the error is caused by insufficient
     *    permissions.
     * @throws Error\Card if the error is the error code is 402 (payment
     *    required)
     * @throws Error\InvalidRequest if the error is caused by the user.
     * @throws Error\Idempotency if the error is caused by an idempotency key.
     * @throws Error\OAuth\InvalidClient
     * @throws Error\OAuth\InvalidGrant
     * @throws Error\OAuth\InvalidRequest
     * @throws Error\OAuth\InvalidScope
     * @throws Error\OAuth\UnsupportedGrantType
     * @throws Error\OAuth\UnsupportedResponseType
     * @throws Error\Permission if the error is caused by insufficient
     *    permissions.
     * @throws Error\RateLimit if the error is caused by too many requests
     *    hitting the API.
     * @throws Error\Api otherwise.
     */
    public function handleErrorResponse($rbody, $rcode, $rheaders, $resp)
    {
        if (!is_array($resp) || !isset($resp['error'])) {
            $msg = "Invalid response object from API: $rbody "
                . "(HTTP response code was $rcode)";
            throw new Error\PayhereError($msg, $rcode, $rbody, $resp, $rheaders);
        }

        $errorData = $resp['error'];

        $error = null;
        if (is_string($errorData)) {
            $error = self::_specificOAuthError($rbody, $rcode, $rheaders, $resp, $errorData);
        }
        if (!$error) {
            $error = self::_specificAPIError($rbody, $rcode, $rheaders, $resp, $errorData);
        }

        throw $error;
    }

    /**
     * @static
     *
     * @param string $rbody
     * @param int    $rcode
     * @param array  $rheaders
     * @param array  $resp
     * @param array  $errorData
     *
     * @return Error\RateLimit|Error\Idempotency|Error\InvalidRequest|Error\Authentication|Error\Card|Error\Permission|Error\Api
     */
    private static function _specificAPIError($rbody, $rcode, $rheaders, $resp, $errorData)
    {
        $msg = isset($errorData['message']) ? $errorData['message'] : null;
        $param = isset($errorData['param']) ? $errorData['param'] : null;
        $code = isset($errorData['code']) ? $errorData['code'] : null;
        $type = isset($errorData['type']) ? $errorData['type'] : null;

        switch ($rcode) {
            case 400:
                // 'rate_limit' code is deprecated, but left here for backwards compatibility
                // for API versions earlier than 2015-09-08
                if ($code == 'rate_limit') {
                    return new Error\RateLimit($msg, $param, $rcode, $rbody, $resp, $rheaders);
                }
                if ($type == 'idempotency_error') {
                    return new Error\Idempotency($msg, $rcode, $rbody, $resp, $rheaders);
                }

            // intentional fall-through
            case 404:
                return new Error\InvalidRequest($msg, $param, $rcode, $rbody, $resp, $rheaders);
            case 401:
                return new Error\Authentication($msg, $rcode, $rbody, $resp, $rheaders);
            case 402:
                return new Error\Card($msg, $param, $code, $rcode, $rbody, $resp, $rheaders);
            case 403:
                return new Error\Permission($msg, $rcode, $rbody, $resp, $rheaders);
            case 429:
                return new Error\RateLimit($msg, $param, $rcode, $rbody, $resp, $rheaders);
            default:
                return new Error\PayhereError($msg, $rcode, $rbody, $resp, $rheaders);
        }
    }

    /**
     * @static
     *
     * @param string|bool $rbody
     * @param int         $rcode
     * @param array       $rheaders
     * @param array       $resp
     * @param string      $errorCode
     *
     * @return null|Error\OAuth\InvalidClient|Error\OAuth\InvalidGrant|Error\OAuth\InvalidRequest|Error\OAuth\InvalidScope|Error\OAuth\UnsupportedGrantType|Error\OAuth\UnsupportedResponseType
     */
    private static function _specificOAuthError($rbody, $rcode, $rheaders, $resp, $errorCode)
    {
        $description = isset($resp['error_description']) ? $resp['error_description'] : $errorCode;

        switch ($errorCode) {
            case 'invalid_client':
                return new Error\OAuth\InvalidClient($errorCode, $description, $rcode, $rbody, $resp, $rheaders);
            case 'invalid_grant':
                return new Error\OAuth\InvalidGrant($errorCode, $description, $rcode, $rbody, $resp, $rheaders);
            case 'invalid_request':
                return new Error\OAuth\InvalidRequest($errorCode, $description, $rcode, $rbody, $resp, $rheaders);
            case 'invalid_scope':
                return new Error\OAuth\InvalidScope($errorCode, $description, $rcode, $rbody, $resp, $rheaders);
            case 'unsupported_grant_type':
                return new Error\OAuth\UnsupportedGrantType($errorCode, $description, $rcode, $rbody, $resp, $rheaders);
            case 'unsupported_response_type':
                return new Error\OAuth\UnsupportedResponseType($errorCode, $description, $rcode, $rbody, $resp, $rheaders);
        }

        return null;
    }

    /**
     * @static
     *
     * @param null|array $appInfo
     *
     * @return null|string
     */
    private static function _formatAppInfo($appInfo)
    {
        if ($appInfo !== null) {
            $string = $appInfo['name'];
            if ($appInfo['version'] !== null) {
                $string .= '/' . $appInfo['version'];
            }
            if ($appInfo['url'] !== null) {
                $string .= ' (' . $appInfo['url'] . ')';
            }
            return $string;
        } else {
            return null;
        }
    }






    /**
     * @param string $rbody
     * @param int    $rcode
     * @param array  $rheaders
     *
     * @return mixed
     * @throws Error\Api
     * @throws Error\Authentication
     * @throws Error\Card
     * @throws Error\InvalidRequest
     * @throws Error\OAuth\InvalidClient
     * @throws Error\OAuth\InvalidGrant
     * @throws Error\OAuth\InvalidRequest
     * @throws Error\OAuth\InvalidScope
     * @throws Error\OAuth\UnsupportedGrantType
     * @throws Error\OAuth\UnsupportedResponseType
     * @throws Error\Permission
     * @throws Error\RateLimit
     * @throws Error\Idempotency
     */
    private function _interpretResponse($rbody, $rcode, $rheaders)
    {
        $resp = json_decode($rbody, true);
        $jsonError = json_last_error();
        if ($resp === null && $jsonError !== JSON_ERROR_NONE) {
            $msg = "Invalid response body from API: $rbody "
                . "(HTTP response code was $rcode, json_last_error() was $jsonError)";
            throw new Error\PayhereError($msg, $rcode, $rbody);
        }

        if ($rcode < 200 || $rcode >= 300) {
            $this->handleErrorResponse($rbody, $rcode, $rheaders, $resp);
        }
        return $resp;
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