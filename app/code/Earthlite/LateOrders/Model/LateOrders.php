<?php
declare(strict_types = 1);
namespace Earthlite\LateOrders\Model;

use Magento\Catalog\Api\ProductRepositoryInterfaceFactory;
use Magento\Catalog\Model\ProductFactory;
use Magento\Sales\Model\Order;
use Magento\Framework\Stdlib\DateTime\DateTime;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Store\Model\ScopeInterface;
use Psr\Log\LoggerInterface;

/**
 * Currently Accepted LeadtTime formats number followed by days or day
 * 
 * class LateOrders
 */
class LateOrders
{
    /**
     *
     * @var ProductRepositoryInterfaceFactory
     */
    protected $productRepositoryInterface;
    
    /**
     *
     * @var ProductFactory 
     */
    protected $productFactory;
    
    /**
     *
     * @var DateTime
     */
    protected $dateTime;
    
    /**
     *
     * @var ScopeConfigInterface
     */
    protected $scopeConfig;
    
    /**
     *
     * @var Logger
     */
    protected $logger;


    /**
     * LateOrders Constructor
     * @param ProductRepositoryInterfaceFactory $productRepositoryInterface
     * @param ProductFactory $productFactory
     * @param DateTime $dateTime
     * @param ScopeConfigInterface $scopeConfig
     * @param LoggerInterface $logger
     */
    public function __construct(
        ProductRepositoryInterfaceFactory $productRepositoryInterface,
        ProductFactory $productFactory,
        DateTime $dateTime,
        ScopeConfigInterface $scopeConfig,
        LoggerInterface $logger
    ) {
        $this->productRepositoryInterface = $productRepositoryInterface;
        $this->dateTime = $dateTime;
        $this->productFactory = $productFactory;
        $this->scopeConfig = $scopeConfig;
        $this->logger = $logger;
    }

    /**
     * 
     * @param Order $order
     * @return array
     */
    public function getDelayedProducts(Order $order):array
    {
        $delayedProductDetails = [];
        foreach ($order->getAllVisibleItems() as $item) {
            $productModel = $this->productFactory->create();
            if ($item->getQtyOrdered() != $item->getQtyShipped() && $productModel->getIdBySku($item->getSku())) {
                $productRepository = $this->productRepositoryInterface->create();
                $productRepository->cleanCache();
                $productDetails = $productRepository->getById($item->getProductId());
                $leadTime = $productDetails->getCustomAttribute('lead_time')?$productDetails->getCustomAttribute('lead_time')->getValue():'';
                $productionItem = $productDetails->getCustomAttribute('production_item')->getValue();
                if (!$leadTime && !$productionItem) {
                    $leadTime = $this->getDefaultNonProductionItemLeadTime();
                }
                if ($leadTime) {
                    $todayDate = $this->dateTime->gmtDate('Y-m-d');
                    $leadTimeDate = $this->formatLeadDate($leadTime, $order->getCreatedAt());
                    if ($leadTimeDate && $todayDate > $leadTimeDate) {
                        $delayedProductDetails[] = ["product" => $productDetails, "orderItem" => $item];
                    }
                }
            }
        }
        return $delayedProductDetails;
    }
    
    /**
     * 
     * @param string $leadTime
     * @param string $createdAt
     * @return string
     */
    protected function formatLeadDate($leadTime, $createdAt)
    { 
        if (is_numeric($leadTime)) {
            $leadTime = "$leadTime days";
        }
        $validLeadTimePattern = '/\b([1-9]{1,3} days|[1-9]{1,} day|[1-9]{1,}days|[1-9]{1,}day)\b$/';
        if (!preg_match($validLeadTimePattern, $leadTime)) {
           $this->logger->info("Invalid Lead Time: $leadTime");
            return false; 
        }
        return $this->dateTime->date(
            'Y-m-d', strtotime($leadTime, strtotime($createdAt))
        );
    }


    /**
     * 
     * @return string
     */
    public function getStorename()
    {
        return $this->scopeConfig->getValue(
            'trans_email/ident_sales/name',
            ScopeInterface::SCOPE_STORE
        );
    }

    /**
     * 
     * @return string
     */
    public function getStoreEmail()
    {
        return $this->scopeConfig->getValue(
            'trans_email/ident_sales/email',
            ScopeInterface::SCOPE_STORE
        );
    }
    
    /**
     * 
     * @return string
     */
    protected function getDefaultNonProductionItemLeadTime()
    {
        return $this->scopeConfig->getValue(
            'earthlite_lateorders/general/defaul_lead_time',
            ScopeInterface::SCOPE_STORE
        );
    }
}
