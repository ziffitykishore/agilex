<?php
namespace Ziffity\Filteredproducts\Block;

class ListProduct extends \Magento\Catalog\Block\Product\ListProduct
{
    protected function _getProductCollection()
    {
        $categoryId = $this->getData('category_id');
        $type = $this->getData('attributue_type');
        $this->_productCollection = parent::_getProductCollection();
        $this->_productCollection->clear()->getSelect()->reset('where');
        if($categoryId){
           $this->_productCollection->addCategoriesFilter(['in' => [$categoryId]]);
        }
        if($type){
            $this->_productCollection->addAttributeToFilter($type, array( 'eq' => 1));
        }
        $this->_productCollection->setStoreId(1)->addStoreFilter(1);
        $this->_productCollection->addAttributeToSort('updated_at', 'desc');
        $this->_productCollection->addAttributeToFilter('status', 1);
        $this->_productCollection->addAttributeToFilter('visibility', 4);
//        $this->_productCollection->getSelect()->order(new \Zend_Db_Expr("RAND()"));
        $size = $this->getData('page_size') ? $this->getData('page_size') : 5;
        $this->_productCollection->setPageSize($size);
        return $this->_productCollection;
    }

    /**
     * Retrieve loaded category collection
     *
     * @return AbstractCollection
     */
    public function getLoadedProductCollection()
    {
        return $this->_getProductCollection();
    }
}
