<?php

namespace Creatuity\Nav\Model\Soap;

use Creatuity\Nav\Model\Connection\ConnectionFactory;
use Creatuity\Nav\Model\Http\HttpClientFactory;
use Creatuity\Nav\Model\Service\Object\ServiceObject;

class HttpSoapClientFactory
{
    protected $connectionFactory;
    protected $httpClientFactory;
    protected $wsdlFactory;

    public function __construct(
        ConnectionFactory $connectionFactory,
        HttpClientFactory $httpClientFactory,
        WsdlFactory $wsdlFactory
    ) {
        $this->connectionFactory = $connectionFactory;
        $this->httpClientFactory = $httpClientFactory;
        $this->wsdlFactory = $wsdlFactory;
    }

    public function create(ServiceObject $serviceObject)
    {
        return new HttpSoapClient(
            $serviceObject,
            $this->connectionFactory,
            $this->httpClientFactory,
            $this->wsdlFactory
        );
    }
}
