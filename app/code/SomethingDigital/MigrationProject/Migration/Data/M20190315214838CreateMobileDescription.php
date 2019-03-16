<?php

namespace SomethingDigital\MigrationProject\Migration\Data;

use Magento\Framework\Setup\SetupInterface;
use SomethingDigital\Migration\Api\MigrationInterface;
use SomethingDigital\Migration\Helper\Cms\Page as PageHelper;
use SomethingDigital\Migration\Helper\Cms\Block as BlockHelper;
use SomethingDigital\Migration\Helper\Email\Template as EmailHelper;
use Magento\Config\Model\ResourceModel\Config as ResourceConfig;
use Magento\Eav\Setup\EavSetupFactory;

class M20190315214838CreateMobileDescription implements MigrationInterface
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
        /** @var EavSetup $eavSetup */
        $eavSetup = $this->eavSetupFactory->create(['setup' => $setup]);

        /**
         * Add attributes to the eav/attribute
        **/
        $eavSetup->addAttribute(
            \Magento\Catalog\Model\Category::ENTITY,
            'mobile_description',
            [
                'type'         => 'text',
                'label'        => 'Mobile Description',
                'input'        => 'textarea',
                'sort_order'   => 30,
                'source'       => '',
                'global'       => \Magento\Eav\Model\Entity\Attribute\ScopedAttributeInterface::SCOPE_STORE,
                'visible'      => true,
                'required'     => false,
                'default'      => null,
                'group'        => 'Content'
            ]
        );

        // create CMS block "catalogsearch_mobile_description"
        $extraData = [
            'title' => 'Catalog Search Mobile Description',
            'is_active' => 1,
            'stores' => [0]
        ];

        $this->block->create('catalogsearch_mobile_description', 'Catalog Search Mobile Description', '<p>Sample</p>', $extraData);
    }
}
