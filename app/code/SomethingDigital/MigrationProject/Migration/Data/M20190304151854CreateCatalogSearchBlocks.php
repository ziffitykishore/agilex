<?php

namespace SomethingDigital\MigrationProject\Migration\Data;

use Magento\Framework\Setup\SetupInterface;
use SomethingDigital\Migration\Api\MigrationInterface;
use SomethingDigital\Migration\Helper\Cms\Page as PageHelper;
use SomethingDigital\Migration\Helper\Cms\Block as BlockHelper;
use SomethingDigital\Migration\Helper\Email\Template as EmailHelper;
use Magento\Config\Model\ResourceModel\Config as ResourceConfig;

class M20190304151854CreateCatalogSearchBlocks implements MigrationInterface
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
        // create CMS block "catalogsearch_after_content"
        $extraData = [
            'title' => 'Catalog Search After Content',
            'is_active' => 1,
            'stores' => [0]
        ];

        $this->block->create('catalogsearch_after_content', 'Catalog Search After Content', '<p>Sample</p>', $extraData);

        // create CMS block "catalogsearch_after_sidebar"
        $extraData = [
            'title' => 'Catalog Search After Sidebar',
            'is_active' => 1,
            'stores' => [0]
        ];

        $this->block->create('catalogsearch_after_sidebar', 'Catalog Search After Sidebar', '<p>Sample</p>', $extraData);

        // create CMS block "catalogsearch_description"
        $extraData = [
            'title' => 'Catalog Search Description',
            'is_active' => 1,
            'stores' => [0]
        ];

        $this->block->create('catalogsearch_description', 'Catalog Search Description', '<p>Sample</p>', $extraData);
    }
}
