<?php
declare(strict_types = 1);
namespace Earthlite\LateOrders\Cron;

use Psr\Log\LoggerInterface;
use Magento\Sales\Model\ResourceModel\Order\CollectionFactory as OrderCollectionFactory;
use Magento\Store\Model\StoreManagerInterface;
use Magento\Framework\Mail\Template\TransportBuilder;
use Magento\Framework\Translate\Inline\StateInterface;
use Earthlite\LateOrders\Model\LateOrders as LateOrdersHelper;
use Magento\Sales\Model\ResourceModel\OrderFactory;

/**
 * Implemented logic to filter the orders based on lead time and order statuses
 * Order status Should be pending or processing
 * If lead time is not valid the email process will be not done
 * Logged the error details into var/lateorders.log file
 * 
 * class LateOrders
 */
class LateOrders
{
    const LATE_ORDER_EMAIL_TEMPLATE = 'late_orders_email_template';
        
    /**
     *
     * @var LoggerInterface
     */
    protected $logger;
    
    /**
     *
     * @var OrderCollectionFactory
     */
    protected $orderCollectionFactory;
    
    /**
     *
     * @var StoreManagerInterface
     */
    protected $storeManagerInterface;
    
    /**
     *
     * @var TransportBuilder
     */
    protected $transportBuilder;
    
    /**
     *
     * @var StateInterface
     */
    protected $stateInterface;
    
    /**
     *
     * @var LateOrdersHelper
     */
    protected $lateOrdersHelper;

    /**
     *
     * @var OrderFactory
     */
    protected $orderResourceFactory;
    
    /**
     * LateOrders Constructor
     * @param LoggerInterface $logger
     * @param OrderCollectionFactory $orderCollectionFactory
     * @param StoreManagerInterface $storeManagerInterface
     * @param TransportBuilder $transportBuilder
     * @param StateInterface $stateInterface
     * @param LateOrdersHelper $lateOrdersHelper
     * @param OrderFactory $orderResourceFactory
     */
    public function __construct(
        LoggerInterface $logger,
        OrderCollectionFactory $orderCollectionFactory,
        StoreManagerInterface $storeManagerInterface,
        TransportBuilder $transportBuilder,
        StateInterface $stateInterface,
        LateOrdersHelper $lateOrdersHelper,
        OrderFactory $orderResourceFactory
    ) {
        $this->logger = $logger;
        $this->orderCollectionFactory = $orderCollectionFactory;
        $this->storeManagerInterface = $storeManagerInterface;
        $this->transportBuilder = $transportBuilder;
        $this->stateInterface = $stateInterface;
        $this->lateOrdersHelper = $lateOrdersHelper;
        $this->orderResourceFactory = $orderResourceFactory;
    }

    /**
     * Check and Send Email in case of delay in shipping
     */
    public function execute()
    {
        if (!$this->lateOrdersHelper->getModuleStatus())  {
            $this->logger->info("Late orders module not enabled");
            return false;
        }
        $this->logger->info("Late orders cron started");
        $orders = $this->getOrders();
        foreach ($orders as $order) {
            $this->lateOrders($order);
        }
        $this->logger->info("Late orders cron stopped");
    }
    
    /**
     * 
     * @return \Magento\Sales\Model\ResourceModel\Order\Collection
     */
    protected function getOrders()
    {
        /** @var \Magento\Sales\Model\ResourceModel\Order\Collection $orderCollection **/
        $ordersCollection = $this->orderCollectionFactory->create()
                ->addFieldToFilter('status', ['in' => ['pending','processing']])
                ->addFieldToFilter('late_order_flag', 0);
        return $ordersCollection;
    }
    
    /**
     * Checks the shipping status of individual items and send email
     * 
     * @param \Magento\Sales\Model\Order $order
     * @return void
     */
    protected function lateOrders(\Magento\Sales\Model\Order $order)
    { 
        try {
            $delayedOrder = $this->lateOrdersHelper->checkOrderDelayed($order);
            if ($delayedOrder) {
               $this->sendLateOrderEmail($order);
            }
        } catch (\Exception $e) {
            $this->logger->info("Exception on late orders:".$e->getMessage());
        }
    }
    
    /**
     * 
     * @param \Magento\Sales\Model\Order $order
     */
    protected function sendLateOrderEmail(\Magento\Sales\Model\Order $order)
    {
        $templateOptions = [
            'area' => \Magento\Framework\App\Area::AREA_FRONTEND,
            'store' => $this->storeManagerInterface->getStore($order->getStoreId())->getId()
        ];
        $templateVars = [
            'store' => $this->storeManagerInterface->getStore($order->getStoreId()),
            'customer_name' => $order->getCustomerFirstName() .' '. $order->getCustomerLastName(),
            'order' => $order
        ];
        $emailFromAddress = [
            'email' => $this->lateOrdersHelper->getStoreEmail(),
            'name' => $this->lateOrdersHelper->getStorename()
        ];
        $this->stateInterface->suspend();
        $emailToAddresses = [$order->getCustomerEmail()];
        $transport = $this->transportBuilder->setTemplateIdentifier(self::LATE_ORDER_EMAIL_TEMPLATE)
                ->setTemplateOptions($templateOptions)
                ->setTemplateVars($templateVars)
                ->setFrom($emailFromAddress)
                ->addTo($emailToAddresses)
                ->getTransport();
        $transport->sendMessage();
        $this->stateInterface->resume();
        $orderResource = $this->orderResourceFactory->create();
        $order->setLateOrderFlag(1);
        $orderResource->save($order);
        $this->logger->info("Late Order Email Sent for Order:". $order->getIncrementId());
    }
}

