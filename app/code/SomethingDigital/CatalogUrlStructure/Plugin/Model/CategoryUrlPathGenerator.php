<?php

namespace SomethingDigital\CatalogUrlStructure\Plugin\Model;

use SomethingDigital\CatalogUrlStructure\Model\System\Config\Backend\Catalog\Url\Rewrite\Prefix;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Store\Model\ScopeInterface;


class CategoryUrlPathGenerator
{
    const XML_USE_PARENT_CATEGORY_PATH = 'catalog/seo/use_parent_category_path_for_category_urls';

    protected $scopeConfig;

    public function __construct(
        ScopeConfigInterface $scopeConfig
    ) {
        $this->scopeConfig = $scopeConfig;
    }
    /**
    * Add a prefix to category URL path
    *
    * @param \Magento\CatalogUrlRewrite\Model\CategoryUrlPathGenerator $subject
    * @param $path
    * @return string
    */
    public function afterGetUrlPath(\Magento\CatalogUrlRewrite\Model\CategoryUrlPathGenerator $subject, $path)
    {
        $storeScope = ScopeInterface::SCOPE_STORES;

        $category_prefix = $this->scopeConfig->getValue(Prefix::XML_PATH_CATEGORY_URL_PREFIX, $storeScope);

        $useParentPath = $this->scopeConfig->getValue(self::XML_USE_PARENT_CATEGORY_PATH, $storeScope);
        if (!$useParentPath) {
            $pathArr = explode('/',$path);
            $path = end($pathArr);
        }

        if (strpos($path,$category_prefix) === false) {
            $path = $category_prefix . $path;
            $path = str_replace('//', "/", $path);
        }

        return $path;
    }
}
