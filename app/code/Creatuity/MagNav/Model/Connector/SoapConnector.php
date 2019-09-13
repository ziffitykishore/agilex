<?php

namespace Creatuity\MagNav\Model\Connector;

use Magento\Framework\App\Config\ScopeConfigInterface;

class SoapConnector
{

    /**
     * @var SoapClientFactory
     */
    protected $soapClient;

    /**
     * @var string
     */
    protected $username = '';

    /**
     * @var string
     */
    protected $password = '';

    /**
     * @var string
     */
    protected $wsdlLocation = '';

    /**
     * @var string
     */
    protected $navintModule = '';

    /**
     * @var SoapClient
     */
    protected $client;


    public function __construct($navintModule, SoapClientFactory $soapClientFactory, ScopeConfigInterface $coreConfig)
    {
        $this->soapClientFactory = $soapClientFactory;
        $this->navintModule = $navintModule;

        $this->wsdlLocation = $coreConfig->getValue('creatuity/magnav/wsdl');
        $this->password = $coreConfig->getValue('creatuity/magnav/password');
        $this->username = $coreConfig->getValue('creatuity/magnav/username');
    }

    public function __call($name, array $arguments)
    {
        $response = \call_user_func_array([$this->soapClient(), $name], $arguments);
        return $response;
    }

    /**
     * @return SoapClient
     */
    protected function soapClient()
    {
        if (!$this->client) {
            $this->client = $this->soapClientFactory->create([
                'username' => $this->username,
                'password' => $this->password,
                'wsdl' => $this->wsdlLocation . '/' . $this->navintModule,
                'options' => []
            ]);
        }
        return $this->client;
    }


}