<?php

namespace Payhere;

/**
 * Base class for Payhere test cases.
 */
class TestCase extends \PHPUnit_Framework_TestCase
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



    protected function setUp()
    {
        // Save original values so that we can restore them after running tests
        $this->_baseUrl = Payhere::getBaseUrl();

        $this-> _targetEnvironment = Payhere::getTargetEnvironment();


        $this->_currency = Payhere::getCurrency();


        $this->_collectionApiSecret = Payhere::getCollectionApiSecret();

        $this->_collectionPrimaryKey = Payhere::getCollectionPrimaryKey();

        $this->_collectionUserId  = Payhere::getCollectionUserId();


        // Set up the HTTP client mocker
        $this->clientMock = $this->getMock('\Payhere\HttpClient\ClientInterface');

        // By default, use the real HTTP client
        Request::setHttpClient(HttpClient\CurlClient::instance());
    }

    protected function tearDown()
    {
        // Restore original values


         Payhere::setBaseUrl($this->_baseUrl);

         Payhere::setTargetEnvironment($this-> _targetEnvironment);

        Payhere::setCollectionApiSecret( $this->_collectionApiSecret);

         Payhere::setCollectionPrimaryKey($this->_collectionPrimaryKey);

        Payhere::setCollectionUserId( $this->_collectionUserId );

    }

    /**
     * Sets up a request expectation with the provided parameters. The request
     * will actually go through and be emitted.
     *
     * @param string $method HTTP method (e.g. 'post', 'get', etc.)
     * @param string $path relative path (e.g. '/v1/charges')
     * @param array|null $params array of parameters. If null, parameters will
     *   not be checked.
     * @param string[]|null $headers array of headers. Does not need to be
     *   exhaustive. If null, headers are not checked.
     * @param bool $hasFile Whether the request parameters contains a file.
     *   Defaults to false.
     * @param string|null $base base URL (e.g. 'https://api.Payhere.com')
     */
    protected function expectsRequest(
        $method,
        $path,
        $params = null,
        $headers = null,
        $hasFile = false,
        $base = null
    ) {
        $this->prepareRequestMock($method, $path, $params, $headers, $hasFile, $base)
            ->will($this->returnCallback(
                function ($method, $absUrl, $headers, $params, $hasFile) {
                    $curlClient = HttpClient\CurlClient::instance();
                    Request::setHttpClient($curlClient);
                    return $curlClient->request($method, $absUrl, $headers, $params, $hasFile);
                }
            ));
    }

    /**
     * Sets up a request expectation with the provided parameters. The request
     * will not actually be emitted, instead the provided response parameters
     * will be returned.
     *
     * @param string $method HTTP method (e.g. 'post', 'get', etc.)
     * @param string $path relative path (e.g. '/v1/charges')
     * @param array|null $params array of parameters. If null, parameters will
     *   not be checked.
     * @param string[]|null $headers array of headers. Does not need to be
     *   exhaustive. If null, headers are not checked.
     * @param bool $hasFile Whether the request parameters contains a file.
     *   Defaults to false.
     * @param array $response
     * @param integer $rcode
     * @param string|null $base
     *
     * @return array
     */
    protected function stubRequest(
        $method,
        $path,
        $params = null,
        $headers = null,
        $hasFile = false,
        $response = [],
        $rcode = 200,
        $base = null
    ) {
        $this->prepareRequestMock($method, $path, $params, $headers, $hasFile, $base)
            ->willReturn([json_encode($response), $rcode, []]);
    }

    /**
     * Prepares the client mocker for an invocation of the `request` method.
     * This helper method is used by both `expectsRequest` and `stubRequest` to
     * prepare the client mocker to expect an invocation of the `request` method
     * with the provided arguments.
     *
     * @param string $method HTTP method (e.g. 'post', 'get', etc.)
     * @param string $path relative path (e.g. '/v1/charges')
     * @param array|null $params array of parameters. If null, parameters will
     *   not be checked.
     * @param string[]|null $headers array of headers. Does not need to be
     *   exhaustive. If null, headers are not checked.
     * @param bool $hasFile Whether the request parameters contains a file.
     *   Defaults to false.
     * @param string|null $base base URL (e.g. 'https://api.Payhere.com')
     *
     * @return PHPUnit_Framework_MockObject_Builder_InvocationMocker
     */
    private function prepareRequestMock(
        $method,
        $path,
        $params = null,
        $headers = null,
        $hasFile = false,
        $base = null
    ) {
        Request::setHttpClient($this->clientMock);

        if ($base === null) {
            $base = Payhere::$apiBase;
        }
        $absUrl = $base . $path;

        return $this->clientMock
            ->expects($this->once())
            ->method('request')
            ->with(
                $this->identicalTo(strtolower($method)),
                $this->identicalTo($absUrl),
                // for headers, we only check that all of the headers provided in $headers are
                // present in the list of headers of the actual request
                $headers === null ? $this->anything() : $this->callback(function ($array) use ($headers) {
                    foreach ($headers as $header) {
                        if (!in_array($header, $array)) {
                            return false;
                        }
                    }
                    return true;
                }),
                $params === null ? $this->anything() : $this->identicalTo($params),
                $this->identicalTo($hasFile)
            );
    }
}
