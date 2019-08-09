<?php
namespace SomethingDigital\ProductDetails\ViewModel;

use Magento\Framework\Registry as CoreRegistry;
use Magento\Store\Model\ScopeInterface;
use Magento\Framework\App\Config\ScopeConfigInterface;

class Manuals implements \Magento\Framework\View\Element\Block\ArgumentInterface
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
    
    public function getManuals()
    {
        $product = $this->getProduct();
        $manuals = $product->getData('documents_manuals');
        
        return $manuals;
    }
}