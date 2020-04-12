<?php

namespace Payhere;

use Payhere\HttpClient\CurlClient;

class RequestTest extends TestCase
{
    public function testHttpClientInjection()
    {
        $reflector = new \ReflectionClass('Payhere\\ApiRequest');
        $method = $reflector->getMethod('httpClient');
        $method->setAccessible(true);

        $curl = new CurlClient();
        $curl->setTimeout(10);
        ApiRequest::setHttpClient($curl);

        $injectedCurl = $method->invoke(new ApiRequest());
        $this->assertSame($injectedCurl, $curl);
    }
}