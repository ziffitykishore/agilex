<?php
namespace SomethingDigital\PriceRounding\Setup;

use Magento\Framework\Setup\InstallDataInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\ModuleDataSetupInterface;
use Magento\Catalog\Setup\CategorySetup;

class InstallData implements InstallDataInterface
{
    private $categorySetup;

    /**
     * Init
     *
     * @param CategorySetup $categorySetup
     */
    public function __construct(CategorySetup $categorySetup)
    {
        $this->categorySetup = $categorySetup;
    }

    public function install(ModuleDataSetupInterface $setup, ModuleContextInterface $context)
    {
        $this->categorySetup->addAttribute(
            \Magento\Catalog\Model\Product::ENTITY,
            'exact_unit_price',
            [
                'type' => 'text',
                'label' => 'Exact Unit Price',
                'input' => 'text',
                'visible' => true,
                'required' => false,
                'user_defined' => true,
                'searchable' => false,
                'filterable' => false,
                'comparable' => false,
                'visible_in_advanced_search' => false,
                'used_in_product_listing' => true,
                'is_used_in_grid' => false,
                'is_visible_in_grid' => false,
                'is_filterable_in_grid' => false,
                'group' => 'General',
                'source' => '',
                'global' => \Magento\Eav\Model\Entity\Attribute\ScopedAttributeInterface::SCOPE_GLOBAL,
            ]
        );

        $this->categorySetup->addAttribute(
            \Magento\Catalog\Model\Product::ENTITY,
            'manufacturer_exact_unit_price',
            [
                'type' => 'text',
                'label' => 'Manufacturer Exact Unit Price',
                'input' => 'text',
                'visible' => true,
                'required' => false,
                'user_defined' => true,
                'searchable' => false,
                'filterable' => false,
                'comparable' => false,
                'visible_in_advanced_search' => false,
                'used_in_product_listing' => true,
                'is_used_in_grid' => false,
                'is_visible_in_grid' => false,
                'is_filterable_in_grid' => false,
                'group' => 'General',
                'source' => '',
                'global' => \Magento\Eav\Model\Entity\Attribute\ScopedAttributeInterface::SCOPE_GLOBAL,
            ]
        );

        $this->categorySetup->addAttribute(
            \Magento\Catalog\Model\Product::ENTITY,
            'special_exact_unit_price',
            [
                'type' => 'text',
                'label' => 'Special Exact Unit Price',
                'input' => 'text',
                'visible' => true,
                'required' => false,
                'user_defined' => true,
                'searchable' => false,
                'filterable' => false,
                'comparable' => false,
                'visible_in_advanced_search' => false,
                'used_in_product_listing' => true,
                'is_used_in_grid' => false,
                'is_visible_in_grid' => false,
                'is_filterable_in_grid' => false,
                'group' => 'General',
                'source' => '',
                'global' => \Magento\Eav\Model\Entity\Attribute\ScopedAttributeInterface::SCOPE_GLOBAL,
            ]
        );
    }
}