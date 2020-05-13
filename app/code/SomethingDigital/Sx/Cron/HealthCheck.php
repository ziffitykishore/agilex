<?php

namespace SomethingDigital\Sx\Cron;

use SomethingDigital\Sx\Model\Adapter;
use Magento\Framework\HTTP\ClientFactory;
use SomethingDigital\Sx\Logger\Logger;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Store\Model\StoreManagerInterface;
use SomethingDigital\ApiMocks\Helper\Data as TestMode;
use Magento\Framework\App\Config\Storage\WriterInterface;
use Magento\Framework\App\Cache\TypeListInterface;
use Magento\Framework\Encryption\EncryptorInterface;

class HealthCheck extends Adapter
{
  public function __construct(
      ClientFactory $curlFactory,
      Logger $logger,
      ScopeConfigInterface $config,
      StoreManagerInterface $storeManager,
      TestMode $testMode,
      WriterInterface $configWriter,
      TypeListInterface $cacheTypeList,
      EncryptorInterface $encryptor
  ) {
      parent::__construct(
          $curlFactory,
          $logger,
          $config,
          $storeManager,
          $testMode,
          $configWriter,
          $cacheTypeList,
          $encryptor
      );
  }

  /**
    * The middleware and SX get slower after no requests for a while, so we ping the HealthCheck endpoint every 30 min to keep them awake.
    *
    * @return void
  */
  public function execute()
  {

      $this->requestPath = 'Api/HealthCheck';
      $result = $this->getRequest();
  }
}