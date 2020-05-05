<?php
declare(strict_types = 1);
namespace Earthlite\OrderComments\Observer;

use Magento\Framework\Event\ObserverInterface;
use Magento\Framework\Event\Observer as EventObserver;
use Magento\Quote\Api\Data as QuoteApi;
use Magento\Quote\Model\QuoteRepository;
use Psr\Log\LoggerInterface;
use Magento\Framework\Event\ManagerInterface;
use Magento\Checkout\Api\Data\ShippingInformationInterface;

/**
 * class OrderComments
 */
class OrderComments implements ObserverInterface
{

    protected $_eventManager;
    protected $quoteRepository;

    /**
     * @var LoggerInterface
     */
    protected $logger;

    /**
     * OrderComments constructor
     * 
     * @param ManagerInterface $eventManager
     * @param QuoteRepository $quoteRepository
     * @param ShippingInformationInterface $addressInformation
     * @param LoggerInterface $logger
     */
    public function __construct(
        ManagerInterface $eventManager,
        QuoteRepository $quoteRepository,
        ShippingInformationInterface $addressInformation,
        LoggerInterface $logger
    ) {
        $this->_eventManager = $eventManager;
        $this->quoteRepository = $quoteRepository;
        $this->addressInformation = $addressInformation;
        $this->logger = $logger;
    }

    /**
     * @param EventObserver $observer
     * @return $this|void
     */
    public function execute(\Magento\Framework\Event\Observer $observer)
    {
        try {
            $order = $observer->getEvent()->getOrder();
            $quote = $observer->getEvent()->getQuote();
            $order->setData('order_comments', $quote->getOrderComments());
        } catch (\Exception $e) {
            $this->logger->info("Order Comments:".$e->getMessage());
        }
        return $this;
    }
}