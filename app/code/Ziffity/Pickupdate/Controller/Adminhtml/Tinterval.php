<?php


namespace Ziffity\Pickupdate\Controller\Adminhtml;

use Magento\Framework\View\Result\PageFactory;
use Magento\Backend\App\Action\Context;
use Psr\Log\LoggerInterface;
use Magento\Backend\Model\Session;
use Magento\Ui\Component\MassAction\Filter;

abstract class Tinterval extends \Magento\Backend\App\Action
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
     * @var \Ziffity\Pickupdate\Model\ResourceModel\Tinterval
     */
    protected $resourceModel;
    /**
     * @var \Ziffity\Pickupdate\Model\ResourceModel\Tinterval\Collection
     */
    protected $tintervalCollection;
    /**
     * @var Filter
     */
    protected $filter;
    /**
     * @var \Ziffity\Pickupdate\Model\Tinterval
     */
    protected $model;
    /**
     * @var \Magento\Store\Model\StoreManagerInterface
     */
    protected $storeManager;
    /**
     * @var \Magento\Framework\Stdlib\DateTime\DateTime
     */
    protected $date;
    /**
     * @var \Ziffity\Pickupdate\Helper\Data
     */
    protected $pickupHelper;

    /**
     * @param PageFactory $resultPageFactory
     */
    public function __construct(
        Context $context,
        PageFactory $resultPageFactory,
        \Magento\Framework\Registry $coreRegistry,
        LoggerInterface $logInterface,
        \Ziffity\Pickupdate\Model\ResourceModel\Tinterval $resourceModel,
        \Ziffity\Pickupdate\Model\TintervalFactory $model,
        \Ziffity\Pickupdate\Model\ResourceModel\Tinterval\Collection $tintervalCollection,
        Filter $filter,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Magento\Framework\Stdlib\DateTime\DateTime $date,
        \Ziffity\Pickupdate\Helper\Data $pickupHelper
    ) {
        parent::__construct($context);
        $this->resultPageFactory = $resultPageFactory;
        $this->_coreRegistry = $coreRegistry;
        $this->logInterface = $logInterface;
        $this->session = $context->getSession();
        $this->resourceModel = $resourceModel;
        $this->tintervalCollection = $tintervalCollection;
        $this->filter = $filter;
        $this->model = $model;
        $this->storeManager = $storeManager;
        $this->date = $date;
        $this->pickupHelper = $pickupHelper;
    }

    protected function _initAction()
    {
        $resultPage = $this->resultPageFactory->create();
        $resultPage->setActiveMenu('Ziffity_Pickupdate::pickupdate_tinterval');
        $resultPage->addBreadcrumb(__('Manage Time Intervals'), __('Manage Time Intervals'));
        $resultPage->getConfig()->getTitle()->prepend(__('Manage Time Intervals'));

        return $resultPage;
    }

    protected function _isAllowed()
    {
        return $this->_authorization->isAllowed('Ziffity_Pickupdate::pickupdate_tinterval');
    }
}
