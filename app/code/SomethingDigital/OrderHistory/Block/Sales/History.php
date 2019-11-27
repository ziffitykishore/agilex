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
use Magento\Theme\Block\Html\Pager;


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

    private $ordersApiResponse;

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

    protected function _prepareLayout()
    {
        parent::_prepareLayout();

        /** @var Pager $pager */
        $pager = $this->getLayout()->createBlock(Pager::class, 'custom.history.pager');
        $pager->setShowPerPage(true)->setCollection(
            $this->getApiOrders(true)
        );
        $this->setChild('pager', $pager);

        return $this;
    }

    public function getPagerHtml()
    {
        return $this->getChildHtml('pager');
    }

    public function getApiOrders($all = false)
    {
        $orders = [];
        $params = [
            'poNumber' => $this->getRequest()->getParam('poNumber'),
            'sxOrderNumber' => $this->getRequest()->getParam('sxOrderNumber'),
            'productSku' => $this->getRequest()->getParam('productSku'),
            'recordLimit' => 100
        ];
        try {
            if (empty($this->ordersApiResponse)) {
                $this->ordersApiResponse = $this->ordersApi->getOrders($params);
            }
        } catch (LocalizedException $e) {
            $this->logger->critical('Get Orders API Request has failed with exception: ' . $e->getMessage());
        }
        $orders = $this->arrayManager->get('body', $this->ordersApiResponse, []);
        $current_page =  $this->request->getParam("p", 1);
        $limit =  $this->request->getParam("limit", 10);
        $offset =  ($current_page * $limit) - $limit;

        $collection = $this->collectionFactory->create();

        if (is_array($orders)) {
            if (!$all) {
                $orders = array_slice($orders, $offset, $limit);
            }
            foreach ($orders as $key => $item) {
                $varienObject = new \Magento\Framework\DataObject();
                $varienObject->setData($item);
                $collection->addItem($varienObject);
            }
        }

        return $collection;
    }

    public function getViewUrl($order)
    {
        return $this->getUrl('sales/order/detail', ['order' => $order->getData('SxId')]);
    }

    public function formatTime($time = NULL, $format = \IntlDateFormatter::SHORT, $showDate = false)
    {
        return date("m/d/y", strtotime($time));
    }
}
