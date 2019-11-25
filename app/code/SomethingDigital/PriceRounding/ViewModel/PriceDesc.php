<?php
namespace SomethingDigital\PriceRounding\ViewModel;

use Magento\Framework\Registry as CoreRegistry;
use Magento\Store\Model\ScopeInterface;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\Pricing\PriceCurrencyInterface;

class PriceDesc implements \Magento\Framework\View\Element\Block\ArgumentInterface
{
    /**
     * @var CoreRegistry
     */
    private $coreRegistry;
    protected $scopeConfig;
    protected $currency;

    public function __construct(
        CoreRegistry $coreRegistry,
        ScopeConfigInterface $scopeConfig,
        PriceCurrencyInterface $currency
    ) {
        $this->coreRegistry = $coreRegistry;
        $this->scopeConfig = $scopeConfig;
        $this->currency = $currency;
    }

    /**
     * Retrieve current product model
     *
     * @return \Magento\Catalog\Model\Product
     */
    public function getProduct()
    {
        return $this->coreRegistry->registry('product');
    }

    /**
     * Get custom product status
     *
     * @return string
     */
    
    public function getExactPrice()
    {
        $product = $this->getProduct();
        $priceDesc = '';
        
        if ($product->getExactUnitPrice()) {
            $priceDesc = $this->currency->getCurrency()->getCurrencySymbol() . $product->getExactUnitPrice();
        }
        
        return $priceDesc;
    }
}