<?php

namespace SomethingDigital\MigrationProject\Migration\Data;

use Magento\Framework\Setup\SetupInterface;
use SomethingDigital\Migration\Api\MigrationInterface;
use SomethingDigital\Migration\Helper\Cms\Page as PageHelper;
use SomethingDigital\Migration\Helper\Cms\Block as BlockHelper;
use SomethingDigital\Migration\Helper\Email\Template as EmailHelper;
use Magento\Config\Model\ResourceModel\Config as ResourceConfig;

class M20181218143743CreateSampleCatalogPage2 implements MigrationInterface
{
    protected $page;
    protected $block;
    protected $bluefoot;
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
        
        // create CMS page "sample-catalog-page-2"
        $extraData = [
            'title' => 'Sample Catalog Page 2',
            'page_layout' => 'empty',
            'meta_title' => '',
            'meta_keywords' => '',
            'meta_description' => '',
            'content_heading' => '',
            'layout_update_xml' => '',
            'custom_theme' => '',
            'custom_root_template' => NULL,
            'is_active' => 1,
            'stores' => [0]
        ];

        $this->page->create('sample-catalog-page-2', 'Sample Catalog Page 2', '{{widget type="Magento\\Cms\\Block\\Widget\\Block" template="widget/static_block/default.phtml" block_id="24"}}', $extraData);

    }
}
