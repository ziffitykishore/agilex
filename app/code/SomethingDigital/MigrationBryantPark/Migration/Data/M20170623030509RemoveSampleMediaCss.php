<?php

namespace SomethingDigital\MigrationBryantPark\Migration\Data;

use Magento\Framework\Setup\SetupInterface;
use SomethingDigital\Migration\Api\MigrationInterface;
use Magento\Framework\App\Cache\Type\Config as ConfigCacheType;
use Magento\Framework\App\Config\ScopeConfigInterface as ScopeConfig;
use Magento\Framework\App\Config\Storage\WriterInterface as ConfigWriter;

class M20170623030509RemoveSampleMediaCss implements MigrationInterface
{
    protected $configWriter;
    protected $scopeConfig;
    protected $configCacheType;

    public function __construct(ConfigWriter $configWriter, ScopeConfig $scopeConfig, ConfigCacheType $configCacheType)
    {
        $this->configWriter = $configWriter;
        $this->scopeConfig = $scopeConfig;
        $this->configCacheType = $configCacheType;
    }

    public function execute(SetupInterface $setup)
    {
        $headIncludes = $this->scopeConfig->getValue('design/head/includes');
        $headIncludes = str_replace([
            '<link  rel="stylesheet" type="text/css"  media="all" href="{{MEDIA_URL}}styles.css" />',
            '<link rel="stylesheet" type="text/css" media="all" href="{{MEDIA_URL}}styles.css" />',
        ], '', $headIncludes);

        $this->configWriter->save('design/head/includes', $headIncludes);
        $this->configCacheType->clean();
    }
}
