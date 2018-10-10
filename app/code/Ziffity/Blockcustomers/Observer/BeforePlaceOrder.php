<?php

namespace Ziffity\Blockcustomers\Observer;

use Magento\Framework\Event\ObserverInterface;
use Ziffity\Blockcustomers\Model\ResourceModel\Data\CollectionFactory;
use Magento\Framework\Message\ManagerInterface;
use Magento\Store\Model\ScopeInterface;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\UrlInterface;
use Magento\Framework\Exception\CouldNotSaveException;


class BeforePlaceOrder implements ObserverInterface
{

    protected $blockedCollection;

    protected $_messageManager;
    
    protected $scopeConfig;

    protected $_urlInterface;

    protected $_responseFactory;
    
    protected $_url;
    

    
    public function __construct(
        CollectionFactory $blockedCollection,
        ManagerInterface $messageManager,
        ScopeConfigInterface $scopeConfig,
        UrlInterface $urlInterface,
        \Magento\Framework\App\Response\RedirectInterface $redirect,
        \Magento\Framework\App\ResponseFactory $responseFactory,
        \Magento\Framework\UrlInterface $url            
       
    ) {
        $this->blockedCollection= $blockedCollection;
        $this->_messageManager = $messageManager;
        $this->scopeConfig = $scopeConfig;
        $this->_urlInterface = $urlInterface;
        $this->redirect = $redirect;
        $this->_responseFactory = $responseFactory;
        $this->_url = $url;        
    }
    
    
    public function execute(\Magento\Framework\Event\Observer $observer)
    {        

        $order = $observer->getEvent()->getOrder();
        $orderData = $order->getData();
        
        $supportEmail = $this->scopeConfig->getValue('trans_email/ident_support/email',ScopeInterface::SCOPE_STORE);
        $existCollection = $this->blockedCollection->create()->addFieldToFilter('email',$orderData['customer_email'])->addFieldToFilter('is_active',['eq'=>1]);        
        if($existCollection->getSize()){
            $this->_messageManager->addErrorMessage('You are not authorized to place an order, please contact us at '.$supportEmail);
          
             $CustomRedirectionUrl = $this->_urlInterface->getUrl('onestepcheckout/index/index');
             $this->_responseFactory->create()->setRedirect($CustomRedirectionUrl)->sendResponse();
              throw new CouldNotSaveException(__('You are not authorized to place an order, please contact us at '.$supportEmail));
            
        }
    }
}