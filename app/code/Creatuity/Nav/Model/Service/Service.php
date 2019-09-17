<?php

namespace Creatuity\Nav\Model\Service;

use Creatuity\Nav\Model\Service\Object\ServiceObject;
use Creatuity\Nav\Model\Service\Request\ServiceRequest;
use Creatuity\Nav\Model\Service\Response\Parser\ResponseParser;
use Creatuity\Nav\Model\Soap\HttpSoapClientFactory;
use Psr\Log\LoggerInterface;

class Service
{
    protected $serviceObject;
    protected $httpSoapClientFactory;
    protected $logger;

    public function __construct(
        ServiceObject $serviceObject,
        HttpSoapClientFactory $httpSoapClientFactory,
        LoggerInterface $logger
    ) {
        $this->serviceObject = $serviceObject;
        $this->httpSoapClientFactory = $httpSoapClientFactory;
        $this->logger = $logger;
    }

    public function process(ServiceRequest $serviceRequest)
    {
        $method = $serviceRequest->getMethod();
        $parameters = $serviceRequest->getParameters();

        $soapClient = $this->httpSoapClientFactory->create($this->serviceObject);

        $startTime = microtime(true);
        $response = $soapClient->$method($parameters);
        $endTime = microtime(true);
        $this->logger->info("NAV Response Time");
        $this->logger->info($endTime-$startTime);

        $responseParser = new ResponseParser(
            $this->serviceObject,
            $serviceRequest->getOperation(),
            $serviceRequest->getDimension()
        );
        $this->logger->info("Product Count = ".count($responseParser->parse($response)));
        return $responseParser->parse($response);
    }
}
