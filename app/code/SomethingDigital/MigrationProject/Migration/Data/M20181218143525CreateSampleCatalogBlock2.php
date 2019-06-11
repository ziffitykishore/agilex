<?php

namespace SomethingDigital\MigrationProject\Migration\Data;

use Magento\Framework\Setup\SetupInterface;
use SomethingDigital\Migration\Api\MigrationInterface;
use SomethingDigital\Migration\Helper\Cms\Page as PageHelper;
use SomethingDigital\Migration\Helper\Cms\Block as BlockHelper;
use SomethingDigital\Migration\Helper\Email\Template as EmailHelper;
use Magento\Config\Model\ResourceModel\Config as ResourceConfig;

class M20181218143525CreateSampleCatalogBlock2 implements MigrationInterface
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
        
        // create CMS block "sample_catalog_block_2"
        $extraData = [
            'title' => 'Sample Catalog Block 2',
            'is_active' => 1,
            'stores' => [0]
        ];

        $this->block->create('sample_catalog_block_2', 'Sample Catalog Block 2', '<p>Sample 2</p>', $extraData);

    }
}
