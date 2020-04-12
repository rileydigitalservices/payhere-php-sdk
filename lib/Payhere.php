<?php

namespace Payhere;

/**
 * Class Payhere
 *
 * @package momoApi
 */
class Payhere
{

    // @var string the base url of the API
    const VERSION = '6.35.2';


    //@var string target environment
    public static $baseUrl;


    // @var string the currency of http calls
    public static $targetEnvironment;


    // @var string The Payhere Inpayments API Secret.
    public static $currency;

    // @var string The Payhere collections primary Key
    public static $collectionApiSecret;

    // @var string The Payhere collections User Id
    public static $collectionPrimaryKey;


    // @var string The Payhere remittance API Secret.
    public static $collectionUserId;

    // @var string The Payhere remittance primary Key
    public static $remittanceApiSecret;

    // @var string The Payhere remittance User Id
    public static $remittancePrimaryKey;


    // @var string The Payhere disbursements API Secret.
    public static $remittanceUserId;

    // @var string The Payhere disbursements primary Key
    public static $disbursementApiSecret;

    // @var string The Payhere disbursements User Id
    public static $disbursementPrimaryKey;


    // @var boolean Defaults to true.
    public static $disbursementUserId;


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
        $burl = getenv("BASE_URL");

        if (isset(self::$baseUrl)) {
            return self::$baseUrl;
        } else if ($burl) {
            return $burl;
        } else {
            return "https://ericssonbasicapi2.azure-api.net";
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

        $targ = getenv("TARGET_ENVIRONMENT");
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
     * @return string The collectionApiSecret.
     */
    public static function getInpaymentApiSecret()
    {

        $arg = getenv("COLLECTION_API_SECRET");

        if (isset(self::$collectionApiSecret)) {
            return self::$collectionApiSecret;
        }

        if ($arg) {
            return $arg;
        }
    }


    /**
     * Sets the collectionApiSecret.
     *
     * @param string $collectionApiSecret
     */
    public static function setInpaymentApiSecret($collectionApiSecret)
    {
        self::$collectionApiSecret = $collectionApiSecret;
    }


    /**
     * @return string The collectionPrimaryKey.
     */
    public static function getInpaymentPrimaryKey()
    {
        $arg = getenv("COLLECTION_PRIMARY_KEY");

        if (isset(self::$collectionPrimaryKey)) {
            return self::$collectionPrimaryKey;
        }

        if ($arg) {
            return $arg;
        }
    }


    /**
     * Sets the collectionPrimaryKey.
     *
     * @param string $collectionPrimaryKey
     */
    public static function setInpaymentPrimaryKey($collectionPrimaryKey)
    {
        self::$collectionPrimaryKey = $collectionPrimaryKey;
    }


    /**
     * @return string The collectionUserId.
     */
    public static function getInpaymentUserId()
    {

        $arg = getenv("COLLECTION_USER_ID");

        if (isset(self::$collectionUserId)) {
            return self::$collectionUserId;
        }

        if ($arg) {
            return $arg;
        }
    }


    /**
     * Sets the collectionUserId.
     *
     * @param string $collectionUserId
     */
    public static function setInpaymentUserId($collectionUserId)
    {
        self::$collectionUserId = $collectionUserId;
    }

    /**
     * @return string The disbursementApiSecret.
     */
    public static function getOutpaymentApiSecret()
    {
        $arg = getenv("DISBURSEMENT_API_SECRET");

        if (isset(self::$disbursementApiSecret)) {
            return self::$disbursementApiSecret;
        }

        if ($arg) {
            return $arg;
        }
    }


    /**
     * Sets the disbursementApiSecret.
     *
     * @param string $disbursementApiSecret
     */
    public static function setOutpaymentApiSecret($disbursementApiSecret)
    {
        self::$disbursementApiSecret = $disbursementApiSecret;
    }


    /**
     * @return string The disbursementPrimaryKey.
     */
    public static function getOutpaymentPrimaryKey()
    {

        $arg = getenv("DISBURSEMENT_PRIMARY_KEY");

        if (isset(self::$disbursementPrimaryKey)) {
            return self::$disbursementPrimaryKey;
        }

        if ($arg) {
            return $arg;
        }
    }


    /**
     * Sets the disbursementPrimaryKey.
     *
     * @param string $disbursementPrimaryKey
     */
    public static function setOutpaymentPrimaryKey($disbursementPrimaryKey)
    {
        self::$disbursementPrimaryKey = $disbursementPrimaryKey;
    }


    /**
     * @return string The disbursementUserId .
     */
    public static function getOutpaymentUserId()
    {

        $arg = getenv("DISBURSEMENT_USER_ID");

        if (isset(self::$disbursementUserId)) {
            return self::$disbursementUserId;
        }

        if ($arg) {
            return $arg;
        }
    }


    /**
     * Sets the disbursementUserId.
     *
     * @param string $disbursementUserId
     */
    public static function setOutpaymentUserId($disbursementUserId)
    {
        self::$disbursementUserId = $disbursementUserId;
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