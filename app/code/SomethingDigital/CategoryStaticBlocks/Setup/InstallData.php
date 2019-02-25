<?php
namespace SomethingDigital\CategoryStaticBlocks\Setup;

use Magento\Eav\Setup\EavSetup;
use Magento\Eav\Setup\EavSetupFactory;
use Magento\Framework\Setup\InstallDataInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\ModuleDataSetupInterface;

class InstallData implements InstallDataInterface
{
    private $eavSetupFactory;

    /**
     * Init
     *
     * @param EavSetupFactory $eavSetupFactory
     */
    public function __construct(EavSetupFactory $eavSetupFactory)
    {
        $this->eavSetupFactory = $eavSetupFactory;
    }

    public function install(ModuleDataSetupInterface $setup, ModuleContextInterface $context)
    {
        /** @var EavSetup $eavSetup */
        $eavSetup = $this->eavSetupFactory->create(['setup' => $setup]);

        /**
         * Add attributes to the eav/attribute
        **/

        $eavSetup->addAttribute(
            \Magento\Catalog\Model\Category::ENTITY, 
            'static_block_left_bar', [
                'type' => 'int',
                'label' => 'Add CMS Block - Left Bar',
                'input' => 'select',
                'source' => 'Magento\Catalog\Model\Category\Attribute\Source\Page',
                'required' => false,
                'sort_order' => 25,
                'global' => \Magento\Eav\Model\Entity\Attribute\ScopedAttributeInterface::SCOPE_STORE,
                'group' => 'Content',
            ]
        );

        $eavSetup->addAttribute(
            \Magento\Catalog\Model\Category::ENTITY, 
            'static_block_bottom', [
                'type' => 'int',
                'label' => 'Add CMS Block - Bottom',
                'input' => 'select',
                'source' => 'Magento\Catalog\Model\Category\Attribute\Source\Page',
                'required' => false,
                'sort_order' => 25,
                'global' => \Magento\Eav\Model\Entity\Attribute\ScopedAttributeInterface::SCOPE_STORE,
                'group' => 'Content',
            ]
        );

    }
}