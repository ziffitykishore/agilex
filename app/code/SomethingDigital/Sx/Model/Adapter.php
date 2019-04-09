<?php

namespace SomethingDigital\Sx\Model;

use SomethingDigital\OracleApi\Exception\ApiRequestException;
use Magento\Framework\HTTP\ClientFactory;
use Psr\Log\LoggerInterface;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Store\Model\StoreManagerInterface;

abstract class Adapter
{
    const XML_PATH_API_URL = 'sx/general/url';
    const XML_PATH_API_TOKEN = 'sx/general/token';

    /** @var \Magento\Framework\HTTP\ClientFactory */
    protected $curlFactory;

    /** @var \Psr\Log\LoggerInterface */
    protected $logger;

    /** @var \Magento\Framework\App\Config\ScopeConfigInterface */
    protected $config;

    /** @var \Magento\Store\Model\StoreManagerInterface */
    protected $storeManager;

    /** @var array|string */
    protected $requestBody = [];

    /** @var string */
    protected $requestPath = '';

    /** @var string */
    protected $path;

    /**
     * Adapter constructor.
     * @param \Magento\Framework\HTTP\ClientFactory $curlFactory
     * @param \Psr\Log\LoggerInterface $logger
     * @param \Magento\Framework\App\Config\ScopeConfigInterface $config
     * @param \Magento\Store\Model\StoreManagerInterface $storeManager
     */

    public function __construct(
        ClientFactory $curlFactory,
        LoggerInterface $logger,
        ScopeConfigInterface $config,
        StoreManagerInterface $storeManager
    ) {
        $this->curlFactory = $curlFactory;
        $this->logger = $logger;
        $this->config = $config;
        $this->storeManager = $storeManager;
        $this->setRequestPath();
    }
    
    protected function getRequest()
    {
        /** @var \Magento\Framework\HTTP\Client\Curl $curl */
        $curl = $this->curlFactory->create();
        $curl->setTimeout(20);
        $curl->addHeader('Authorization', 'Bearer ' . $this->getToken());
        $curl->addHeader('cache-control', 'no-cache');
        try {
            $curl->get($this->getRequestUrl());
            return [
                'status' => $curl->getStatus(),
                'body' => \Zend_Json::decode($curl->getBody()),
            ];
        } catch (\Exception $e) {
            $this->logger->alert('Request to ' . $this->getRequestUrl() . ' has failed with exception: ' . $e->getMessage());
            $this->logger->alert($e);
            throw new ApiRequestException(__('Internal error during request to SX API'));
        }
    }

    protected function postRequest()
    {
        /** @var \Magento\Framework\HTTP\Client\Curl $curl */
        $curl = $this->curlFactory->create();
        $curl->setTimeout(45);
        $curl->addHeader('Authorization', 'Bearer ' . $this->getToken());
        $curl->addHeader('Cache-Control', 'no-cache');
        $curl->addHeader('Content-Type', 'application/x-www-form-urlencoded');
        if (empty($this->requestBody)) {
            throw new ApiRequestException(__('Empty SX API request'));
        }
        try {
            $curl->post($this->getRequestUrl(), $this->requestBody);
            return [
                'status' => $curl->getStatus(),
                'body' => \Zend_Json::decode($curl->getBody()),
            ];
        } catch (\Exception $e) {
            $this->logger->critical(
                sprintf('Request to %s has failed with exception: %s; request data: %s; response: %s', $this->getRequestUrl(), $e->getMessage(), json_encode($this->requestBody), $curl->getBody())
            );
            $this->logger->critical($e);
            throw new ApiRequestException(__('Internal error during request to SX API'));
        }
    }

    /**
     * Get URL for current SX API endpoint
     *
     * @return string
     */
    protected function getRequestUrl()
    {
        return $this->getApiBaseUrl() . $this->requestPath;
    }

    protected function setRequestPath()
    {
        if (!$this->path) {
            throw new \Exception('API endpoint path is not defined');
        }
        
        $this->requestPath = $this->path;

        return $this;
    }

    /**
     * Get base URL for SX API
     *
     * @return type
     */

    protected function getApiBaseUrl()
    {
        return $this->getConfig(static::XML_PATH_API_URL) . '/';
    }

    /**
     * @return string
     */
    protected function getToken()
    {
        return $this->getConfig(static::XML_PATH_API_TOKEN);
    }

    /**
     * Get config value by path
     *
     * @param string $path
     * @return string
     */
    protected function getConfig($path)
    {
        return $this->config->getValue($path, \Magento\Store\Model\ScopeInterface::SCOPE_STORE);
    }
    
}