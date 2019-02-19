<?php

namespace SomethingDigital\MigrationProject\Migration\Data;

use Magento\Framework\Setup\SetupInterface;
use SomethingDigital\Migration\Api\MigrationInterface;
use SomethingDigital\Migration\Helper\Cms\Page as PageHelper;
use SomethingDigital\Migration\Helper\Cms\Block as BlockHelper;
use SomethingDigital\Migration\Helper\Email\Template as EmailHelper;
use Magento\Config\Model\ResourceModel\Config as ResourceConfig;
use Magento\Directory\Model\ResourceModel\Currency;
use Magento\Catalog\Setup\CategorySetup;

class M20190218183904AddInventoryProductAttributes implements MigrationInterface
{
    protected $page;
    protected $block;
    protected $email;
    protected $resourceConfig;

    /**
     * @var Currency
     */
    private $currency;

    private $categorySetup;

    public function __construct(
        PageHelper $page,
        BlockHelper $block,
        EmailHelper $email,
        ResourceConfig $resourceConfig,
        Currency $currency,
        CategorySetup $categorySetup
    ) {
        $this->page = $page;
        $this->block = $block;
        $this->email = $email;
        $this->resourceConfig = $resourceConfig;
        $this->currency = $currency;
        $this->categorySetup = $categorySetup;
    }

    public function execute(SetupInterface $setup)
    {
        $this->categorySetup->addAttribute(
            \Magento\Catalog\Model\Product::ENTITY,
            'sx_inventory_status',
            [
                'type' => 'int',
                'label' => 'Inventory Status',
                'input' => 'select',
                'visible' => true,
                'required' => false,
                'user_defined' => true,
                'searchable' => false,
                'filterable' => false,
                'comparable' => false,
                'visible_in_advanced_search' => false,
                'used_in_product_listing' => true,
                'is_used_in_grid' => true,
                'is_visible_in_grid' => true,
                'is_filterable_in_grid' => true,
                'group' => 'General',
                'source' => \Magento\Eav\Model\Entity\Attribute\Source\Table::class,
                'global' => \Magento\Eav\Model\Entity\Attribute\ScopedAttributeInterface::SCOPE_GLOBAL,
                'option' => ['values' => ['DNR', 'Order as needed', 'Stock']],
            ]
        );
    }
}
