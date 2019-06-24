<?php


namespace Ziffity\Pickupdate\Controller\Pickupdate;

use Ziffity\Pickupdate\Model\PickupdateRepository;
use Magento\Backend\App\Action\Context;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\Registry;
use Magento\Framework\View\Result\PageFactory;
use Magento\Sales\Controller\AbstractController\OrderViewAuthorization;
use Magento\Sales\Model\OrderRepository;
use Psr\Log\LoggerInterface;
use Magento\Framework\Exception\LocalizedException;

class Save extends \Magento\Framework\App\Action\Action
{
    /**
     * @var PickupdateRepository
     */
    private $pickupdateRepository;

    /**
     * @var Registry
     */
    protected $coreRegistry;

    /**
     * @var PageFactory
     */
    protected $resultPageFactory;

    /**
     * @var OrderRepository
     */
    private $orderRepository;

    /**
     * @var LoggerInterface
     */
    private $logInterface;

    /**
     * @var \Ziffity\Pickupdate\Helper\Data
     */
    private $pickupHelper;

    /**
     * @var OrderViewAuthorization
     */
    private $orderAuthorization;

    /**
     * @var \Magento\Framework\Stdlib\DateTime\DateTime
     */
    private $date;

    /**
     * @var \Magento\Framework\Mail\Template\TransportBuilder
     */
    protected $transportBuilder;

    /**
     * Save constructor.
     *
     * @param Context                                           $context
     * @param PickupdateRepository                            $pickupdateRepository
     * @param Registry                                          $coreRegistry
     * @param PageFactory                                       $resultPageFactory
     * @param OrderViewAuthorization                            $orderAuthorization
     * @param OrderRepository                                   $orderRepository
     * @param LoggerInterface                                   $logInterface
     * @param \Ziffity\Pickupdate\Helper\Data                  $pickupHelper
     * @param \Magento\Framework\Stdlib\DateTime\DateTime       $date
     * @param \Magento\Framework\Mail\Template\TransportBuilder $transportBuilder
     */
    public function __construct(
        Context $context,
        PickupdateRepository $pickupdateRepository,
        Registry $coreRegistry,
        PageFactory $resultPageFactory,
        OrderViewAuthorization $orderAuthorization,
        OrderRepository $orderRepository,
        LoggerInterface $logInterface,
        \Ziffity\Pickupdate\Helper\Data $pickupHelper,
        \Magento\Framework\Stdlib\DateTime\DateTime $date,
        \Magento\Framework\Mail\Template\TransportBuilder $transportBuilder
    ) {
        parent::__construct($context);
        $this->pickupdateRepository = $pickupdateRepository;
        $this->coreRegistry = $coreRegistry;
        $this->resultPageFactory = $resultPageFactory;
        $this->orderRepository = $orderRepository;
        $this->logInterface = $logInterface;
        $this->pickupHelper = $pickupHelper;
        $this->orderAuthorization = $orderAuthorization;
        $this->date = $date;
        $this->transportBuilder = $transportBuilder;
    }

    /**
     * Save Pickup Date Action for Customer
     * Can be used by Guest @see \Ziffity\Pickupdate\Controller\Guest\Save
     *
     * @return \Magento\Framework\App\ResponseInterface
     */
    public function execute()
    {
        $orderId = $this->getRequest()->getParam('order_id');
        if (!$orderId) {
            return $this->_forward('noroute');
        }
        try {
            $order = $this->getOrder($orderId);
            $pickupdate = $this->pickupdateRepository->getByOrder($orderId);
        } catch (NoSuchEntityException $e) {
            return $this->getRedirect($orderId);
        }

        if (!$pickupdate->isCanEditOnFront()) {
            return $this->getRedirect($orderId);
        }
        try {
            /* get Pickup Date value before save for compare */
            $wasDate = $pickupdate->getDate();

            if (!$pickupdate->prepareForSave($this->getRequest()->getPostValue(), $order)) {
                $this->messageManager->addErrorMessage(__('No data to save'));
                return $this->_redirect('*/*/edit', ['order_id' => $orderId]);
            }
            $pickupdate->validate($order);
            $this->pickupdateRepository->save($pickupdate);
            if ($wasDate != $pickupdate->getDate()) {
                $this->sendNotification($wasDate, $pickupdate, $order);
            }
            $this->messageManager->addSuccessMessage(__('Pickup Date has been successfully saved'));
        } catch (LocalizedException $e) {
            $this->messageManager->addErrorMessage($e->getMessage());
            return $this->_redirect('*/*/edit', ['order_id' => $orderId]);
        } catch (\Exception $e) {
            $this->messageManager->addErrorMessage(__('Something went wrong while saving data.'));
            $this->logInterface->critical($e);
        }
        return $this->getRedirect($orderId);
    }

    /**
     * Get Redirect to Order View. For Customer
     *
     * @param int $orderId
     *
     * @return \Magento\Framework\App\ResponseInterface
     */
    protected function getRedirect($orderId)
    {
        return $this->_redirect('sales/order/view', ['order_id' => $orderId]);
    }

    /**
     * @param int $orderId
     *
     * @return \Magento\Sales\Api\Data\OrderInterface
     * @throws NoSuchEntityException
     */
    protected function getOrder($orderId)
    {
        $order = $this->orderRepository->get($orderId);
        if (!$this->orderAuthorization->canView($order)) {
            throw new NoSuchEntityException();
        }
        return $order;
    }

    /**
     * Send Email notification to admin
     *
     * @param string $wasDate
     * @param \Ziffity\Pickupdate\Model\Pickupdate $pickupdate
     * @param \Magento\Sales\Api\Data\OrderInterface $order
     */
    protected function sendNotification($wasDate, $pickupdate, $order)
    {
        $recipientEmails = explode(',', $this->pickupHelper->getStoreScopeValue('editable/email'));
        if (empty($recipientEmails)) {
            return;
        }
        $value = $pickupdate->getFormattedDate();
        $pickupdate->setDate($value);

        if ($order->getCustomerIsGuest()) {
            $customerName = $order->getBillingAddress()->getName();
        } else {
            $customerName = $order->getCustomerName();
        }

        $sender = ['email' => $order->getCustomerEmail(), 'name' => $customerName];

        $vars = [
            'pickup' => $pickupdate,
            'was_date' => $this->pickupsHelper->convertDateOutput($wasDate),
            'order' => $order
        ];

        $this->transportBuilder
            ->setTemplateIdentifier('pickupdate_admin_email_template')
            ->setTemplateOptions(['area' => 'adminhtml', 'store' => 0])
            ->setTemplateVars($vars)
            ->setFrom($sender)
            ->addTo($recipientEmails);

        $transport = $this->transportBuilder->getTransport();
        $transport->sendMessage();
    }
}
