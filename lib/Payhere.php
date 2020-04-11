<?php

namespace Payhere;

/**
 * Class Payhere
 *
 * @package payhere
 */
class Payhere


{


    // @var string the base url of the API
    public static $baseUrl;


    //@var string target environment
    public static $targetEnvironment;


    // @var string the currency of http calls
    public static $currency;



    // @var string The Payhere Collections API Secret.
    public static $collectionApiSecret;

    // @var string The Payhere collections primary Key
    public static $collectionPrimaryKey;

    // @var string The Payhere collections User Id
    public static $collectionUserId ;


    // @var boolean Defaults to true.
    public static $verifySslCerts = false;



    // @var Util\LoggerInterface|null The logger to which the library will
    //   produce messages.
    public static $logger = null;

    // @var int Maximum number of request retries
    public static $maxNetworkRetries = 0;


    // @var float Maximum delay between retries, in seconds
    private static $maxNetworkRetryDelay = 2.0;

    // @var float Initial delay between retries, in seconds
    private static $initialNetworkRetryDelay = 0.5;

    const VERSION = '6.35.2';




    /**
     * @return string The Base Url.
     */
    public static function getBaseUrl()
    {
        return self::$baseUrl || getenv("BASE_URL") || "http://api.payhere.africa" ;
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
        return self::$targetEnvironment || getenv("TARGET_ENVIRONMENT") || "sandbox" ;;
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
     * @return string The collectionApiSecret.
     */
    public static function getCollectionApiSecret()
    {
        return self::$collectionApiSecret || getenv("COLLECTION_API_SECRET");
    }


    /**
     * Sets the collectionApiSecret.
     *
     * @param string $collectionApiSecret
     */
    public static function setCollectionApiSecret($collectionApiSecret)
    {
        self::$collectionApiSecret = $collectionApiSecret;
    }


    /**
     * @return string The collectionPrimaryKey.
     */
    public static function getCollectionPrimaryKey()
    {
        return self::$collectionPrimaryKey || getenv("COLLECTION_PRIMARY_KEY");
    }




    /**
     * Sets the collectionPrimaryKey.
     *
     * @param string $collectionPrimaryKey
     */
    public static function setCollectionPrimaryKey($collectionPrimaryKey)
    {
        self::$collectionPrimaryKey = $collectionPrimaryKey;
    }


    /**
     * @return string The collectionUserId.
     */
    public static function getCollectionUserId()
    {
        return self::$collectionUserId || getenv("COLLECTION_USER_ID");
    }



    /**
     * Sets the collectionUserId.
     *
     * @param string $collectionUserId
     */
    public static function setCollectionUserId($collectionUserId)
    {
        self::$collectionUserId = $collectionUserId;
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
     *   will produce messages.
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