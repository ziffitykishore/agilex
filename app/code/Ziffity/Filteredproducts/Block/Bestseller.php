<?php
namespace Ziffity\Filteredproducts\Block;
/**
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * This is the Summary for this element.
 * 
 * @inheritDoc
 */
class Bestseller extends \Magento\Framework\View\Element\Template
{
    protected $collectionFactory;
    protected $recentlyViewed;
    protected $productFactory;
    protected $listProductBlock;
    public function __construct(
        \Magento\Framework\View\Element\Template\Context $context,
        \Magento\Sales\Model\ResourceModel\Report\Bestsellers\CollectionFactory $collectionFactory,
        \Magento\Catalog\Block\Product\ListProduct $listProductBlock,
        \Magento\Catalog\Model\ProductFactory $productFactory,
        array $data = []
    )
    {     
        $this->_collectionFactory = $collectionFactory;
        $this->listProductBlock = $listProductBlock;
        $this->_productFactory = $productFactory;
        parent::__construct($context, $data);
    }
    
     public function _prepareLayout()
    {
        return parent::_prepareLayout();
    }
    
    public function getLoadedProductCollection(){
         $collection = $this->_collectionFactory->create()->setModel(
            'Magento\Catalog\Model\Product'
        );
        return $collection;
        
    }
    
    public function getList($products){ 
        $array_list = explode(",",$products);
        
        $model = $this->_productFactory->create();
        $productCollection = $model->getCollection()
            ->addFieldToFilter('entity_id', array('in'=> $array_list));
        //$productCollection->load();
        return $productCollection;
    }
    public function getAddToCartPostParams($product)
    { 
        return $this->listProductBlock->getAddToCartPostParams($product);
    }
    
  

}
