<?php
namespace Ziffity\Signifyd\Observer;

use Magento\Framework\Event;
use Magento\Framework\Event\Observer;
use Magento\Framework\Exception\AlreadyExistsException;
use Magento\Sales\Api\Data\OrderInterface;
use Magento\Signifyd\Api\CaseCreationServiceInterface;
use Magento\Signifyd\Model\Config;
use Psr\Log\LoggerInterface;
use Magento\Framework\App\Config\ScopeConfigInterface;
use \Magento\Store\Model\ScopeInterface;

class PlaceOrder extends \Magento\Signifyd\Observer\PlaceOrder
{
    const S_ENABLE = 'signifyd/general/enable';
    const S_INCLUDE_PAYMENT_METHOD = 'signifyd/general/include';
    
    /**
     * @var Config
     */
    private $signifydIntegrationConfig;

    /**
     * @var CaseCreationServiceInterface
     */
    private $caseCreationService;

    /**
     * @var LoggerInterface
     */
    private $logger;

    /**
     * @var ScopeConfigInterface
     */
    public $coreConfig;
    
    /**
     * Constructor
     * @param Config $signifydIntegrationConfig
     * @param CaseCreationServiceInterface $caseCreationService
     * @param LoggerInterface $logger
     * @param ScopeConfigInterface $coreConfig
     */
    public function __construct(
        Config $signifydIntegrationConfig,
        CaseCreationServiceInterface $caseCreationService,
        LoggerInterface $logger,            
        ScopeConfigInterface $coreConfig
    ) {
        $this->coreConfig = $coreConfig;
        $this->signifydIntegrationConfig = $signifydIntegrationConfig;
        $this->caseCreationService = $caseCreationService;
        $this->logger = $logger;
        
        /*config values*/
        $this->intScopValues($coreConfig);
    }

    /**
     * Initialize scope values
     * @param ScopeConfigInterface $coreConfig
     */
    private function intScopValues($coreConfig)
    {
        $this->enabled = $coreConfig->getValue(self::S_ENABLE , ScopeInterface::SCOPE_STORE);
        $this->incPaymentMethods = $coreConfig->getValue(self::S_INCLUDE_PAYMENT_METHOD, ScopeInterface::SCOPE_STORE);
    }

    /**
     * to check offline payments
     * @param string $method
     * @param boolean $flag
     * @return boolean
     */
    private function checkToAllowOffline($method, $flag = false)
    {
        /*saved cc method xtsavedcc*/
        $allowMethods = array_map('trim', explode(',', $this->incPaymentMethods));
        if($this->enabled && in_array($method, $allowMethods)) {
            $flag = true;
        }
        return $flag;
    }

    /**
     * {@inheritdoc}
     */
    public function execute(Observer $observer)
    {
        if (!$this->signifydIntegrationConfig->isActive()) {
            return;
        }

        $orders = $this->extractOrders(
            $observer->getEvent()
        );

        if (null === $orders) {
            return;
        }

        foreach ($orders as $order) {
            $this->createCaseForOrder($order);
        }
    }

    /**
     * Creates Signifyd case for single order with online payment method.
     *
     * @param OrderInterface $order
     * @return void
     */
    private function createCaseForOrder($order)
    {
        $orderId = $order->getEntityId();
        $allowOffline = $this->checkToAllowOffline($order->getPayment()->getMethod());
        if (null === $orderId || ($order->getPayment()->getMethodInstance()->isOffline() && !$allowOffline)) {
            return;
        }

        try {
            $this->caseCreationService->createForOrder($orderId);
        } catch (AlreadyExistsException $e) {
            $this->logger->error($e->getMessage());
        }
    }

    /**
     * Returns Orders entity list from Event data container
     *
     * @param Event $event
     * @return OrderInterface[]|null
     */
    private function extractOrders(Event $event)
    {
        $order = $event->getData('order');
        if (null !== $order) {
            return [$order];
        }

        return $event->getData('orders');
    }

}
