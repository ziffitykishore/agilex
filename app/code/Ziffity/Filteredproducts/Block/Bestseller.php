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
    public function __construct(
        \Magento\Framework\View\Element\Template\Context $context,
        \Magento\Sales\Model\ResourceModel\Report\Bestsellers\CollectionFactory $collectionFactory,
        \Magento\Reports\Block\Product\Viewed $recentlyViewed,
        array $data = array()
    )
    {     
        $this->_collectionFactory = $collectionFactory;
        $this->_recentlyViewed = $recentlyViewed;
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
    
//    public function recentProducts(){     
//        return $this->_recentlyViewed->getItemsCollection();
//    }
//    
  

}
