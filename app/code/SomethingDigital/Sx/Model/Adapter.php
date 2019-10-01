<?php

namespace SomethingDigital\Sx\Model;

use SomethingDigital\Sx\Exception\ApiRequestException;
use Magento\Framework\HTTP\ClientFactory;
use Psr\Log\LoggerInterface;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Store\Model\StoreManagerInterface;
use SomethingDigital\ApiMocks\Helper\Data as TestMode;
use Magento\Framework\App\Config\Storage\WriterInterface;
use Magento\Framework\App\Cache\TypeListInterface;
use Magento\Framework\Encryption\EncryptorInterface;

abstract class Adapter
{
    const XML_PATH_API_URL = 'sx/general/url';
    const XML_PATH_API_TOKEN = 'sx/general/token';
    const XML_PATH_API_TOKEN_EXPIRES = 'sx/general/token_expires';
    const XML_PATH_API_USERNAME = 'sx/general/username';
    const XML_PATH_API_PASSWORD = 'sx/general/password';

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

    /** @var TestMode */
    protected $testMode;

    protected $configWriter;
    protected $cacheTypeList;
    protected $encryptor;

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
        StoreManagerInterface $storeManager,
        TestMode $testMode,
        WriterInterface $configWriter,
        TypeListInterface $cacheTypeList,
        EncryptorInterface $encryptor
    ) {
        $this->curlFactory = $curlFactory;
        $this->logger = $logger;
        $this->config = $config;
        $this->storeManager = $storeManager;
        $this->testMode = $testMode;
        $this->configWriter = $configWriter;
        $this->cacheTypeList = $cacheTypeList;
        $this->encryptor = $encryptor;
    }
    
    protected function getRequest()
    {
        $token = $this->getToken();

        if (!$this->isTokenValid()) {
            $token = $this->refreshToken();
        }

        /** @var \Magento\Framework\HTTP\Client\Curl $curl */
        $curl = $this->curlFactory->create();
        $curl->setTimeout(40);
        if ($this->isTestMode()) {
            $curl->setOption(CURLOPT_SSL_VERIFYHOST, 0);
            $curl->setOption(CURLOPT_SSL_VERIFYPEER, 0);
        } else {
            $curl->addHeader('Authorization', 'Bearer ' . $token);
            $curl->addHeader('Cache-Control', 'no-cache');
        }
        try {
            $curl->get($this->getRequestUrl());
            return [
                'status' => $curl->getStatus(),
                'body' => \Zend_Json::decode($curl->getBody()),
            ];
        } catch (\Exception $e) {
            $this->logger->alert('Request to ' . $this->getRequestUrl() . ' has failed with exception: ' . $e->getMessage());
            $this->logger->alert($e);
            return [];
        }
    }

    protected function postRequest()
    {
        $token = $this->getToken();
        if (!$this->isTokenValid()) {
            $token = $this->refreshToken();
        }

        /** @var \Magento\Framework\HTTP\Client\Curl $curl */
        $curl = $this->curlFactory->create();
        $curl->setTimeout(200);
        if ($this->isTestMode()) {
            $curl->setOption(CURLOPT_SSL_VERIFYHOST, 0);
            $curl->setOption(CURLOPT_SSL_VERIFYPEER, 0);
            $curl->addHeader('X-Requested-With', 'XMLHttpRequest');
        } else {
            $curl->addHeader('Authorization', 'Bearer ' . $token);
            $curl->addHeader('Cache-Control', 'no-cache');
        }
        $curl->addHeader('Content-Type', 'application/json');
        if (empty($this->requestBody)) {
            throw new ApiRequestException(__('Empty SX API request'));
        }
        try {

            $curl->post($this->getRequestUrl(), json_encode($this->requestBody));

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
     * Check whether SX API mock is enabled
     *
     * @return bool
     */
    protected function isTestMode()
    {
        return $this->testMode->isEnabled();
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

    /**
     * Get base URL for SX API
     *
     * @return type
     */

    protected function getApiBaseUrl()
    {
        if (!$this->isTestMode()) {
            return $this->getConfig(static::XML_PATH_API_URL) . '/';
        } else {
            return $this->storeManager->getStore()->getBaseUrl();
        }
    }

    /**
     * @return string
     */
    protected function getToken()
    {
        return $this->getConfig(static::XML_PATH_API_TOKEN);
    }

    /**
     * @return string
     */
    protected function refreshToken()
    {
        $this->requestPath = 'Token';
        $this->requestBody = [
            'grant_type' => 'password',
            'username' => $this->getConfig(static::XML_PATH_API_USERNAME),
            'password' => $this->getConfig(static::XML_PATH_API_PASSWORD)
        ];

        /** @var \Magento\Framework\HTTP\Client\Curl $curl */
        $curl = $this->curlFactory->create();
        $curl->setTimeout(40);
        $curl->addHeader('Cache-Control', 'no-cache');
        $curl->addHeader('Content-Type', 'application/x-www-form-urlencoded');
        if (empty($this->requestBody)) {
            throw new ApiRequestException(__('Empty SX API request'));
        }
        try {
            $curl->post($this->getRequestUrl(), $this->requestBody);
            if ($curl->getStatus() == 200) {
                $body = \Zend_Json::decode($curl->getBody());

                $this->configWriter->save(static::XML_PATH_API_TOKEN,  $this->encryptor->encrypt($body['access_token']), $scope = ScopeConfigInterface::SCOPE_TYPE_DEFAULT, $scopeId = 0);
                $this->configWriter->save(static::XML_PATH_API_TOKEN_EXPIRES,  $body['.expires'], $scope = ScopeConfigInterface::SCOPE_TYPE_DEFAULT, $scopeId = 0);

                $this->cacheTypeList->cleanType('config');

                return $body['access_token'];
            }
        } catch (\Exception $e) {
            $this->logger->critical(
                sprintf('Request to %s has failed with exception: %s; request data: %s; response: %s', $this->getRequestUrl(), $e->getMessage(), json_encode($this->requestBody), $curl->getBody())
            );
            $this->logger->critical($e);
            throw new ApiRequestException(__('Internal error during request to SX API'));
        }
    }

    /**
     * @return boolean
     */
    protected function isTokenValid()
    {
        if (time() > strtotime($this->getConfig(static::XML_PATH_API_TOKEN_EXPIRES))) {
            return false;
        } else {
            return true;
        }
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
