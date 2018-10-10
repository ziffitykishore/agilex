<?php

namespace Ziffity\Dataencryption\Controller\Index;
use Magento\Framework\View\Result\PageFactory;
use Magento\Framework\App\Action\Context;
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
class Index extends \Magento\Framework\App\Action\Action
{

    protected $resultPageFactory;
    
    public function __construct(Context $context, PageFactory $resultPageFactory)
    {
        parent::__construct($context);
        $this->resultPageFactory = $resultPageFactory;
    }

    public function execute()
    {
        
        $this->_eventManager->dispatch('ziffity_dataencryption');
       // $resultPage->setActiveMenu('Ziffity_Blockcustomers::ziffity_blockcustomers');
       // $resultPage->getConfig()->getTitle()->prepend(__('Block Customers'));
        return $this->resultPageFactory->create();
    }

}
