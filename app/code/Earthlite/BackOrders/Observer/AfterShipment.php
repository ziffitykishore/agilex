<?php
namespace Earthlite\BackOrders\Observer;

use Magento\Framework\Event\ObserverInterface;
use Magento\Sales\Model\Order;
use Magento\Catalog\Model\ProductFactory;
use Psr\Log\LoggerInterface;
use Magento\Store\Model\StoreManagerInterface;
use Magento\Framework\Mail\Template\TransportBuilder;
use Magento\Framework\Translate\Inline\StateInterface;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Store\Model\ScopeInterface;
use Earthlite\BackOrders\Model\OrderItems;

class AfterShipment implements ObserverInterface
{
    const BACK_ORDER_EMAIL_TEMPLATE = 'back_orders_email_template';
        
    /**
     *
     * @var LoggerInterface
     */
    protected $logger;

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

    protected $orderItems;
    

    public function __construct(
        StoreManagerInterface $storeManagerInterface,
        TransportBuilder $transportBuilder,
        StateInterface $stateInterface,        
        LoggerInterface $logger,
        OrderItems $orderItems
    ) {
        $this->storeManagerInterface = $storeManagerInterface;
        $this->transportBuilder = $transportBuilder;
        $this->stateInterface = $stateInterface;
        $this->logger = $logger;
        $this->orderItems = $orderItems;
    }

    /**
     *
     * @param \Magento\Framework\Event\Observer $observer
     * @return $this
     */
    public function execute(\Magento\Framework\Event\Observer $observer)
    {
       
        if($this->orderItems->isEnabled()) {
            $shipment = $observer->getEvent()->getShipment();
            
            $order = $shipment->getOrder();

            $delayedtItems = $this->orderItems->getDelayedItems($order);       

            if(!empty($delayedtItems)) {
                $this->sendBackOrderEmail($order, $delayedtItems);
            }
        }
    }

    /**
     * 
     * @param \Magento\Sales\Model\Order $order
     */
    protected function sendBackOrderEmail(\Magento\Sales\Model\Order $order, $items)
    {
        $templateOptions = [
            'area' => \Magento\Framework\App\Area::AREA_FRONTEND,
            'store' => $this->storeManagerInterface->getStore($order->getStoreId())->getId()
        ];

        $templateVars = [
            'store' => $this->storeManagerInterface->getStore($order->getStoreId()),
            'customer_name' => $order->getCustomerFirstName() .' '. $order->getCustomerLastName(),
            'order' => $order,
            'items' => $items
        ];
        $emailFromAddress = [
            'email' => $this->orderItems->getStoreEmail(),
            'name' => $this->orderItems->getStorename()
        ];
        $this->stateInterface->suspend();
        $emailToAddresses = [$order->getCustomerEmail()];
        $transport = $this->transportBuilder->setTemplateIdentifier(self::BACK_ORDER_EMAIL_TEMPLATE)
            ->setTemplateOptions($templateOptions)
            ->setTemplateVars($templateVars)
            ->setFrom($emailFromAddress)
            ->addTo($emailToAddresses)
            ->getTransport();
        $transport->sendMessage();
        $this->stateInterface->resume();
        

        return true;
    }
}