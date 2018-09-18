<?php
namespace Magebees\Lazyload\Helper;
use Magento\Framework\Filesystem;

class Data extends \Magento\Framework\App\Helper\AbstractHelper
{
   
	 public function __construct(
        \Magento\Framework\App\Helper\Context $context,
        \Magento\Store\Model\StoreManagerInterface $storeManager) {        
        $this->_storeManager = $storeManager;  	
	    parent::__construct($context);
    }
	 public function getConfig()
    {
        return $this->scopeConfig->getValue('lazyload/setting', \Magento\Store\Model\ScopeInterface::SCOPE_STORE);
    }
   
	 public function getImageUrl($image)
    {
        $image_url=$this->_storeManager->getStore()
               ->getBaseUrl(\Magento\Framework\UrlInterface::URL_TYPE_MEDIA).'lazyload/'.$image;
        return $image_url;
    }
   
}