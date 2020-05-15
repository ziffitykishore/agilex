<?php

namespace SomethingDigital\MigrationProject\Migration\Data;

use Magento\Framework\Setup\SetupInterface;
use SomethingDigital\Migration\Api\MigrationInterface;
use SomethingDigital\Migration\Helper\Cms\Page as PageHelper;
use SomethingDigital\Migration\Helper\Cms\Block as BlockHelper;
use SomethingDigital\Migration\Helper\Email\Template as EmailHelper;
use Magento\Config\Model\ResourceModel\Config as ResourceConfig;
use Magento\Eav\Setup\EavSetupFactory;

class M20200515103515UpdateTscoreAttr implements MigrationInterface
{
    protected $page;
    protected $block;
    protected $email;
    protected $resourceConfig;
    protected $eavSetupFactory;

    public function __construct(
        PageHelper $page, 
        BlockHelper $block, 
        EmailHelper $email, 
        ResourceConfig $resourceConfig,
        EavSetupFactory $eavSetupFactory
    ) {
        $this->page = $page;
        $this->block = $block;
        $this->email = $email;
        $this->resourceConfig = $resourceConfig;
        $this->eavSetupFactory = $eavSetupFactory;
    }

    public function execute(SetupInterface $setup)
    {
        try {
            $eavSetup = $this->eavSetupFactory->create(['setup' => $setup]);
            $eavSetup->updateAttribute(
                'catalog_product', 
                'SKU_tMargin_90days', 
                [
                    'attribute_code' => 'sku_tmargin_90days'
                ]
            );

            $eavSetup->updateAttribute(
                'catalog_product', 
                'SKU_tMargin_12days', 
                [
                    'attribute_code' => 'sku_tmargin_12months',
                    'label' => 'SKU_tMargin_12months'
                ]
            );

            $eavSetup->updateAttribute(
                'catalog_product', 
                'SKU_tOrders_90Days', 
                [
                    'attribute_code' => 'sku_torders_90days'
                ]
            );

            $eavSetup->updateAttribute(
                'catalog_product', 
                'SKU_tOrders_12Month', 
                [
                    'attribute_code' => 'sku_torders_12month'
                ]
            );

            $eavSetup->updateAttribute(
                'catalog_product', 
                'IG_tMargin_90days', 
                [
                    'attribute_code' => 'ig_tmargin_90days'
                ]
            );

            $eavSetup->updateAttribute(
                'catalog_product', 
                'IG_tMargin_12Month', 
                [
                    'attribute_code' => 'ig_tmargin_12month'
                ]
            );

            $eavSetup->updateAttribute(
                'catalog_product', 
                'IG_tOrders_90Days', 
                [
                    'attribute_code' => 'ig_torders_90days'
                ]
            );

            $eavSetup->updateAttribute(
                'catalog_product', 
                'IG_tOrders_12Month', 
                [
                    'attribute_code' => 'ig_torders_12month'
                ]
            ); 
        } catch (\Exception $e) {
            
        }
    }
}
