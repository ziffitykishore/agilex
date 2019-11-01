<?php

namespace Creatuity\Nav\Model\Task;

use Exception;
use Psr\Log\LoggerInterface;
use Magento\Sales\Api\Data\OrderInterface;
use Magento\Framework\DataObject\Factory as DataObjectFactory;
use Magento\Sales\Api\Data\OrderItemInterface;
use Creatuity\Nav\Model\Data\Processor\OrderStatusDataProcessor;
use Creatuity\Nav\Model\Data\Extractor\Nav\FieldDataExtractor;
use Creatuity\Nav\Model\Map\CollectionMap;
use Creatuity\Nav\Model\Provider\Nav\CustomerProvider;
use Creatuity\Nav\Model\Provider\Nav\FinalOrderLineProvider;
use Creatuity\Nav\Model\Provider\Nav\OrderLineProvider;
use Creatuity\Nav\Model\Provider\Nav\OrderProvider;
use Creatuity\Nav\Model\Task\Data\Generator\LineNumberDataGenerator;
use Creatuity\Nav\Model\Task\Manager\NavEntityOperationManager;
use Magento\Sales\Api\OrderRepositoryInterface;
use Creatuity\Nav\Api\Data\DataInterfaceFactory;
use Creatuity\Nav\Api\DataRepositoryInterface;
use Magento\Framework\Mail\Template\TransportBuilder;
use Magento\Store\Model\ScopeInterface;
use Magento\Framework\Mail\Template\SenderResolverInterface;
use Magento\Customer\Model\EmailNotification;
use Magento\Framework\App\Config\ScopeConfigInterface;

/**
 *
 * @SuppressWarnings(PHPMD.CyclomaticComplexity)
 * @SuppressWarnings(PHPMD.LongVariable)
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 * @SuppressWarnings(PHPMD.ExcessiveParameterList)
 * @SuppressWarnings(PHPMD.ExcessiveMethodList)
 * @SuppressWarnings(PHPMD.TooManyFields)
 */
class OrderUpdateTask implements TaskInterface
{

    /**
     * Order Sync Log type
     */
    const ORDER_SYNC = 'order_sync';

    /**
     * @var DataObjectFactory
     */
    protected $dataObjectFactory;

    /**
     * @var LoggerInterface
     */
    protected $logger;

    /**
     * @var CollectionMap
     */
    protected $orderCollectionMap;

    /**
     * @var CustomerProvider
     */
    protected $orderCustomerProvider;

    /**
     * @var OrderProvider
     */
    protected $orderProvider;

    /**
     * @var OrderLineProvider
     */
    protected $orderLineProvider;

    /**
     * @var FinalOrderLineProvider
     */
    protected $taxFinalOrderLineProvider;

    /**
     * @var FinalOrderLineProvider
     */
    protected $discountFinalOrderLineProvider;

    /**
     * @var FinalOrderLineProvider
     */
    protected $shippingFinalOrderLineProvider;

    /**
     * @var NavEntityOperationManager
     */
    protected $orderReleaseManager;

    /**
     * @var NavEntityOperationManager
     */
    protected $orderDeleteManager;

    /**
     * @var OrderStatusDataProcessor
     */
    protected $orderStatusDataProcessor;

    /**
     * @var OrderStatusDataProcessor
     */
    protected $orderFailedStatusProcessor;

    /**
     * @var LineNumberDataGenerator
     */
    protected $lineNumberDataGenerator;

    /**
     * @var FieldDataExtractor
     */
    protected $orderPrimaryKeyFieldDataExtractor;

    /**
     * @var array
     */
    protected $orderItemFilters;

    /**
     * @var OrderRepositoryInterface
     */
    protected $orderRepository;

    /**
     * @var DataInterfaceFactory
     */
    protected $navisionLoggerFactory;

    /**
     * @var DataRepositoryInterface
     */
    protected $navisionLoggerRepository;

    /**
     * @var TransportBuilder
     */
    protected $transportBuilder;

    /**
     * @var SenderResolverInterface
     */
    protected $senderResolver;

