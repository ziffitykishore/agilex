<?php


namespace Ziffity\Pickupdate\Controller\Adminhtml;

use Magento\Backend\Model\Session;
use Magento\Framework\View\Result\PageFactory;

abstract class Pickupdate extends \Magento\Backend\App\Action
{

    /**
     * @var \Ziffity\Pickupdate\Model\ResourceModel\Pickupdate
     */
    protected $resourceModel;
    /**
     * @var \Ziffity\Pickupdate\Model\PickupdateFactory
     */
    protected $model;
    /**
     * @var \Ziffity\Pickupdate\Model\ResourceModel\Pickupdate\Collection
     */
    protected $pickupdateCollection;

    /**
     * @var Session
     */
    protected $session;
    /**
     * @var \Magento\Framework\Registry
     */
    protected $coreRegistry;
    /**
     * @var PageFactory
     */
    protected $resultPageFactory;
    /**
     * @var \Magento\Sales\Model\OrderFactory
     */
    protected $orderFactory;
    /**
     * @var \Magento\Sales\Model\ResourceModel\Order
     */
    protected $orderResource;
    /**
     * @var \Psr\Log\LoggerInterface
     */
    protected $logInterface;
    /**
     * @var \Ziffity\Pickupdate\Helper\Data
     */
    protected $pickupHelper;
    /**
     * @var \Magento\Framework\Stdlib\DateTime\DateTime
     */
    protected $date;
    /**
     * @var \Magento\Framework\Mail\Template\TransportBuilder
     */
    protected $transportBuilder;

    public function __construct(
        \Magento\Backend\App\Action\Context $context,
        \Ziffity\Pickupdate\Model\ResourceModel\Pickupdate $resourceModel,
        \Ziffity\Pickupdate\Model\PickupdateFactory $model,
        \Ziffity\Pickupdate\Model\ResourceModel\Pickupdate\Collection $pickupdateCollection,
        \Magento\Framework\Registry $coreRegistry,
        PageFactory $resultPageFactory,
        \Magento\Sales\Model\OrderFactory $orderFactory,
        \Magento\Sales\Model\ResourceModel\Order $orderResource,
        \Psr\Log\LoggerInterface $logInterface,
        \Ziffity\Pickupdate\Helper\Data $pickupHelper,
        \Magento\Framework\Stdlib\DateTime\DateTime $date,
        \Magento\Framework\Mail\Template\TransportBuilder $transportBuilder
    )
    {
        parent::__construct($context);
        $this->resourceModel = $resourceModel;
        $this->model = $model;
        $this->pickupdateCollection = $pickupdateCollection;
        $this->session = $context->getSession();
        $this->coreRegistry = $coreRegistry;
        $this->resultPageFactory = $resultPageFactory;
        $this->orderFactory = $orderFactory;
        $this->orderResource = $orderResource;
        $this->logInterface = $logInterface;
        $this->pickupsHelper = $pickupHelper;
        $this->date = $date;
        $this->transportBuilder = $transportBuilder;
    }

    protected function _isAllowed()
    {
        return $this->_authorization->isAllowed('Ziffity_Pickupdate::pickupdate_pickupdate');
    }
}
