<?php

namespace Earthlite\MegaMenu\Block\Menu;

use Magento\Widget\Block\BlockInterface;
use Magento\Catalog\Block\Product\Context;
use Magento\Catalog\Block\Product\AbstractProduct;

class ShopbyBrands extends AbstractProduct implements BlockInterface
{
  

    protected $_brandCatalog;

    public function __construct(
        Context $context,
        \Earthlite\Category\ViewModel\CategoryData $brandCatalog,
        array $data = []
    ) {
        $this->_brandCatalog = $brandCatalog;
        parent::__construct($context, $data);
    }

    /**
     * 
     * @return string
     */
    public function getTitle()
    {
        return $this->_getData('title');
    }

    public function getCategoryId()
    {
        return $this->_getData('parentcat');
    }
    
    public function getCategory()
    {
        $categoryIds = $this->getCategoryId();
        $catIds = explode(',', $categoryIds);
        if(!empty($catIds)) {
            return $this->_brandCatalog->loadCategoryById($catIds[0]);
        }

        return false;
    }

    public function getBrands()
    {
        return $this->_brandCatalog->getBrands();
    }
}
