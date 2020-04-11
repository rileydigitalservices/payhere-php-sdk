<?php

namespace Payhere\Error;

use Exception;

abstract class Base extends Exception
{
    public function __construct(
        $message,
        $httpStatus = null,
        $httpBody = null,
        $jsonBody = null,
        $httpHeaders = null
    ) {
        parent::__construct($message);
        $this->httpStatus = $httpStatus;
        $this->httpBody = $httpBody;
        $this->jsonBody = $jsonBody;
        $this->httpHeaders = $httpHeaders;
        $this->appId = null;

        // TODO: make this a proper constructor argument in the next major
        //       release.
        $this->stripeCode = isset($jsonBody["error"]["code"]) ? $jsonBody["error"]["code"] : null;

        if ($httpHeaders && isset($httpHeaders['APP-ID'])) {
            $this->appId = $httpHeaders['APP-ID'];
        }
    }

    public function getPayhereCode()
    {
        return $this->stripeCode;
    }

    public function getHttpStatus()
    {
        return $this->httpStatus;
    }

    public function getHttpBody()
    {
        return $this->httpBody;
    }

    public function getJsonBody()
    {
        return $this->jsonBody;
    }

    public function getHttpHeaders()
    {
        return $this->httpHeaders;
    }

    public function getRequestId()
    {
        return $this->appId;
    }

    public function __toString()
    {
        $id = $this->appId ? " from API request '{$this->appId}'": "";
        $message = explode("\n", parent::__toString());
        $message[0] .= $id;
        return implode("\n", $message);
    }
}