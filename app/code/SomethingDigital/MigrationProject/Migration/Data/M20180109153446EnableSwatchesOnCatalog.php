<?php

namespace SomethingDigital\MigrationProject\Migration\Data;

use Magento\Framework\Setup\SetupInterface;
use SomethingDigital\Migration\Api\MigrationInterface;
use SomethingDigital\Migration\Helper\Cms\Page as PageHelper;
use SomethingDigital\Migration\Helper\Cms\Block as BlockHelper;
use SomethingDigital\Migration\Helper\Email\Template as EmailHelper;
use Magento\Config\Model\ResourceModel\Config as ResourceConfig;

class M20180109153446EnableSwatchesOnCatalog implements MigrationInterface
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
        //enable swatch on catalog page
        $this->setConfigs([
            'catalog/frontend/show_swatches_in_product_list' => '1',
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
