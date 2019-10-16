<?php

namespace SomethingDigital\CategoryMenu\Setup;

use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\ModuleDataSetupInterface;
use Magento\Framework\Setup\InstallDataInterface;
use Magento\Eav\Setup\EavSetup;
use Magento\Eav\Setup\EavSetupFactory;
use Magento\Catalog\Model\Category;

class InstallData implements InstallDataInterface
{
    /**
     * @var EavSetupFactory
     */
    private $eavSetupFactory;

    public function __construct(EavSetupFactory $eavSetupFactory)
    {
        $this->eavSetupFactory = $eavSetupFactory;
    }

    public function install(ModuleDataSetupInterface $setup, ModuleContextInterface $context)
    {
        /** @var EavSetup $eavSetup */
        $eavSetup = $this->eavSetupFactory->create(['setup' => $setup]);

        $eavSetup->addAttribute(Category::ENTITY, 'menu_static_block', [
            'type' => 'varchar',
            'label' => 'Menu Static Block',
            'input' => 'text',
            'required' => false,
            'user_defined' => true,
            'sort_order' => 7,
            'global' => \Magento\Eav\Model\Entity\Attribute\ScopedAttributeInterface::SCOPE_STORE,
            'group' => 'General Information',
        ]);

        $eavSetup->addAttribute(Category::ENTITY, 'mobile_menu_static_block', [
            'type' => 'varchar',
            'label' => 'Mobile Menu Static Block (Optional)',
            'input' => 'text',
            'required' => false,
            'user_defined' => true,
            'sort_order' => 8,
            'global' => \Magento\Eav\Model\Entity\Attribute\ScopedAttributeInterface::SCOPE_STORE,
            'group' => 'General Information',
        ]);
    }
}
