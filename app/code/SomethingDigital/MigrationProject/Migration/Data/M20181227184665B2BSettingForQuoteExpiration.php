<?php

namespace SomethingDigital\MigrationProject\Migration\Data;

use Magento\Framework\Setup\SetupInterface;
use SomethingDigital\Migration\Api\MigrationInterface;
use SomethingDigital\Migration\Helper\Cms\Page as PageHelper;
use SomethingDigital\Migration\Helper\Cms\Block as BlockHelper;
use SomethingDigital\Migration\Helper\Email\Template as EmailHelper;
use Magento\Config\Model\ResourceModel\Config as ResourceConfig;

class M20181227184665B2BSettingForQuoteExpiration implements MigrationInterface
{
    protected $page;
    protected $block;
    protected $email;
    protected $resourceConfig;

    public function __construct(PageHelper $page, BlockHelper $block, EmailHelper $email, ResourceConfig $resourceConfig)
    {
        $this->page = $page;
        $this->block = $block;
        $this->email = $email;
        $this->resourceConfig = $resourceConfig;
    }

    public function execute(SetupInterface $setup)
    {
        $this->setConfigs([
            'quote/general/default_expiration_period' => '30',
            'quote/general/default_expiration_period_time' => 'day',
        ]);       
    }

    protected function setConfigs(array $configs)
    {
        foreach ($configs as $path => $value) {
            $this->setConfig($path, $value);
        }
    }

    protected function setConfig($path, $value)
    {
        $this->resourceConfig->saveConfig($path, $value, 'default', 0);
    }
}

