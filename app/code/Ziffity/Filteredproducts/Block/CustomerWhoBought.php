<?php
namespace Ziffity\Filteredproducts\Block;

use Magento\Catalog\Block\Product\Context;
use Magento\Catalog\Api\CategoryRepositoryInterface;
use Magento\Catalog\Model\Layer\Resolver;
use Magento\Framework\Data\Helper\PostHelper;
use Magento\Framework\Url\Helper\Data;
use Magento\Sales\Model\ResourceModel\Order\Item\CollectionFactory;
use \Magento\Framework\Stdlib\DateTime\DateTime;
use Magento\Framework\Stdlib\DateTime\TimezoneInterface;

class CustomerWhoBought extends \Magento\Catalog\Block\Product\ListProduct
{
    protected $orderCollection;
    protected $registry;
    protected $date;
    protected $timezone;

    public function __construct(
        Context $context,
        PostHelper $postDataHelper,
        Resolver $layerResolver,
        CategoryRepositoryInterface $categoryRepository,
        Data $urlHelper,
        CollectionFactory $orderCollectionFactory,
        DateTime $date,
        TimezoneInterface $timezone,
        array $data = []
    )
    {
        $this->_orderCollection = $orderCollectionFactory;
        $this->_registry = $context->getRegistry();
        $this->_date = $date;
        $this->_timezone = $timezone;
        $this->search = null;
        parent::__construct(
            $context,
            $postDataHelper,
            $layerResolver,
            $categoryRepository,
            $urlHelper,
            $data
        );
    }

    protected function _getProductCollection()
    {
        $this->_productCollection = parent::_getProductCollection();
        $this->_productCollection->clear()->getSelect()->reset('where');
        return $this->_productCollection;
    }

    public function getCurrentProduct()
    {
        $productId = false;
        $currentProduct = $this->_registry->registry('current_product');
        if ($currentProduct !== NULL) {
            $productId = $currentProduct->getId();
        } 
        return $productId;
    }
    
    public function getOrderIds()
    {
        return $this->orderCollection()->getColumnValues('order_id');
    }
    
    public function getOrderItemsFactory()
    {
        return $this->_orderCollection->create();
    }

    public function orderCollection()
    {
        $currentProduct = $this->getCurrentProduct();
        $time = time();
        $to = date('Y-m-d H:i:s', $time);
        $lastTime = $time - (60*60*24*30*6);
        $from = date('Y-m-d H:i:s', $lastTime);
        $orderCollection = $this->getOrderItemsFactory()->addFieldToSelect(['order_id', 'product_id', 'created_at']);
        $orderCollection->addAttributeToFilter('created_at', array('from' => $from, 'to' => $to));
        $orderCollection->addFieldToFilter('product_id', array('eq' => $currentProduct));
        $orderCollection->getSelect()->order('item_id DESC')->limit(500);
        return $orderCollection;
    }

    public function getItemCollection()
    {
        $currentProduct = $this->getCurrentProduct();
        $orders = $this->getOrderIds();
        $itemCollection = $this->getOrderItemsFactory()->addFieldToSelect(['product_id', 'qty_ordered']);        
        $itemCollection->addFieldToFilter('product_id',array('neq' => $currentProduct));
        $itemCollection->getSelect()->where('order_id IN(?)',$orders);
        $itemCollection->getSelect()->order('qty_ordered DESC')->group('product_id');
        return $itemCollection;
    }

    public function getItemsList()
    {
        $itemCollection = $this->getItemCollection();
        return $itemCollection->getColumnValues('product_id');
    }

    public function getLoadedProductCollection()
    {    
        $productId = $this->getItemsList();
        $filterCollection = $this->_getProductCollection();
        $filterCollection->addAttributeToSelect('*');
        $filterCollection->getSelect()->where('e.entity_id IN(?)',$productId);
        $filterCollection->setPageSize(4);
        return $filterCollection;
    }
}
