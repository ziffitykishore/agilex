<?php

namespace SomethingDigital\MigrationBryantPark\Migration\Data;

use Magento\Framework\Setup\SetupInterface;
use SomethingDigital\Migration\Api\MigrationInterface;
use SomethingDigital\Migration\Helper\Cms\Page as PageHelper;
use SomethingDigital\Migration\Helper\Cms\Block as BlockHelper;
use SomethingDigital\Migration\Helper\Email\Template as EmailHelper;
use Magento\Config\Model\ResourceModel\Config as ResourceConfig;

class M20170613191304CatalogProductsPerPage implements MigrationInterface
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
        // change default catalog products per view counts
        $this->resourceConfig->saveConfig(
            'catalog/frontend/grid_per_page_values',
            '12,48,72',
            'default',
            0
        );

        // enable 'view all'
        $this->resourceConfig->saveConfig(
            'catalog/frontend/list_allow_all',
            1,
            'default',
            0
        );
        
        // Show 12 products per page
        $this->resourceConfig->saveConfig(
            'catalog/frontend/grid_per_page',
            12,
            'default',
            0
        );
    }
}
