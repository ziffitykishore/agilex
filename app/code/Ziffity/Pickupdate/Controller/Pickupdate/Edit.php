<?php


namespace Ziffity\Pickupdate\Controller\Pickupdate;

use Ziffity\Pickupdate\Model\PickupdateRepository;
use Magento\Framework\Registry;
use Magento\Framework\View\Result\PageFactory;
use Magento\Sales\Controller\AbstractController\OrderViewAuthorization;
use Magento\Sales\Model\OrderRepository;

class Edit extends \Magento\Framework\App\Action\Action
{
    /**
     * @var PickupdateRepository
     */
    private $pickupdateRepository;

    /**
     * @var Registry
     */
    private $coreRegistry;

    /**
     * @var PageFactory
     */
    private $resultPageFactory;

    /**
     * @var OrderRepository
     */
    private $orderRepository;

    /**
     * @var \Ziffity\Pickupdate\Helper\Data
     */
    private $pickupHelper;

    /**
     * @var OrderViewAuthorization
     */
    private $orderAuthorization;

    /**
     * Edit constructor.
     *
     * @param \Magento\Backend\App\Action\Context $context
     * @param PickupdateRepository              $pickupdateRepository
     * @param Registry                            $coreRegistry
     * @param PageFactory                         $resultPageFactory
     * @param OrderViewAuthorization              $orderAuthorization
     * @param OrderRepository                     $orderRepository
     * @param \Ziffity\Pickupdate\Helper\Data    $pickupHelper
     */
    public function __construct(
        \Magento\Backend\App\Action\Context $context,
        PickupdateRepository $pickupdateRepository,
        Registry $coreRegistry,
        PageFactory $resultPageFactory,
        OrderViewAuthorization $orderAuthorization,
        OrderRepository $orderRepository,
        \Ziffity\Pickupdate\Helper\Data $pickupHelper
    ) {
        parent::__construct($context);
        $this->pickupdateRepository = $pickupdateRepository;
        $this->coreRegistry = $coreRegistry;
        $this->resultPageFactory = $resultPageFactory;
        $this->orderRepository = $orderRepository;
        $this->pickupHelper = $pickupHelper;
        $this->orderAuthorization = $orderAuthorization;
    }

    /**
     * @return \Magento\Framework\App\ResponseInterface|\Magento\Framework\View\Result\Page|void
     */
    public function execute()
    {
        $orderId = $this->getRequest()->getParam('order_id');
        if (!$orderId) {
            return $this->_forward('noroute');
        }
        try {
            $pickupdate = $this->pickupdateRepository->getByOrder($orderId);
            $order        = $this->orderRepository->get($orderId);
        } catch (\Magento\Framework\Exception\NoSuchEntityException $e) {
            return $this->_redirect('sales/order/view', ['order_id' => $orderId]);
        }

        if (!$this->orderAuthorization->canView($order) || !$pickupdate->isCanEditOnFront()) {
            return $this->_redirect('sales/order/history');
        }

        $this->coreRegistry->register('current_ziffity_pickupdate', $pickupdate);
        $this->coreRegistry->register('current_order', $order);

        $resultPage = $this->resultPageFactory->create();
        /** @var \Magento\Framework\View\Element\Html\Links $navigationBlock */
        $navigationBlock = $resultPage->getLayout()->getBlock('customer_account_navigation');
        if ($navigationBlock) {
            $navigationBlock->setActive('sales/order/history');
        }

        $title = __('Edit Pickup Date For The Order #%1', $order->getIncrementId());
        $resultPage->getConfig()->getTitle()->prepend($title);

        return $resultPage;
    }
}
