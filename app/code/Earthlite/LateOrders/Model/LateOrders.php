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
    const XML_PATH_STORE_NAME = 'trans_email/ident_sales/name';
    const XML_PATH_STORE_EMAIL =  'trans_email/ident_sales/email';
    const XML_PATH_MODULE_STATUS = 'earthlite_lateorders/general/late_order_enable';
    const XML_PATH_PRODUCTION_BUFFER_TIME  = 'earthlite_lateorders/general/lead_time_production_buffer';
    const XML_PATH_NON_PRODUCTION_BUFFER_TIME  = 'earthlite_lateorders/general/lead_time_nonproduction_buffer';
    const XML_PATH_LATE_ORDERS_TEMPLATE  = 'earthlite_lateorders/general/template';

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
                if ($leadTime) {
                    $type = $item->getItemType();
                    $modifiedLeadDatesofOrderItems[] = $this->formatLeadDate($leadTime, $order,$type);
                }
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
     * @param array $modifiedLeadDatesofOrderItems
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
     * @param Order $order
     * @return string
     */
    public function formatLeadDate($leadTime, $order, $type)
    { 
        if ($type) {
            return $this->formatProductionItemLeadTime($leadTime, $order);
        } else {
            return $this->formatNonProductionItemLeadTime($leadTime, $order);
        }
    }
    
    /**
     * 
     * @param string $leadTime
     * @param Order $order
     * @return boolean|string
     */
    protected function formatProductionItemLeadTime($leadTime, $order)
    {
        if (is_numeric($leadTime)) {
            $leadTime = "$leadTime days";
        }
        if (!preg_match(self::LEAD_TIME_PATTERN, $leadTime)) {
            $this->logger->info("Invalid Lead Time: $leadTime for Order".$order->getIncrementId());
            return false;
        }
        if (strpos($leadTime, 'days')) {
            $leadTime = str_replace("days", "weekdays", $leadTime);
        } else {
            $leadTime = str_replace("day", "weekdays", $leadTime);
        }
        $leadDate = $this->dateTime->date(
                        'Y-m-d', strtotime($leadTime, strtotime($order->getCreatedAt()))
        );
         return $this->getLeadTimeWithBuffer($leadDate, true);
    }
    
    /**
     * 
     * @param string $leadTime
     * @param Order $order
     * @return string|bool
     */
    protected function formatNonProductionItemLeadTime($leadTime, $order)
    {
        $modifiedLeadTime = trim(str_replace("hours", "", strtolower($leadTime)));
        $explodeLeadTime = explode('-', $modifiedLeadTime);
        $maxLeadTime = end($explodeLeadTime);
        if (!is_numeric($maxLeadTime)) {
            $this->logger->info("Invalid Lead Time: $leadTime for Order".$order->getIncrementId());
            return false;
        }
        $leadDate = $this->dateTime->date(
                        'Y-m-d', strtotime($maxLeadTime.'hours', strtotime($order->getCreatedAt()))
        );
        return $this->getLeadTimeWithBuffer($leadDate);
    }
    
    /**
     * 
     * @param type $leadDate
     * @param type $type
     * @return type
     */
    protected function getLeadTimeWithBuffer($leadDate, $type=false)
    {
        if ($type) {
            $bufferTime = $this->getProductionItemBufferTime();
        } else {
            $bufferTime = $this->getNonProductionItemBufferTime();
        }
        if ($bufferTime) {
            if (is_numeric($bufferTime)) {
                $bufferTime = "$bufferTime days";
            }
            if (!preg_match(self::LEAD_TIME_PATTERN, $bufferTime)) {
                $this->logger->info("Invalid Buffer Time:" . $bufferTime);
                return $leadDate;
            }
            if (strpos($bufferTime, 'days')) {
                $bufferTime = str_replace("days", "weekdays", $bufferTime);
            } else {
                $bufferTime = str_replace("day", "weekdays", $bufferTime);
            }

            return $this->dateTime->date(
                            'Y-m-d', strtotime($bufferTime, strtotime($leadDate))
            );
        }
        return $leadDate;
    }
    /**
     * 
     * @return string
     */
    public function getStorename()
    {
        return $this->scopeConfig->getValue(
            self::XML_PATH_STORE_NAME,
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
            self::XML_PATH_STORE_EMAIL,
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
            self::XML_PATH_MODULE_STATUS,
            ScopeInterface::SCOPE_STORE
        );
    }
    
    /**
     * 
     * @return string|null
     */
    public function getProductionItemBufferTime()
    {
        return $this->scopeConfig->getValue(
            self::XML_PATH_PRODUCTION_BUFFER_TIME,
            ScopeInterface::SCOPE_STORE
        );
    }
    
    /**
     * 
     * @return string|null
     */
    public function getNonProductionItemBufferTime()
    {
        return $this->scopeConfig->getValue(
            self::XML_PATH_NON_PRODUCTION_BUFFER_TIME,
            ScopeInterface::SCOPE_STORE
        );
    }
    
    public function getLateOrdersTemplateId($storeId)
    {
        return $this->scopeConfig->getValue(
            self::XML_PATH_LATE_ORDERS_TEMPLATE,
            ScopeInterface::SCOPE_STORE,
            $storeId
        );
        
    }
}