    /**
     * @var ScopeConfigInterface
     */
    protected $scopeConfig;

    /**
     *
     * @param DataObjectFactory $dataObjectFactory
     * @param LoggerInterface $logger
     * @param CollectionMap $orderCollectionMap
     * @param CustomerProvider $orderCustomerProvider
     * @param OrderProvider $orderProvider
     * @param OrderLineProvider $orderLineProvider
     * @param FinalOrderLineProvider $taxFinalOrderLineProvider
     * @param FinalOrderLineProvider $discountFinalOrderLineProvider
     * @param FinalOrderLineProvider $shippingFinalOrderLineProvider
     * @param NavEntityOperationManager $orderReleaseManager
     * @param NavEntityOperationManager $orderDeleteManager
     * @param OrderStatusDataProcessor $orderStatusDataProcessor
     * @param OrderStatusDataProcessor $orderFailedStatusProcessor
     * @param LineNumberDataGenerator $lineNumberDataGenerator
     * @param FieldDataExtractor $orderPrimaryKeyFieldDataExtractor
     * @param array $orderItemFilters
     * @param OrderRepositoryInterface $orderRepository
     */
    public function __construct(
        DataObjectFactory $dataObjectFactory,
        LoggerInterface $logger,
        CollectionMap $orderCollectionMap,
        CustomerProvider $orderCustomerProvider,
        OrderProvider $orderProvider,
        OrderLineProvider $orderLineProvider,
        FinalOrderLineProvider $taxFinalOrderLineProvider,
        FinalOrderLineProvider $discountFinalOrderLineProvider,
        FinalOrderLineProvider $shippingFinalOrderLineProvider,
        NavEntityOperationManager $orderReleaseManager,
        NavEntityOperationManager $orderDeleteManager,
        OrderStatusDataProcessor $orderStatusDataProcessor,
        OrderStatusDataProcessor $orderFailedStatusProcessor,
        LineNumberDataGenerator $lineNumberDataGenerator,
        FieldDataExtractor $orderPrimaryKeyFieldDataExtractor,
        array $orderItemFilters = [],
        OrderRepositoryInterface $orderRepository,
        DataInterfaceFactory $navisionLoggerFactory,
        DataRepositoryInterface $navisionLoggerRepository,
        TransportBuilder $transportBuilder,
        SenderResolverInterface $senderResolver,
        ScopeConfigInterface $scopeConfig
    ) {
        $this->dataObjectFactory = $dataObjectFactory;
        $this->logger = $logger;
        $this->orderCollectionMap = $orderCollectionMap;
        $this->orderCustomerProvider = $orderCustomerProvider;
        $this->orderProvider = $orderProvider;
        $this->orderLineProvider = $orderLineProvider;
        $this->taxFinalOrderLineProvider = $taxFinalOrderLineProvider;
        $this->discountFinalOrderLineProvider = $discountFinalOrderLineProvider;
        $this->shippingFinalOrderLineProvider = $shippingFinalOrderLineProvider;
        $this->orderReleaseManager = $orderReleaseManager;
        $this->orderDeleteManager = $orderDeleteManager;
        $this->orderStatusDataProcessor = $orderStatusDataProcessor;
        $this->orderFailedStatusProcessor = $orderFailedStatusProcessor;
        $this->lineNumberDataGenerator = $lineNumberDataGenerator;
        $this->orderPrimaryKeyFieldDataExtractor = $orderPrimaryKeyFieldDataExtractor;
        $this->orderItemFilters = $orderItemFilters;
        $this->orderRepository = $orderRepository;
        $this->navisionLoggerFactory = $navisionLoggerFactory;
        $this->navisionLoggerRepository = $navisionLoggerRepository;
        $this->transportBuilder = $transportBuilder;
        $this->senderResolver = $senderResolver;
        $this->scopeConfig = $scopeConfig;
    }

    /**
     * Execute method
     *
     * @return void
     */
    public function execute()
    {
        foreach ($this->orderCollectionMap->getPageIndices() as $pageIndex) {
            $this->orderCollectionMap->setPage($pageIndex);
            foreach ($this->orderCollectionMap->getKeys() as $orderIncrementId) {
                $this->updateOrder($this->orderCollectionMap->get($orderIncrementId));
            }
        }
    }

