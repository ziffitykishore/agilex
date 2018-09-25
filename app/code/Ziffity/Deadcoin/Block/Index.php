<?php
namespace Ziffity\Deadcoin\Block;

use Magento\Framework\View\Element\Template;
use Magento\Framework\View\Element\Template\Context;
use Magento\Store\Model\StoreManagerInterface;
use Magento\Catalog\Model\ResourceModel\Product\CollectionFactory;
use Magento\Checkout\Helper\Cart;

class Index extends Template
{

	protected $_storeManager;
	protected $_collection;
	protected $_cart;

    public function __construct(
        Context $context,
		StoreManagerInterface $storeManager,
		CollectionFactory $collection,
		Cart $cart
    )
    {
		$this->_storeManager = $storeManager;
		$this->_collection = $collection;
		$this->_cart = $cart;
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

}
