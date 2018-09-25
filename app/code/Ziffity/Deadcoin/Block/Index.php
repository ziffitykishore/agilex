<?php
namespace Ziffity\Deadcoin\Block;

use Magento\Framework\View\Element\Template;
use Magento\Framework\View\Element\Template\Context;
use Magento\Store\Model\StoreManagerInterface;
use Magento\Catalog\Model\ResourceModel\Product\CollectionFactory;
use Magento\Checkout\Helper\Cart;
use Magento\Catalog\Helper\Image;

class Index extends Template
{

	protected $_storeManager;
	protected $_collection;
	protected $_cart;
        protected $_image;

    public function __construct(
                Context $context,
		StoreManagerInterface $storeManager,
		CollectionFactory $collection,
		Cart $cart,
                Image $image
    )
    {
		$this->_storeManager = $storeManager;
		$this->_collection = $collection;
		$this->_cart = $cart;
                $this->_image = $image;
                parent::__construct($context);
    }


	public function getWebsiteId()
	{
		return $this->_storeManager->getStore()->getWebsiteId();		
	}

	public function getOrderCollection()
	{
		return $this->_collection->create()->addAttributeToSelect('*');
	}

	public function getProductUrl()
	{
		return $this->_cart;
	}
        
        public function getStoreObj()
        {
                return $this->_image;
        }
        

}
