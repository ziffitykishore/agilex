<?php

namespace Creatuity\Nav\Model\Service;

use Creatuity\Nav\Model\Service\Object\ServiceObject;
use Creatuity\Nav\Model\Service\Request\ServiceRequest;
use Creatuity\Nav\Model\Service\Response\Parser\ResponseParser;
use Creatuity\Nav\Model\Soap\HttpSoapClientFactory;

class Service
{
    protected $serviceObject;
    protected $httpSoapClientFactory;

    public function __construct(
        ServiceObject $serviceObject,
        HttpSoapClientFactory $httpSoapClientFactory
    ) {
        $this->serviceObject = $serviceObject;
        $this->httpSoapClientFactory = $httpSoapClientFactory;
    }

    public function process(ServiceRequest $serviceRequest)
    {
        $method = $serviceRequest->getMethod();
        $parameters = $serviceRequest->getParameters();

        $soapClient = $this->httpSoapClientFactory->create($this->serviceObject);
        $response = $soapClient->$method($parameters);

        $responseParser = new ResponseParser(
            $this->serviceObject,
            $serviceRequest->getOperation(),
            $serviceRequest->getDimension()
        );

        return $responseParser->parse($response);
    }
}
