<?php

namespace SomethingDigital\MigrationProject\Migration\Data;

use Magento\Framework\Setup\SetupInterface;
use SomethingDigital\Migration\Api\MigrationInterface;
use SomethingDigital\Migration\Helper\Cms\Page as PageHelper;
use SomethingDigital\Migration\Helper\Cms\Block as BlockHelper;
use SomethingDigital\Migration\Helper\Email\Template as EmailHelper;
use Magento\Config\Model\ResourceModel\Config as ResourceConfig;
use Magento\Catalog\Api\Data\ProductInterfaceFactory;
use Magento\Framework\App\State;


class M20190311155101CreateSampleProducts implements MigrationInterface
{
    protected $page;
    protected $block;
    protected $email;
    protected $resourceConfig;
    protected $productFactory;
    private $state;

    public function __construct(
        PageHelper $page, 
        BlockHelper $block, 
        EmailHelper $email, 
        ResourceConfig $resourceConfig,
        ProductInterfaceFactory $productFactory,
        State $state
    ) {
        $this->page = $page;
        $this->block = $block;
        $this->email = $email;
        $this->resourceConfig = $resourceConfig;
        $this->productFactory = $productFactory;
        $this->state = $state;
    }

    public function execute(SetupInterface $setup)
    {
        $this->state->setAreaCode(\Magento\Framework\App\Area::AREA_ADMINHTML);

        //1st Product Tiered pricing
        $product = $this->productFactory->create();
        $product->setSku('sample-product-tp');
        $product->setName('Sample Product - Tiered Pricing');
        $product->setDescription('Test');
        $product->setUrlKey('sample-product-tiered-pricing');
        $product->setTypeId(\Magento\Catalog\Model\Product\Type::TYPE_SIMPLE);
        $product->setAttributeSetId(4);
        $product->setWebsiteIds(array(1));
        $product->setStatus(1);
        $product->setVisibility(4);
        $product->setStockData(
            array(
                'use_config_manage_stock' => 1,
                'manage_stock' => 1,
                'is_in_stock' => 1,
                'qty' => 999
            )
        );
        $product->setPrice(100);
        $product->setTierPrice(
            array(
                [ 
                    'website_id' => 0,
                    'cust_group' => 1, 
                    'price_qty' => 1 , 
                    'price' => 90.0 
                ]
            )
        );
        $product->setCategoryIds(array(7,10));
        $product->save();

        // 2nd Product - Qty Tiered Pricing
        $product = $this->productFactory->create();
        $product->setSku('sample-product-qty-tp');
        $product->setName('Sample Product - Qty Tiered Pricing');
        $product->setDescription('Test');
        $product->setUrlKey('sample-product-qty-tiered-pricing');
        $product->setTypeId(\Magento\Catalog\Model\Product\Type::TYPE_SIMPLE);
        $product->setAttributeSetId(4);
        $product->setWebsiteIds(array(1));
        $product->setStatus(1);
        $product->setVisibility(4);
        $product->setStockData(
            array(
                'use_config_manage_stock' => 1,
                'manage_stock' => 1,
                'is_in_stock' => 1,
                'qty' => 999
            )
        );
        $product->setPrice(100);
        $product->setTierPrice(
            array(
                [ 
                    'website_id' => 0,
                    'cust_group' => 0, 
                    'price_qty' => 10 , 
                    'price' => 90.0 
                ],
                [ 
                    'website_id' => 0,
                    'cust_group' => 0, 
                    'price_qty' => 20 , 
                    'price' => 70.0 
                ]
            )
        );
        $product->setCategoryIds(array(7,10));
        $product->save();

        // 3rd product - Sale pricing
        $product = $this->productFactory->create();
        $product->setSku('sample-product-special-price');
        $product->setName('Sample Product - Special Price');
        $product->setDescription('Test');
        $product->setUrlKey('sample-product-special-price');
        $product->setTypeId(\Magento\Catalog\Model\Product\Type::TYPE_SIMPLE);
        $product->setAttributeSetId(4);
        $product->setWebsiteIds(array(1));
        $product->setStatus(1);
        $product->setVisibility(4);
        $product->setStockData(
            array(
                'use_config_manage_stock' => 1,
                'manage_stock' => 1,
                'is_in_stock' => 1,
                'qty' => 999
            )
        );
        $product->setPrice(100);
        $product->setSpecialPrice(49);
        $product->setCategoryIds(array(7,10));
        $product->save();

        // 4rd product - Qty increments
        $product = $this->productFactory->create();
        $product->setSku('sample-product-qty-increments');
        $product->setName('Sample Product - Qty increments');
        $product->setDescription('Test');
        $product->setUrlKey('sample-product-qty-increments');
        $product->setTypeId(\Magento\Catalog\Model\Product\Type::TYPE_SIMPLE);
        $product->setAttributeSetId(4);
        $product->setWebsiteIds(array(1));
        $product->setStatus(1);
        $product->setVisibility(4);
        $product->setStockData(
            array(
                'use_config_manage_stock' => 1,
                'manage_stock' => 1,
                'is_in_stock' => 1,
                'qty' => 999,
                'qty_increments' => 5
            )
        );
        $product->setPrice(100);
        $product->setCategoryIds(array(7,10));
        $product->save();

        // 5th - Special Price & Qty Increments
        $product = $this->productFactory->create();
        $product->setSku('sample-product-special-price-qty-increments');
        $product->setName('Sample Product - Special price & Qty increments');
        $product->setDescription('Test');
        $product->setUrlKey('sample-product-special-price-qty-increments');
        $product->setTypeId(\Magento\Catalog\Model\Product\Type::TYPE_SIMPLE);
        $product->setAttributeSetId(4);
        $product->setWebsiteIds(array(1));
        $product->setStatus(1);
        $product->setVisibility(4);
        $product->setStockData(
            array(
                'use_config_manage_stock' => 1,
                'manage_stock' => 1,
                'is_in_stock' => 1,
                'qty' => 999,
                'qty_increments' => 8
            )
        );
        $product->setPrice(100);
        $product->setSpecialPrice(59);
        $product->setCategoryIds(array(7,10));
        $product->save();

        // 6th - Special Price & Qty Increments & Tier pricing
        $product = $this->productFactory->create();
        $product->setSku('sample-product-special-price-qty-increments-tier-pricing');
        $product->setName('Sample Product - Special price & Qty increments & Tier pricing');
        $product->setDescription('Test');
        $product->setUrlKey('sample-product-special-price-qty-increments-tier-pricing');
        $product->setTypeId(\Magento\Catalog\Model\Product\Type::TYPE_SIMPLE);
        $product->setAttributeSetId(4);
        $product->setWebsiteIds(array(1));
        $product->setStatus(1);
        $product->setVisibility(4);
        $product->setStockData(
            array(
                'use_config_manage_stock' => 1,
                'manage_stock' => 1,
                'is_in_stock' => 1,
                'qty' => 999,
                'qty_increments' => 8
            )
        );
        $product->setPrice(100);
        $product->setSpecialPrice(59);
        $product->setTierPrice(
            array(
                [ 
                    'website_id' => 0,
                    'cust_group' => 0, 
                    'price_qty' => 10 , 
                    'price' => 80.0 
                ],
                [ 
                    'website_id' => 0,
                    'cust_group' => 0, 
                    'price_qty' => 20 , 
                    'price' => 65.0 
                ]
            )
        );
        $product->setCategoryIds(array(7,10));
        $product->save();
    }
}
