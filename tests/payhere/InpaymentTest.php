<?php

namespace Payhere;

use Payhere\HttpClient\CurlClient;

class InpaymentTest extends TestCase
{

    public function testHttpClientInjection()
    {
        $reflector = new \ReflectionClass('Payhere\\Inpayment');
        $method = $reflector->getMethod('httpClient');
        $method->setAccessible(true);

        $curl = new CurlClient();
        $curl->setTimeout(10);
        Inpayment::setHttpClient($curl);

        $injectedCurl = $method->invoke(new Inpayment());
        $this->assertSame($injectedCurl, $curl);
    }

    public function testDefaultHeaders()
    {
        $reflector = new \ReflectionClass('Payhere\\Inpayment');
        $method = $reflector->getMethod('_defaultHeaders');
        $method->setAccessible(true);

    }

    public function  testGetToken(){

        $coll = new Inpayment();

        $token = $coll->getToken();

        $this->assertSame($token->getToken(), "");

    }

    public function  testGetBalance(){

        $coll = new Inpayment();

        $bal = $coll->getBalance();

        $this->assertSame($bal, "");

    }




}