<?php

namespace SomethingDigital\CatalogPageNumber\ViewModel;

use Magento\Framework\Registry as CoreRegistry;
use Magento\Store\Model\ScopeInterface;
use Magento\Framework\App\Config\ScopeConfigInterface;

class CatalogPageNumber implements \Magento\Framework\View\Element\Block\ArgumentInterface
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
    public function getCatalogPage()
    {
        $product = $this->getProduct();
        $catalogPageNumber = $product->getData('catalog_page_number');

        if (!$catalogPageNumber || $catalogPageNumber >= 9000) {
            return false;
        }
        
        $storeScope = ScopeInterface::SCOPE_STORES;

        $offset = $this->scopeConfig->getValue("catalog/frontend/catalog_page_number_offset", $storeScope);
        $link = $this->scopeConfig->getValue("catalog/frontend/catalog_link", $storeScope);

        $urlCatalogPageNumber = $catalogPageNumber + $offset;

        return [
            'catalog_page_number' => $catalogPageNumber,
            'catalog_link' => $link . $urlCatalogPageNumber
        ];
    }


}
