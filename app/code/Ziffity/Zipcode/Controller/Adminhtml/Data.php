<?php

namespace Ziffity\Zipcode\Controller\Adminhtml;

use Magento\Backend\App\Action;
use Magento\Backend\App\Action\Context;
use Magento\Framework\Registry;
use Magento\Framework\View\Result\PageFactory;
use Magento\Backend\Model\View\Result\ForwardFactory;
use Ziffity\Zipcode\Api\DataRepositoryInterface;

abstract class Data extends Action
{

    const ACTION_RESOURCE = 'Ziffity_Zipcode::data';

    protected $dataRepository;

    protected $coreRegistry;

    protected $resultPageFactory;

    protected $resultForwardFactory;

    public function __construct(
        Registry $registry,
        DataRepositoryInterface $dataRepository,
        PageFactory $resultPageFactory,
        ForwardFactory $resultForwardFactory,
        Context $context
    ) {
        $this->coreRegistry         = $registry;
        $this->dataRepository       = $dataRepository;
        $this->resultPageFactory    = $resultPageFactory;
        $this->resultForwardFactory = $resultForwardFactory;
        parent::__construct($context);
    }
}
