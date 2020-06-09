<?php
declare(strict_types = 1);
namespace Earthlite\LateOrders\Model;

use Magento\Sales\Model\Order;
use Magento\Framework\Stdlib\DateTime\DateTime;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Store\Model\ScopeInterface;
use Psr\Log\LoggerInterface;
use Magento\Catalog\Api\ProductRepositoryInterface;

/**
 * Currently Accepted LeadtTime formats number followed by days or day
 * 
 * class LateOrders
 */
class LateOrders
{
    const LEAD_TIME_PATTERN = '/\b([1-9]{1}[0-9]{0,2} days|[1-9]{1}[0-9]{0,2} day|[1-9]{1}[0-9]{0,2}days|[1-9]{1}[0-9]{0,2}day)\b$/';
    
    /**
     *
     * @var ProductRepositoryInterface
     */
    protected $productRepositoryInterface;
    
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
     * 
     * @param DateTime $dateTime
     * @param ScopeConfigInterface $scopeConfig
     * @param LoggerInterface $logger
     * @param ProductRepositoryInterface $productRepository
     */
    public function __construct(
        DateTime $dateTime,
        ScopeConfigInterface $scopeConfig,
        LoggerInterface $logger,
        ProductRepositoryInterface $productRepository
    ) {
        $this->dateTime = $dateTime;
        $this->scopeConfig = $scopeConfig;
        $this->logger = $logger;
        $this->productRepository = $productRepository;
    }

    /**
     *  
     * @param Order $order
     * @return bool
     */
    public function checkOrderDelayed(Order $order):bool
    {
        if (!$order->hasShipments()) {
            $modifiedLeadDatesofOrderItems = [];
            foreach ($order->getAllVisibleItems() as $item) {
                $leadTime = $item->getShippingLeadTime();
                $modifiedLeadDatesofOrderItems[] = $this->formatLeadDate($leadTime, $order->getCreatedAt());
            }
            if ($modifiedLeadDatesofOrderItems) {
                return $this->canSendEmail($modifiedLeadDatesofOrderItems); 
            }
        }
        return false;
    }
    
    /**
     * 
     * @param  $item
     * @return string|null
     */
    public function getLeadTime($item)
    {
        $product = $this->productRepository->get($item->getSku());
        $leadTime = $product->getLeadTime();
        $productionItem = $product->getProductionItem();
        if (!$leadTime && !$productionItem) {
            $leadTime = $this->getDefaultNonProductionItemLeadTime();
        }
        return $leadTime;
    }
    
    /**
     * 
     * @param type $modifiedLeadDatesofOrderItems
     * @return bool
     */
    protected function canSendEmail($modifiedLeadDatesofOrderItems):bool
    {
        $maxLeadTime = $this->dateTime->gmtDate('Y-m-d',
                max($modifiedLeadDatesofOrderItems)
             );
        $todayDate = $this->dateTime->gmtDate('Y-m-d');
        if ($maxLeadTime && $todayDate > $maxLeadTime) {
            return true;
        }
        return false;
    }


    /**
     * 
     * @param string $leadTime
     * @param string $createdAt
     * @return string
     */
    public function formatLeadDate($leadTime, $createdAt)
    { 
        if (is_numeric($leadTime)) {
            $leadTime = "$leadTime days";
        }
        if (!preg_match(self::LEAD_TIME_PATTERN, $leadTime)) {
            $this->logger->info("Invalid Lead Time: $leadTime");
            return false;
        }
        if (strpos($leadTime, 'days')) {
            $leadTime = str_replace("days", "weekdays", $leadTime);
        } else {
            $leadTime = str_replace("day", "weekdays", $leadTime);
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
    public function getDefaultNonProductionItemLeadTime()
    {
        return $this->scopeConfig->getValue(
            'earthlite_lateorders/general/defaul_lead_time',
            ScopeInterface::SCOPE_STORE
        );
    }
    
    /**
     * 
     * @return int
     */
    public function getModuleStatus()
    {
        return $this->scopeConfig->getValue(
            'earthlite_lateorders/general/late_order_enable',
            ScopeInterface::SCOPE_STORE
        );
    }
}
