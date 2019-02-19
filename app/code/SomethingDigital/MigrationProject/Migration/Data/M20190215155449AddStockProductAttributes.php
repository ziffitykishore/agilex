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

class M20190215155449AddStockProductAttributes implements MigrationInterface
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
        $this->createIntAttribute(['attribute_code' => 'wh_ca_qty', 'label' => 'Qty for Chatsworth, CA warehouse']);
        $this->createBooleanAttribute(['attribute_code' => 'wh_ca_status', 'label' => 'Stock status for Chatsworth, CA warehouse']);
        $this->createIntAttribute(['attribute_code' => 'wh_ny_qty', 'label' => 'Qty for Queens, NY warehouse']);
        $this->createBooleanAttribute(['attribute_code' => 'wh_ny_status', 'label' => 'Stock status for Queens, NY warehouse']);
        $this->createIntAttribute(['attribute_code' => 'wh_sc_qty', 'label' => 'Qty for Duncan, SC warehouse']);
        $this->createBooleanAttribute(['attribute_code' => 'wh_sc_status', 'label' => 'Stock status for Duncan, SC warehouse']);
    }

    private function createBooleanAttribute($data)
    {
        $this->categorySetup->addAttribute(
            \Magento\Catalog\Model\Product::ENTITY,
            $data['attribute_code'],
            [
                'type' => 'int',
                'backend' => '',
                'frontend' => '',
                'frontend_class' => '',
                'label' => $data['label'],
                'input' => 'boolean',
                'class' => '',
                'source' => 'Magento\Eav\Model\Entity\Attribute\Source\Boolean',
                'global' => \Magento\Eav\Model\Entity\Attribute\ScopedAttributeInterface::SCOPE_GLOBAL,
                'visible' => true,
                'required' => false,
                'user_defined' => true,
                'group' => 'General',
                'default' => '',
                'searchable' => false,
                'filterable' => false,
                'comparable' => false,
                'visible_on_front' => true,
                'used_in_product_listing' => false,
                'unique' => false,
                'apply_to' => ''
            ]
        );
    }

    private function createIntAttribute($data)
    {
        $this->categorySetup->addAttribute(
            \Magento\Catalog\Model\Product::ENTITY,
            $data['attribute_code'],
            [
                'type' => 'int',
                'backend' => '',
                'frontend' => '',
                'frontend_class' => '',
                'label' => $data['label'],
                'input' => 'text',
                'class' => '',
                'source' => '',
                'global' => \Magento\Eav\Model\Entity\Attribute\ScopedAttributeInterface::SCOPE_GLOBAL,
                'visible' => true,
                'required' => false,
                'user_defined' => true,
                'group' => 'General',
                'default' => '',
                'searchable' => false,
                'filterable' => false,
                'comparable' => false,
                'visible_on_front' => true,
                'used_in_product_listing' => false,
                'unique' => false,
                'apply_to' => ''
            ]
        );
    }
}
