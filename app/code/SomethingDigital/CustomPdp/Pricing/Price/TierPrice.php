<?php
namespace SomethingDigital\CustomPdp\Pricing\Price;

use Magento\CatalogInventory\Api\StockRegistryInterface;
use Magento\Catalog\Model\Product;
use Magento\Framework\Pricing\Adjustment\CalculatorInterface;
use Magento\Framework\Pricing\PriceCurrencyInterface;
use Magento\Customer\Model\Session;
use Magento\Customer\Api\GroupManagementInterface;
use Magento\Customer\Model\Group\RetrieverInterface as CustomerGroupRetrieverInterface;

class TierPrice extends \Magento\Catalog\Pricing\Price\TierPrice
{
    private $stockRegistry;

    public function __construct(
        StockRegistryInterface $stockRegistry,
        Product $saleableItem,
        $quantity,
        CalculatorInterface $calculator,
        PriceCurrencyInterface $priceCurrency,
        Session $customerSession,
        GroupManagementInterface $groupManagement,
        CustomerGroupRetrieverInterface $customerGroupRetriever = null

    ) {
        $this->stockRegistry = $stockRegistry;
        parent::__construct(
            $saleableItem,
            $quantity,
            $calculator,
            $priceCurrency,
            $customerSession,
            $groupManagement,
            $customerGroupRetriever = null
        );
    }

    public function getMinSaleQty()
    {
        $stockItem = $this->stockRegistry->getStockItem($this->product->getId());
        
        return $stockItem->getMinSaleQty();
    }

    public function getBasePrice()
    {
        return parent::getBasePrice();
    }

    public function getMsrpPrice()
    {
        $attribute = $this->product->getCustomAttribute('manufacturer_price');
        if (!$attribute) {
            return 0;
        }
        return $attribute->getValue();
    }

    public function getSavePercent($amount)
    {
        $basePrice = $this->getBasePrice();
        $msrp = $this->getMsrpPrice();
        $max_price = max($msrp, $basePrice);
        if (!is_float($amount)) {
            $amount = $amount->getValue();
        }
        return round(
            100 - ((100 / $max_price) * $amount)
        );
    }
}