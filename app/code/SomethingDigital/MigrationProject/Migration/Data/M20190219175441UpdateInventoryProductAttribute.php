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

class M20190219175441UpdateInventoryProductAttribute implements MigrationInterface
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
        $this->categorySetup->updateAttribute(
            \Magento\Catalog\Model\Product::ENTITY,
            'sx_inventory_status',
            'source_model',
            \SomethingDigital\StockInfo\Model\Product\Attribute\Source\SxInventoryStatus::class
        );
    }
}
