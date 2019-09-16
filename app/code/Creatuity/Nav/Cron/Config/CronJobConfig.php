<?php

namespace Creatuity\Nav\Cron\Config;

use Magento\Framework\App\Config\ScopeConfigInterface;

class CronJobConfig
{
    protected $configBasePath;
    protected $scopeConfig;

    public function __construct(
        $configBasePath,
        ScopeConfigInterface $scopeConfig
    ) {
        $this->configBasePath = $configBasePath;
        $this->scopeConfig = $scopeConfig;
    }

    public function getConfigValue($fieldName)
    {
        return $this->scopeConfig->getValue("{$this->getConfigPath($fieldName)}");
    }

    public function getConfigPath($fieldName)
    {
        return "{$this->getConfigBasePath()}/{$fieldName}";
    }

    protected function getConfigBasePath()
    {
        return rtrim($this->configBasePath, '/');
    }
}
