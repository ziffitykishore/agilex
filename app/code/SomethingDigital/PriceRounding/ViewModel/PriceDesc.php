<?php
namespace SomethingDigital\PriceRounding\ViewModel;

use Magento\Framework\Registry as CoreRegistry;
use Magento\Store\Model\ScopeInterface;
use Magento\Framework\App\Config\ScopeConfigInterface;

class PriceDesc implements \Magento\Framework\View\Element\Block\ArgumentInterface
{
    /**
     * @var CoreRegistry
     */
    private $coreRegistry;
    protected $scopeConfig;

    public function __construct(
        CoreRegistry $coreRegistry,
        ScopeConfigInterface $scopeConfig
    ) {
        $this->coreRegistry = $coreRegistry;
        $this->scopeConfig = $scopeConfig;
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
    
    public function getPriceDesc()
    {
        $product = $this->getProduct();
        $priceDesc = '';
        
        if ($product->getExactUnitPrice()) {
            $priceDesc = 'Price Per 100';
        }
        
        return $priceDesc;
    }
}