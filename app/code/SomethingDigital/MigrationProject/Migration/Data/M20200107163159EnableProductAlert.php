<?php

namespace SomethingDigital\MigrationProject\Migration\Data;

use Magento\Framework\Setup\SetupInterface;
use SomethingDigital\Migration\Api\MigrationInterface;
use SomethingDigital\Migration\Helper\Cms\Page as PageHelper;
use SomethingDigital\Migration\Helper\Cms\Block as BlockHelper;
use SomethingDigital\Migration\Helper\Email\Template as EmailHelper;
use Magento\Config\Model\ResourceModel\Config as ResourceConfig;

class M20200107163159EnableProductAlert implements MigrationInterface
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
            ["catalog/productalert/allow_stock", 1, "websites", 1],
            ["catalog/productalert/allow_stock", 1, "websites", 7],
            ["catalog/productalert/allow_stock", 1, "default", 0],
            ["cataloginventory/options/display_product_stock_status", 1, "websites", 1],
            ["cataloginventory/options/display_product_stock_status", 1, "websites", 7],
            ["cataloginventory/options/show_out_of_stock", 1, "default", 0],
        ]);
    }

    private function setConfigs($configs)
    {
        foreach ($configs as $config) {
            $this->setConfig($config[0], $config[1], $config[2], $config[3]);
        }
    }

    private function setConfig($path, $value, $scope, $scopeId)
    {
        $this->resourceConfig->saveConfig($path, $value, $scope, $scopeId);
    }
}
