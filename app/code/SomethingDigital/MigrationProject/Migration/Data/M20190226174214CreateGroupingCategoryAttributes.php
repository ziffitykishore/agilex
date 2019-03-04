<?php

namespace SomethingDigital\MigrationProject\Migration\Data;

use Magento\Framework\Setup\SetupInterface;
use SomethingDigital\Migration\Api\MigrationInterface;
use SomethingDigital\Migration\Helper\Cms\Page as PageHelper;
use SomethingDigital\Migration\Helper\Cms\Block as BlockHelper;
use SomethingDigital\Migration\Helper\Email\Template as EmailHelper;
use Magento\Config\Model\ResourceModel\Config as ResourceConfig;
use Magento\Eav\Setup\EavSetupFactory;

class M20190226174214CreateGroupingCategoryAttributes implements MigrationInterface
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
            'grouping_attribute_1',
            [
                'type'         => 'varchar',
                'label'        => 'Grouping Attribute 1',
                'input'        => 'text',
                'sort_order'   => 100,
                'source'       => '',
                'global'       => \Magento\Eav\Model\Entity\Attribute\ScopedAttributeInterface::SCOPE_STORE,
                'visible'      => true,
                'required'     => false,
                'default'      => null,
                'group'        => 'Content'
            ]
        );
        $eavSetup->addAttribute(
            \Magento\Catalog\Model\Category::ENTITY,
            'grouping_attribute_2',
            [
                'type'         => 'varchar',
                'label'        => 'Grouping Attribute 2',
                'input'        => 'text',
                'sort_order'   => 101,
                'source'       => '',
                'global'       => \Magento\Eav\Model\Entity\Attribute\ScopedAttributeInterface::SCOPE_STORE,
                'visible'      => true,
                'required'     => false,
                'default'      => null,
                'group'        => 'Content'
            ]
        );
        $eavSetup->addAttribute(
            \Magento\Catalog\Model\Category::ENTITY,
            'grouping_attribute_3',
            [
                'type'         => 'varchar',
                'label'        => 'Grouping Attribute 3',
                'input'        => 'text',
                'sort_order'   => 102,
                'source'       => '',
                'global'       => \Magento\Eav\Model\Entity\Attribute\ScopedAttributeInterface::SCOPE_STORE,
                'visible'      => true,
                'required'     => false,
                'default'      => null,
                'group'        => 'Content'
            ]
        );
    }
}
