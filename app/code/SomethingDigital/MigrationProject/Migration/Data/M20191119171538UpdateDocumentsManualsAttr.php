<?php

namespace SomethingDigital\MigrationProject\Migration\Data;

use Magento\Framework\Setup\SetupInterface;
use SomethingDigital\Migration\Api\MigrationInterface;
use SomethingDigital\Migration\Helper\Cms\Page as PageHelper;
use SomethingDigital\Migration\Helper\Cms\Block as BlockHelper;
use SomethingDigital\Migration\Helper\Email\Template as EmailHelper;
use Magento\Config\Model\ResourceModel\Config as ResourceConfig;
use Magento\Eav\Setup\EavSetupFactory;

class M20191119171538UpdateDocumentsManualsAttr implements MigrationInterface
{
    protected $page;
    protected $block;
    protected $email;
    protected $resourceConfig;
    private $eavSetupFactory;

    public function __construct(PageHelper $page, BlockHelper $block, EmailHelper $email, ResourceConfig $resourceConfig, EavSetupFactory $eavSetupFactory)
    {
        $this->page = $page;
        $this->block = $block;
        $this->email = $email;
        $this->resourceConfig = $resourceConfig;
        $this->eavSetupFactory = $eavSetupFactory;
    }

    public function execute(SetupInterface $setup)
    {
        $eavSetup = $this->eavSetupFactory->create(['setup' => $setup]);
        
        $eavSetup->updateAttribute(
            'catalog_product',
            'documents_manuals',
            [
                'backend_model' => null,
                'default_value' => null,
                'is_html_allowed_on_front' => true,
                'is_wysiwyg_enabled' => true
            ]
        );
    }
}
