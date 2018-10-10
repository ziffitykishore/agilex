<?php

namespace Ziffity\Dataencryption\Controller\Index;
use Magento\Framework\View\Result\PageFactory;
use Magento\Framework\App\Action\Context;
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
        return $this->resultPageFactory->create();
    }

}
