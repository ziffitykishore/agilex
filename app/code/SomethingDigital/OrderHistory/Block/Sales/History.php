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
use Magento\Framework\Pricing\PriceCurrencyInterface;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\Stdlib\DateTime\DateTime;


class History extends SalesHistory
{
    private const CUSTOMER_ORDER_HISTORY_LIMIT = 'customer/order_history_date/history_date_limit';
    private const CUSTOMER_ORDER_HISTORY_RECORD_LIMIT = 'customer/order_history_date/history_limit';

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

    /**
     * @var PriceCurrencyInterface
     */
    private $priceCurrency;

    /**
     * @var Pager
     */
    private $_pager;

    /**
     * @var ScopeConfigInterface
     */
    private $scopeConfig;

    /**
     * @var DateTime
     */
    private $date;

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
        ArrayManager $arrayManager,
        \SomethingDigital\OrderHistory\Block\Pager\Pager $pager,
        PriceCurrencyInterface $priceCurrency,
        ScopeConfigInterface $scopeConfig,
        DateTime $date
    ) {
        $this->customerRepo = $customerRepo;
        $this->messageManager = $messageManager;
        $this->logger = $logger;
        $this->ordersApi = $ordersApi;
        $this->collectionFactory = $collectionFactory;
        $this->request = $request;
        $this->arrayManager = $arrayManager;
        $this->priceCurrency = $priceCurrency;
        parent::__construct($context, $orderCollectionFactory, $customerSession, $orderConfig);
        $this->_pager = $pager;
        $this->scopeConfig = $scopeConfig;
        $this->date = $date;
    }

    protected function _prepareLayout()
    {
        parent::_prepareLayout();

        /** @var Pager $pager */
        $pager = $this->getLayout()->createBlock(Pager::class, 'custom.history.pager');
        $pager->setTemplate("SomethingDigital_OrderHistory::pager.phtml");
        $pager->setViewModel($this->_pager);
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
        $record_limit_value = (int)$this->scopeConfig->getValue(self::CUSTOMER_ORDER_HISTORY_RECORD_LIMIT);
        $params = [
            'poNumber' => $this->getRequest()->getParam('poNumber'),
            'sxOrderNumber' => $this->getRequest()->getParam('sxOrderNumber'),
            'productSku' => $this->getRequest()->getParam('productSku'),
            'recordLimit' => $record_limit_value
        ];

        $limit_value = $this->scopeConfig->getValue(self::CUSTOMER_ORDER_HISTORY_LIMIT);
        if (!empty($limit_value)) {
            $start_date = date('m/d/Y', strtotime('-'. $limit_value .' days'));
            $end_date = $this->date->gmtDate('m/d/Y');
            $params = [
                'startDate' => $start_date,
                'endDate' => $end_date
            ];
        }
        
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

        while ($offset >= sizeof($orders)) {
            //If someone is on a later page and increases the limit,
            //there is a chance the offset will be too large,
            //which causes 'no orders' to display.
            $offset -= $limit;
        }

        $collection = $this->collectionFactory->create();

        if (is_array($orders)) {
            if (!$all) {
                $orders = array_slice($orders, $offset, $limit);
            }
            foreach ($orders as $key => $item) {
                $apiResult = $this->ordersApi->getOrder($item['OrderNumber']);
                $apiResult['body']['EnterDate'] = $item['EnterDate'];
                $varienObject = new \Magento\Framework\DataObject();
                $varienObject->setData($apiResult['body']);
                $collection->addItem($varienObject);
            }
        }

        return $collection;
    }

    public function getViewUrl($sxId)
    {
        return $this->getUrl('sales/order/detail', ['order' => $sxId]);
    }

    public function formatTime($time = NULL, $format = \IntlDateFormatter::SHORT, $showDate = false)
    {
        return date("m/d/y", strtotime($time));
    }

    /**
     * Get current store currency symbol with price
     */
    public function getCurrencyFormat($price)
    {
        $price = $this->priceCurrency->format($price,true,2);
        return $price;
    }

    /**
     * Calculate order grand total
     *
     * @return float
     */
    public function getOrderGrandTotal($order)
    {
        $items = $order->getData('LineItems');
        $total = 0;
        foreach ($items as $key => $item) {
            $total += ($item['SoldPrice']*$item['Qty']);
        }
        $total += $order->getData('ShipFee');
        $total += $order->getData('Tax');

        return $total;
    }
}