    /**
     * To Update order to NAV.
     *
     * @param OrderInterface $order
     * @SuppressWarnings(PHPMD.ExcessiveMethodLength)
     */
    protected function updateOrder(OrderInterface $order)
    {
        $customerData = [];
        $error = false;

        try {
            $customerData = $this->orderCustomerProvider->get($order);
            $this->logger->info('Customer Read, Update Successful. MAGENTO_ORDER_NO = '.$order->getIncrementId());
            $orderData = $this->orderProvider->get($order, $customerData, 'order_update_minimal_info');
            $this->logger->info('OrderUpdateMinInfo Successful. MAGENTO_ORDER_NO = '.$order->getIncrementId());
            $this->logger->info('New Order Created. NAV_ORDER_ID = '.$orderData['No']);
            foreach ($order->getAllItems() as $orderItem) {
                if ($this->isOrderItemFiltered($orderItem)) {
                    continue;
                }

                $this->orderLineProvider->get($orderItem, $orderData, $this->lineNumberDataGenerator->generate());
                $this->logger->info(
                    'OrderLineItem Updated Successful. MAGENTO_ORDER_NO = '.$order->getIncrementId()
                );
            }
            $orderData = $this->orderProvider->get($order, $customerData, 'order_update_full_info');
            $this->logger->info('OrderUpdateFullInfo Successful. MAGENTO_ORDER_NO = '.$order->getIncrementId());
            /* Commented due to NO USE
            $this->taxFinalOrderLineProvider->get($order, $orderData, $this->lineNumberDataGenerator->generate());
            $this->discountFinalOrderLineProvider->get($order, $orderData, $this->lineNumberDataGenerator->generate());
             Commented due to NO USE */
            $this->shippingFinalOrderLineProvider->get($order, $orderData, $this->lineNumberDataGenerator->generate());
            $this->logger->info('OrderLineItem Updated Successful. MAGENTO_ORDER_NO = '.$order->getIncrementId());

            $orderData = $this->orderProvider->get($order, $customerData, 'release_order');
            $this->orderReleaseManager->process($orderData);

            #To store Nav Order Id to Magento.
            if ($orderData !== null && $orderData['No']) {

                $this->saveNavisionStatus(
                    self::ORDER_SYNC,
                    true,
                    'Order Released successfully '
                    . 'in Navision. NAV_ORDER_ID = '.$orderData['No']
                );
                $this->logger->info('Order Release Successful. MAGENTO_ORDER_NO = '.$order->getIncrementId());
                $this->logger->info('Order Release Successful. NAV_ORDER_NO = '.$orderData['No']);
                $currentOrder = $this->orderRepository->get($order->getEntityId());
                $currentOrder->setNavOrderId($orderData['No']);
                $currentOrder->save();
            }

            $this->orderStatusDataProcessor->process(
                $order,
                $this->dataObjectFactory->create([
                    'increment_id' => $this->orderPrimaryKeyFieldDataExtractor->extract($orderData),
                ])
            );

            if ($orderData !== null && $orderData['External_Document_No']) {
                $this->saveNavisionStatus(
                    self::ORDER_SYNC,
                    true,
                    'Order Status changed '
                    . 'in Magento. MAGENTO_ORDER_ID = '.$orderData['External_Document_No']
                );
                $this->logger->info('Order Status Changed Successful. MAGENTO_ORDER_NO = '.$order->getIncrementId());
            }

        } catch (Exception $e) {
            $this->logger->critical($e);
            $this->saveNavisionStatus(
                self::ORDER_SYNC,
                false,
                'MAGENTO_ORDER_ID = '
                .$order->getIncrementId().' ERROR: '.$e->getMessage()
            );
            $this->orderFailedStatusProcessor->process(
                $order,
                $this->dataObjectFactory->create([
                    'increment_id' => $order->getIncrementId(),
                ])
            );
            $mailConfig = $this->setMailConfig($order);
            $this->sendEmail($mailConfig);
            $error = true;
        }

        if ($error) {
            try {
                $this->orderDeleteManager->process(
                    $this->orderProvider->get($order, $customerData, 'order_update_minimal_info')
                );
                $this->saveNavisionStatus(
                    self::ORDER_SYNC,
                    true,
                    'Order Deleted in Navision '
                    . 'for MAGENTO_ORDER_ID = '.$order->getIncrementId()
                );
                $this->logger->info('Order Delete Successful. MAGENTO_ORDER_NO = '.$order->getIncrementId());
            } catch (Exception $e) {
                $this->logger->critical($e);
                $this->saveNavisionStatus(
                    self::ORDER_SYNC,
                    false,
                    'MAGENTO_ORDER_ID = '
                    .$order->getIncrementId().' ERROR: '.$e->getMessage()
                );
            }
        }
    }

