<?php
namespace Ziffity\Reports\Controller\Adminhtml\Salestatus;
use Magento\Backend\App\Action\Context;
use Magento\Framework\View\Result\PageFactory;
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
class Index extends \Magento\Backend\App\Action
{
    protected $resultPageFactory;
    public function __construct(Context $context, PageFactory $resultPageFactory)
    {
        parent::__construct($context);
        $this->resultPageFactory = $resultPageFactory;
    }
    
    public function execute()
    {
        $resultPage = $this->resultPageFactory->create();
        $resultPage->getConfig()->getTitle()->prepend(__('PRODUCT - SALE STATUS REPORT'));
        return $resultPage;
        
    }

}
