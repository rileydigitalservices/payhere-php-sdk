<?php

namespace Payhere;

/**
 * Class Payhere
 *
 * @package Payhere
 */
class Payhere
{
    // @var string the base url of the API
    const VERSION = 'v1';

    //@var string target environment
    public static $baseUrl;


    // @var string the currency of http calls
    public static $targetEnvironment;

    // @var string The Payhere username
    public static $username;

    // @var string The Payhere password
    public static $password;


    // @var string The Payhere remittance API Secret.
    public static $appId;

    // @var Util\LoggerInterface|null The logger to which the library will
    //   produce messages.
    public static $verifySslCerts = false;

    // @var int Maximum number of request retries
    public static $logger = null;


    // @var float Maximum delay between retries, in seconds
    public static $maxNetworkRetries = 0;

    // @var float Initial delay between retries, in seconds
    private static $maxNetworkRetryDelay = 2.0;
    private static $initialNetworkRetryDelay = 0.5;

    /**
     * @return string The Base Url.
     */
    public static function getBaseUrl()
    {
        $burl = getenv("PAYHERE_BASE_URL");

        if (isset(self::$baseUrl)) {
            return self::$baseUrl;
        } else if ($burl) {
            return $burl;
        } else {
            if (self::getTargetEnvironment() == "sandbox") {
                return "https://api-sandbox.payhere.africa/api/".self::VERSION;
            }
            return "https://api.payhere.africa/api/".self::VERSION;
        }
    }


    /**
     * Sets the baseUrl.
     *
     * @param string $baseUrl
     */
    public static function setBaseUrl($baseUrl)
    {
        self::$baseUrl = $baseUrl;
    }

    /**
     * @return string The target environment.
     */
    public static function getTargetEnvironment()
    {

        $targ = getenv("PAYHERE_TARGET_ENVIRONMENT");
        if (isset(self::$targetEnvironment)) {
            return self::$targetEnvironment;
        }

        if ($targ) {
            return $targ;
        }

        return "sandbox";
    }


    /**
     * Sets the $targetEnvironment.
     *
     * @param string $targetEnvironment
     */
    public static function setTargetEnvironment($targetEnvironment)
    {
        self::$targetEnvironment = $targetEnvironment;
    }


    /**
     * @return string The username.
     */
    public static function getUsername()
    {

        $arg = getenv("PAYHERE_USERNAME");

        if (isset(self::$username)) {
            return self::$username;
        }

        if ($arg) {
            return $arg;
        }
    }


    /**
     * Sets the username.
     *
     * @param string $username
     */
    public static function setUsername($username)
    {
        self::$username = $username;
    }


    /**
     * @return string The password.
     */
    public static function getPassword()
    {
        $arg = getenv("PAYHERE_PASSWORD");

        if (isset(self::$password)) {
            return self::$password;
        }

        if ($arg) {
            return $arg;
        }
    }


    /**
     * Sets the password.
     *
     * @param string $password
     */
    public static function setPassword($password)
    {
        self::$password = $password;
    }


    /**
     * @return string The appId.
     */
    public static function getAppId()
    {

        $arg = getenv("PAYHERE_APP_ID");

        if (isset(self::$appId)) {
            return self::$appId;
        }

        if ($arg) {
            return $arg;
        }
    }


    /**
     * Sets the appId.
     *
     * @param string $appId
     */
    public static function setAppId($appId)
    {
        self::$appId = $appId;
    }

    /**
     * @return Util\LoggerInterface The logger to which the library will
     *   produce messages.
     */
    public static function getLogger()
    {
        if (self::$logger == null) {
            return new Util\DefaultLogger();
        }
        return self::$logger;
    }

    /**
     * @param Util\LoggerInterface $logger The logger to which the library
     *                                     will produce messages.
     */
    public static function setLogger($logger)
    {
        self::$logger = $logger;
    }


    /**
     * @return int Maximum number of request retries
     */
    public static function getMaxNetworkRetries()
    {
        return self::$maxNetworkRetries;
    }

    /**
     * @param int $maxNetworkRetries Maximum number of request retries
     */
    public static function setMaxNetworkRetries($maxNetworkRetries)
    {
        self::$maxNetworkRetries = $maxNetworkRetries;
    }

    /**
     * @return float Maximum delay between retries, in seconds
     */
    public static function getMaxNetworkRetryDelay()
    {
        return self::$maxNetworkRetryDelay;
    }

    /**
     * @return float Initial delay between retries, in seconds
     */
    public static function getInitialNetworkRetryDelay()
    {
        return self::$initialNetworkRetryDelay;
    }
}