    /**
     * To filter OrderItem
     *
     * @param OrderItemInterface $orderItem
     * @return boolean
     */
    protected function isOrderItemFiltered(OrderItemInterface $orderItem)
    {
        foreach ($this->orderItemFilters as $filter) {
            if ($filter->isFiltered($orderItem)) {
                return true;
            }
        }

        return false;
    }

    /**
     * To save NAV log status
     *
     * @param string $logType
     * @param boolean $logStatus
     * @param string $description
     */
    protected function saveNavisionStatus($logType, $logStatus, $description)
    {
        $navisionData = $this->navisionLoggerFactory->create();
        $navisionData->setLogType($logType);
        $navisionData->setLogStatus($logStatus);
        $navisionData->setDescription(
            $description
        );
        $this->navisionLoggerRepository->save($navisionData);
    }

    /**
     * To Send Transactional Email
     *
     * @param array $mailConfig
     *
     * @throws \Magento\Framework\Exception\MailException
     *
     * @return NULL
     */
    protected function sendEmail(array $mailConfig)
    {
        try {
            $transport = $this->transportBuilder->setTemplateIdentifier($mailConfig['template_id'])
                ->setTemplateOptions(['area' => 'adminhtml', 'store' => $mailConfig['store_id']])
                ->setTemplateVars($mailConfig['template_variable'])
                ->setFrom($mailConfig['from_email_address'])
                ->addTo($mailConfig['to_email_address'], "NAVISION")
                ->getTransport();
            $transport->sendMessage();
            $this->logger->info('Mail Sent Successfully to Admin.');
        } catch (\Magento\Framework\Exception\MailException $ex) {
            $this->logger->info('Somthing Went Wrong. Mail Could not be Sent.');
        }
    }

    /**
     * To set Mail Configuration
     *
     * @param OrderInterface $order
     *
     * @return array
     */
    protected function setMailConfig($order)
    {
        $mailConfig = [];

        $mailConfig['template_id'] = $this->getScopeConfigValue(
            'nav/order_update/order_sync_failed_email_template',
            ScopeInterface::SCOPE_STORE,
            $order->getStoreId()
        );

        $mailConfig['from_email_address'] = $this->senderResolver->resolve(
            $this->getScopeConfigValue(
                EmailNotification::XML_PATH_REGISTER_EMAIL_IDENTITY,
                ScopeInterface::SCOPE_STORE,
                $order->getStoreId()
            ),
            $order->getStoreId()
        );

        $recipients = explode(
            ',',
            $this->getScopeConfigValue(
                'nav/order_update/order_sync_failure_mail_to',
                ScopeInterface::SCOPE_STORE,
                $order->getStoreId()
            )
        );

        $mailConfig['to_email_address'] = $recipients;
        $mailConfig['store_id'] = $order->getStoreId();
        $mailConfig['template_variable'] = ['increment_id' => $order->getIncrementId()];

        return $mailConfig;
    }

    /**
     *
     * @param string $path
     * @param string $scopeType
     * @param int    $scopeCode
     *
     * @return string
     */
    protected function getScopeConfigValue($path, $scopeType, $scopeCode)
    {
        return $this->scopeConfig->getValue($path, $scopeType, $scopeCode);
    }
}
