<?php


namespace Ziffity\Pickupdate\Controller\Adminhtml;

use Magento\Framework\View\Result\PageFactory;
use Magento\Backend\App\Action\Context;
use Psr\Log\LoggerInterface;
use Magento\Backend\Model\Session;

abstract class Holidays extends \Magento\Backend\App\Action
{

    /**
     * @var \Magento\Framework\View\Result\PageFactory
     */
    protected $resultPageFactory;

    /**
     * Core registry
     *
     * @var \Magento\Framework\Registry
     */
    protected $_coreRegistry = null;
    /**
     * @var LoggerInterface
     */
    protected $logInterface;
    /**
     * @var Session
     */
    protected $session;
    /**
     * @var \Ziffity\Pickupdate\Model\ResourceModel\Holidays
     */
    protected $resourceModel;
    /**
     * @var \Magento\Ui\Component\MassAction\Filter
     */
    protected $filter;
    /**
     * @var \Ziffity\Pickupdate\Model\ResourceModel\Holidays\Collection
     */
    protected $collection;
    /**
     * @var \Ziffity\Pickupdate\Model\HolidaysFactory
     */
    protected $model;

    /**
     * @param PageFactory $resultPageFactory
     */
    public function __construct(
        Context $context,
        PageFactory $resultPageFactory,
        \Magento\Framework\Registry $coreRegistry,
        LoggerInterface $logInterface,
        \Ziffity\Pickupdate\Model\HolidaysFactory $model,
        \Ziffity\Pickupdate\Model\ResourceModel\Holidays $resourceModel,
        \Ziffity\Pickupdate\Model\ResourceModel\Holidays\Collection $collection,
        \Magento\Ui\Component\MassAction\Filter $filter
    ) {
        parent::__construct($context);
        $this->resultPageFactory = $resultPageFactory;
        $this->_coreRegistry = $coreRegistry;
        $this->logInterface = $logInterface;
        $this->session = $context->getSession();
        $this->resourceModel = $resourceModel;
        $this->filter = $filter;
        $this->collection = $collection;
        $this->model = $model;
    }

    protected function _initAction()
    {
        $resultPage = $this->resultPageFactory->create();
        $resultPage->setActiveMenu('Ziffity_Pickupdate::pickupdate_holidays');
        $resultPage->addBreadcrumb(__('Manage Exceptions: Working Days and Holidays'), __('Manage Exceptions: Working Days and Holidays'));
        $resultPage->getConfig()->getTitle()->prepend(__('Manage Exceptions: Working Days and Holidays'));

        return $resultPage;
    }

    protected function _isAllowed()
    {
        return $this->_authorization->isAllowed('Ziffity_Pickupdate::pickupdate_holidays');
    }
}
