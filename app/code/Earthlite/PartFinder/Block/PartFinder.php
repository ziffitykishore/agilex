<?php

namespace Earthlite\PartFinder\Block;

use Magento\Catalog\Block\Product\View\Description;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Store\Model\ScopeInterface;
use Magento\Framework\View\Element\Template\Context;
use Magento\Framework\Registry;

/**
 * This class fetch the custom product attribute
 * value for part finder and provide category url for Part Finder.
 */
class PartFinder extends Description
{
    /**
     * Part Finder module enable status configuration
     */
    const PART_FINDER_ENABLED = 'partfinder/general/enable';

    /**
     * Category URL key for Part Finder Category
     */
    const CATEGORY_URL_KEY = 'partfinder/general/category_url_key';

    /**
     * @var ScopeConfigInterface
     */
    private $scopeConfig;

    /**
     * @param Context $context
     * @param Registry $registry
     * @param ScopeConfigInterface $scopeConfig
     * @param array $data
     */
    public function __construct(
        Context $context,
        Registry $registry,
        ScopeConfigInterface $scopeConfig,
        array $data = []
    ) {
        $this->scopeConfig = $scopeConfig;
        parent::__construct($context, $registry, $data);
    }

    /**
     * Get part finder custom attribute value
     *
     * @return string
     */
    public function getPartFinderValue()
    {
        return $this->getProduct()->getData('sku_of_product_parts');
    }

    /**
     * Get Part Finder Category Url
     *
     * @return mixed;
     */
    public function getPartFinderCategoryUrl()
    {
        $categoryUrlKey = $this->scopeConfig->getValue(
            self::CATEGORY_URL_KEY,
            ScopeInterface::SCOPE_STORE
        );
        $parentCategory = $this->getParentCategory();
        if ($parentCategory !== false) {
            $childCategories =  $parentCategory->getChildrenCategories();
            foreach ($childCategories as $category) {
                if ($category->getUrlKey() == $categoryUrlKey) {
                    return $category->getUrl().'?partfinder='.$this->getPartFinderValue();
                }
            }
        }
        return false;
    }

    /**
     * Get parent category of the current product
     *
     * @return mixed;
     */
    protected function getParentCategory()
    {
        $categories = $this->getProduct()->getCategoryCollection();
        foreach ($categories as $category) {
            if ($category->getParentCategory()->getLevel() == 2) {
                return $category->getParentCategory();
            }
        }
        return false;
    }
}
