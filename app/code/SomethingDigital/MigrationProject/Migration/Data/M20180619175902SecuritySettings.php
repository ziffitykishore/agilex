<?php

namespace SomethingDigital\MigrationProject\Migration\Data;

use Magento\Config\Model\ResourceModel\Config as ResourceConfig;
use Magento\Framework\Setup\SetupInterface;
use SomethingDigital\Migration\Api\MigrationInterface;

class M20180619175902SecuritySettings implements MigrationInterface
{
    protected $resourceConfig;
    public function __construct(ResourceConfig $resourceConfig)
    {
        $this->resourceConfig = $resourceConfig;
    }
    public function execute(SetupInterface $setup)
    {
        $this->resourceConfig->saveConfig('msp_securitysuite_twofactorauth/general/enabled', 1, 'default', 0);
        $this->resourceConfig->saveConfig('msp_securitysuite_twofactorauth/general/force_providers', 'google', 'default', 0);
        $this->resourceConfig->saveConfig('msp_securitysuite_twofactorauth/google/enabled', 1, 'default', 0);
    }
}
