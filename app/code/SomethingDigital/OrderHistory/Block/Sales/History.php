<?php

namespace SomethingDigital\OrderHistory\Block\Sales;

use Magento\Sales\Block\Order\History as SalesHistory;
use Magento\Customer\Api\CustomerRepositoryInterface;
use Magento\Framework\View\Element\Template\Context;
use Magento\Sales\Model\ResourceModel\Order\CollectionFactory;
use Magento\Customer\Model\Session;
use Magento\Sales\Model\Order\Config;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Message\ManagerInterface;
use Psr\Log\LoggerInterface;
use SomethingDigital\OrderHistory\Model\OrdersApi;
use Magento\Framework\Data\CollectionFactory as BaseCollectionFactory;
use Magento\Framework\App\RequestInterface;
use Magento\Framework\Stdlib\ArrayManager;


class History extends SalesHistory
{

    /**
     * @var CustomerRepositoryInterface
     */
    private $customerRepo;

    /**
     * @var ManagerInterface
     */
    private $messageManager;

    /**
     * @var LoggerInterface
     */
    private $logger;

    /**
     * @var CollectionFactory
     */
    private $collectionFactory;

    /**
     * @var OrdersApi
     */
    private $ordersApi;

    /**
     * @var ArrayManager
     */
    private $arrayManager;

    /**
     * @var RequestInterface
     */
    private $request;


    public function __construct(
        Context $context,
        CollectionFactory $orderCollectionFactory,
        Session $customerSession,
        Config $orderConfig,
        CustomerRepositoryInterface $customerRepo,
        ManagerInterface $messageManager,
        LoggerInterface $logger,
        OrdersApi $ordersApi,
        BaseCollectionFactory $collectionFactory,
        RequestInterface $request,
        ArrayManager $arrayManager
    ) {
        $this->customerRepo = $customerRepo;
        $this->messageManager = $messageManager;
        $this->logger = $logger;
        $this->ordersApi = $ordersApi;
        $this->collectionFactory = $collectionFactory;
        $this->request = $request;
        $this->arrayManager = $arrayManager;
        parent::__construct($context, $orderCollectionFactory, $customerSession, $orderConfig);
    }

    public function getOrders()
    {
        try {
            $orders = $this->ordersApi->getOrders();
        } catch (LocalizedException $e) {
            $this->logger->critical('Get Orders API Request has failed with exception: ' . $e->getMessage());
        }
        $orders = $this->arrayManager->get('body', $orders, []);
        $current_page =  $this->request->getParam("p", 1);
        $limit =  $this->request->getParam("limit", 10);
        $offset =  ($current_page * $limit) - $limit;

        $collection = $this->collectionFactory->create();

        $i = 0;
        foreach ($orders as $key => $item) {
            if ($key >= $offset && $i < $limit) {
                $varienObject = new \Magento\Framework\DataObject();
                $varienObject->setData($item);
                $collection->addItem($varienObject);
                $i++;
            }
        }

        return $collection;
    }

    public function getViewUrl($order)
    {
        return $this->getUrl('sales/order/detail', ['order' => $order->getData('SxId')]);
    }

    public function formatTime($time = NULL)
    {
        return date("m/d/y", strtotime($time));
    }
}
