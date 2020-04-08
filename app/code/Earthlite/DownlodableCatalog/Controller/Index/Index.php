<?php
declare(strict_types = 1);

namespace Earthlite\DownlodableCatalog\Controller\Index;

use Magento\Framework\App\Action\Action;
use Magento\Framework\App\Action\Context;
use Magento\Framework\View\Result\PageFactory;

/**
 * class Index
 */
class Index extends Action 
{
    /**
     * @var PageFactory
     */
    protected $pageFactory;

    /**
     * Index Constructor
     * 
     * @param Context $context
     * @param PageFactory $pageFactory
     */
    public function __construct(
        Context $context, 
        PageFactory $pageFactory
    ){
        $this->pageFactory = $pageFactory;
        parent::__construct($context);
    }

    /**
     * Downlodable Catalog Action
     * 
     * @return \Magento\Framework\Controller\ResultInterface
     */
    public function execute() 
    {
        $resultPage = $this->pageFactory->create();
        return $resultPage;
    }

}
