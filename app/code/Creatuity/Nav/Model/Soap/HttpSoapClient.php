<?php

namespace Creatuity\Nav\Model\Soap;

use Creatuity\Nav\Model\Http\HttpClientInterface;
use SoapClient;
use Creatuity\Nav\Model\Connection\ConnectionFactory;
use Creatuity\Nav\Model\Service\Object\ServiceObject;
use Creatuity\Nav\Model\Http\HttpClientFactory;

class HttpSoapClient extends SoapClient
{
    protected $serviceObject;
    protected $connectionFactory;
    protected $connection;
    protected $httpClientFactory;
    protected $wsdlFactory;

    public function __construct(
        ServiceObject $serviceObject,
        ConnectionFactory $connectionFactory,
        HttpClientFactory $httpClientFactory,
        WsdlFactory $wsdlFactory
    ) {
        $this->serviceObject = $serviceObject;
        $this->connectionFactory = $connectionFactory;
        $this->httpClientFactory = $httpClientFactory;
        $this->wsdlFactory = $wsdlFactory;

        $wsdlContent = $this->get($this->getWsdlUri());
        $wsdl = $this->wsdlFactory->create($this->serviceObject, $wsdlContent);

        parent::__construct($wsdl->getFilename());
    }

    public function __doRequest($request, $location, $action, $version, $one_way = 0)
    {
        return $this->soapRequest($location, $request, $action);
    }

    protected function soapRequest($path, $body, $action)
    {

        return $this->get(
            $path,
            $body,
            [
                'Content-Type' => 'text/xml; charset=utf-8',
                'SOAPAction'   => $action,
            ]
        );
    }

    protected function get($path, $body = '', array $headers = [])
    {
        return $this->getHttpClient()->get($path, [
                'auth' => [
                    $this->getConnection()->getUsername(),
                    $this->getConnection()->getPassword(),
                    'ntlm',
                ],
                'headers' => $headers,
                'body' => $body,
            ]);
    }

    /**
     * @return HttpClientInterface
     */
    protected function getHttpClient()
    {
        return $this->httpClientFactory->create();
    }

    protected function getWsdlUri()
    {
        return "{$this->getConnection()->getWsdlBaseUri()}/{$this->serviceObject->getName()}";
    }

    protected function getConnection()
    {
        if (!isset($this->connection)) {
            $this->connection = $this->connectionFactory->create();
        }

        return $this->connection;
    }
}
