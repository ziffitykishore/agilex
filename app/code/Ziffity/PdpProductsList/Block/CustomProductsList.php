<?php
namespace Ziffity\PdpProductsList\Block;

class CustomProductsList extends \Magento\Framework\View\Element\Template
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
    
  

}
