# payhere-php-sdk
Riley Digital Services Payhere API SDK for PHP

[![Build Status](https://travis-ci.com/rileydigitalservices/payhere-php-sdk.svg?branch=master)](https://travis-ci.com/rileydigitalservices/payhere-php-sdk)
[![Latest Stable Version](https://poser.pugx.org/rileydigitalservices/payhere-php-sdk/v/stable.svg)](https://packagist.org/packages/rileydigitalservices/payhere-php-sdk)
[![Total Downloads](https://poser.pugx.org/rileydigitalservices/payhere-php-sdk/downloads.svg)](https://packagist.org/packages/rileydigitalservices/payhere-php-sdk)
[![License](https://poser.pugx.org/rileydigitalservices/payhere-php-sdk/license.svg)](https://packagist.org/packages/rileydigitalservices/payhere-php-sdk)
[![Coverage Status](https://coveralls.io/repos/github/rileydigitalservices/payhere-php-sdk/badge.svg?branch=master)](https://coveralls.io/github/rileydigitalservices/payhere-php-sdk?branch=master)
[![Join the community on Spectrum](https://withspectrum.github.io/badge/badge.svg)](https://spectrum.chat/payhere-api-sdk/)



## Requirements

PHP 5.4.0 and later.

## Composer

You can install the sdk via [Composer](http://getcomposer.org/). Run the following command:

```bash

composer require rileydigitalservices/payhere-php-sdk
```

To use the sdk, use Composer's [autoload](https://getcomposer.org/doc/01-basic-usage.md#autoloading):

```php
require_once('vendor/autoload.php');
```

## Manual Installation

If you do not wish to use Composer, you can download the [latest release](https://github.com/rileydigitalservices/payhere-php-sdk/releases). Then, to use the sdk, include the `init.php` file.

```php
require_once('/path/to/payhere-php-sdk/init.php');
```

## Dependencies

The sdk require the following extensions in order to work properly:

- [`curl`](https://secure.php.net/manual/en/book.curl.php), although you can use your own non-cURL client if you prefer
- [`json`](https://secure.php.net/manual/en/book.json.php)
- [`mbstring`](https://secure.php.net/manual/en/book.mbstring.php) (Multibyte String)

If you use Composer, these dependencies should be handled automatically. If you install manually, you'll want to make sure that these extensions are available.

## Getting Started


# Sandbox Environment

## Getting environment API user 

Next, we need to get the `APP-ID`, `username` and `password` for use in the product. You can get these details from `https://dashboard.payhere.africa/register`.

The redentials in the sandbox environment can be used straight away. In production, the credentials are provided for you after KYC requirements are met.


## Configuration

Before we can fully utilize the library, we need to specify global configurations. The global configuration using the requestOpts builder. By default, these are picked from environment variables,
but can be overidden using the Payhere builder

* `BASE_URL`: An optional base url to the MTN Momo API. By default the staging base url will be used
* `ENVIRONMENT`: Optional enviroment, either "sandbox" or "production". Default is 'sandbox'
* `CALLBACK_HOST`: The domain where you webhooks urls are hosted. This is mandatory.
* `COLLECTION_PRIMARY_KEY`: The collections API primary key,
* `COLLECTION_USER_ID`:  The collection User Id
* `COLLECTION_API_SECRET`:  The Collection API secret

you can also use the Payhere to globally set the different variables.



```php
Payhere::setBaseUrl('base');

Payhere::setTargetEnvironment("targetenv");

Payhere::setCurrency("UGX");

Payhere::setCollectionApiSecret("collection_api_secret");

Payhere::setCollectionPrimaryKey("collection_primary_key");

Payhere::setCollectionUserId("collection_user_id");
```



## Collections

The collections client can be created with the following paramaters. Note that the `COLLECTION_USER_ID` and `COLLECTION_API_SECRET` for production are provided on the MTN OVA dashboard;

* `COLLECTION_PRIMARY_KEY`: Primary Key for the `Collection` product on the developer portal.
* `COLLECTION_USER_ID`: For sandbox, use the one generated with the `mtnmomo` command.
* `COLLECTION_API_SECRET`: For sandbox, use the one generated with the `mtnmomo` command.

You can create a collection client with the following:

```php




$client = Collection();
```

### Methods

1. `requestToPay`: This operation is used to request a payment from a consumer (Payer). The payer will be asked to authorize the payment. The transaction is executed once the payer has authorized the payment. The transaction will be in status PENDING until it is authorized or declined by the payer or it is timed out by the system. Status of the transaction can be validated by using `getTransactionStatus`.

2. `getTransaction`: Retrieve transaction information using the `transactionId` returned by `requestToPay`. You can invoke it at intervals until the transaction fails or succeeds. If the transaction has failed, it will throw an appropriate error. 

3. `getBalance`: Get the balance of the account.

4. `isPayerActive`: check if an account holder is registered and active in the system.

### Sample Code

```php

```


## Custom Request Timeouts

*NOTE:* We do not recommend decreasing the timeout for non-read-only calls , since even if you locally timeout, the request  can still complete.

To modify request timeouts (connect or total, in seconds) you'll need to tell the API client to use a CurlClient other than its default. You'll set the timeouts in that CurlClient.

```php
// set up your tweaked Curl client
$curl = new \Payhere\HttpClient\CurlClient();
$curl->setTimeout(10); // default is \Payhere\HttpClient\CurlClient::DEFAULT_TIMEOUT
$curl->setConnectTimeout(5); // default is \Payhere\HttpClient\CurlClient::DEFAULT_CONNECT_TIMEOUT

echo $curl->getTimeout(); // 10
echo $curl->getConnectTimeout(); // 5

// tell Payhere to use the tweaked client
\Payhere\ApiRequest::setHttpClient($curl);

// use the Momo API client as you normally would
```

## Custom cURL Options (e.g. proxies)

Need to set a proxy for your requests? Pass in the requisite `CURLOPT_*` array to the CurlClient constructor, using the same syntax as `curl_stopt_array()`. This will set the default cURL options for each HTTP request made by the SDK, though many more common options (e.g. timeouts; see above on how to set those) will be overridden by the client even if set here.

```php
// set up your tweaked Curl client
$curl = new \Payhere\HttpClient\CurlClient([CURLOPT_PROXY => 'proxy.local:80']);
// tell Payhere to use the tweaked client
\Payhere\ApiRequest::setHttpClient($curl);
```

Alternately, a callable can be passed to the CurlClient constructor that returns the above array based on request inputs. See `testDefaultOptions()` in `tests/CurlClientTest.php` for an example of this behavior. Note that the callable is called at the beginning of every API request, before the request is sent.

### Configuring a Logger

The library does minimal logging, but it can be configured
with a [`PSR-3` compatible logger][psr3] so that messages
end up there instead of `error_log`:

```php
\Payhere\Payhere::setLogger($logger);
```


### Configuring Automatic Retries

The library can be configured to automatically retry requests that fail due to
an intermittent network problem:

```php
\Payhere\Payhere::setMaxNetworkRetries(2);
```


## Development

Get [Composer][composer]. For example, on Mac OS:

```bash
brew install composer
```

Install dependencies:

```bash
composer install
```



Install dependencies as mentioned above (which will resolve [PHPUnit](http://packagist.org/packages/phpunit/phpunit)), then you can run the test suite:

```bash
./vendor/bin/phpunit
```

Or to run an individual test file:

```bash
./vendor/bin/phpunit tests/UtilTest.php
```


[composer]: https://getcomposer.org/
[curl]: http://curl.haxx.se/docs/caextract.html
[psr3]: http://www.php-fig.org/psr/psr-3/