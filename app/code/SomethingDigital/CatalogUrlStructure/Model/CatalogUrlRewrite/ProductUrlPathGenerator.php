<?php

namespace SomethingDigital\CatalogUrlStructure\Model\CatalogUrlRewrite;

use Magento\Store\Model\ScopeInterface;
use Magento\Store\Model\StoreManagerInterface;
use Magento\Framework\App\Config\ScopeConfigInterface;
use SomethingDigital\CatalogUrlStructure\Model\System\Config\Backend\Catalog\Url\Rewrite\Prefix;
use Magento\CatalogUrlRewrite\Model\CategoryUrlPathGenerator;
use Magento\Catalog\Api\ProductRepositoryInterface;

class ProductUrlPathGenerator extends \Magento\CatalogUrlRewrite\Model\ProductUrlPathGenerator
{
    protected $scopeConfig;

    /**
     * @param StoreManagerInterface $storeManager
     * @param ScopeConfigInterface $scopeConfig
     * @param CategoryUrlPathGenerator $categoryUrlPathGenerator
     * @param ProductRepositoryInterface $productRepository
     */
    public function __construct(
        StoreManagerInterface $storeManager,
        ScopeConfigInterface $scopeConfig,
        CategoryUrlPathGenerator $categoryUrlPathGenerator,
        ProductRepositoryInterface $productRepository
    ) {
        $this->scopeConfig = $scopeConfig;
        parent::__construct($storeManager, $scopeConfig, $categoryUrlPathGenerator, $productRepository);
    }

    /**
     * Retrieve Product Url path (with category if exists)
     *
     * @param \Magento\Catalog\Model\Product $product
     * @param \Magento\Catalog\Model\Category $category
     *
     * @return string
     */
    public function getUrlPath($product, $category = null)
    {
        $path = $product->getData('url_path');
        if ($path === null) {
            $path = $product->getUrlKey()
                ? $this->prepareProductUrlKey($product)
                : $this->prepareProductDefaultUrlKey($product);
        }

        $storeScope = ScopeInterface::SCOPE_STORES;

        $category_prefix = $this->scopeConfig->getValue(Prefix::XML_PATH_CATEGORY_URL_PREFIX, $storeScope);
        $product_prefix = $this->scopeConfig->getValue(Prefix::XML_PATH_PRODUCT_URL_PREFIX, $storeScope);

        if ($category !== null) {
            $categoryUrl = str_replace($category_prefix ,'',$this->categoryUrlPathGenerator->getUrlPath($category));
            $path = $categoryUrl . '/' . $path;
        }

        if (stripos($path, $product_prefix) === 0) {
            return $path;
        } else {
            return $product_prefix . $path;
        }
    }
}
