<?php
declare(strict_types = 1);
namespace Earthlite\EstimatedShipping\Model\Shipping;

use Magento\Catalog\Api\ProductRepositoryInterface;
use Magento\Framework\Stdlib\DateTime\DateTime;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Checkout\Model\CartFactory;
use Magento\CatalogInventory\Api\StockStateInterfaceFactory;

/**
 * class Estimation
 */
class Estimation 
{

    const CONFIG_MODULE_PATH = 'estimate_shipping';
    
    /**
     *
     * @var ProductRepositoryInterface 
     */
    protected $productRepository;
    
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
     * @var CartFactory 
     */
    protected $cart;

    public function __construct(
        ProductRepositoryInterface $productRepository, 
        DateTime $dateTime, 
        ScopeConfigInterface $scopeConfig, 
        CartFactory $cart,
        StockStateInterfaceFactory $stockStateInterface
    ) {
        $this->stockStateInterface = $stockStateInterface;
        $this->productRepository = $productRepository;
        $this->dateTime = $dateTime;
        $this->scopeConfig = $scopeConfig;
        $this->cart = $cart;
    }
    
    /**
     * 
     * @param type $sku
     * @return boolean
     */
    public function getProduct($sku) 
    {
        try {
            $product = $this->productRepository->get($sku);
        } catch (\Magento\Framework\Exception\NoSuchEntityException $e) {
            $product = false;
        }
        return $product;
    }
    
    /**
     * 
     * @param string $sku
     * @return string
     */
    public function getEstimatedShipping($sku) 
    {
        $product = $this->getProduct($sku);
        if ($product && $this->isEnabled()) {
            if ($product->getCustomAttribute('production_item') && $product->getCustomAttribute('production_item')->getValue()) {
                if ($product->getCustomAttribute('lead_time')) {
                    $estimatedDays = $product->getCustomAttribute('lead_time')->getValue();
                    $deliveryDate = $this->getProductionItemEstimation($estimatedDays);
                    return $deliveryDate;
                } else {
                    $estimatedDays = $this->getConfigGeneral('default_lead_time_production');
                    $deliveryDate = $this->getProductionItemEstimation($estimatedDays);
                    return $deliveryDate;
                }
            } else if ($product->getCustomAttribute('non_productive_item_shipping')) {
                $stockState = $this->stockStateInterface->create();
                if (($stockState->getStockQty($product->getId()) <= 0) && ($product->getTypeId() == 'simple')) {
                    return "";
                }
                $deliveryDate = $product->getCustomAttribute('non_productive_item_shipping')->getValue();
                $deliveryDate = '<span>Ships within</span> ' . $deliveryDate;
                return $deliveryDate;
            } else {

                $stockState = $this->stockStateInterface->create();
                if (($stockState->getStockQty($product->getId()) <= 0) && ($product->getTypeId() == 'simple')) {
                    return "";
                }

                return '<span>Ships within</span> '.$this->getConfigGeneral('default_lead_time_nonproduction');

            }
        }
    }

    /**
     *
     * @param string $sku
     * @return string
     */
    public function getItemType($sku) 
    {
        $product = $this->getProduct($sku);
        if ($product && $this->isEnabled()) {
            if ($product->getCustomAttribute('production_item') && $product->getCustomAttribute('production_item')->getValue()) {
                return $product->getCustomAttribute('production_item')->getValue();
            }
        }
    }
    
    /**
     * 
     * @param string $sku
     */
    public function getQuoteEstimatedShipping($sku)
    {
        $product = $this->getProduct($sku);
        if ($product && $this->isEnabled()) {
            if ($product->getCustomAttribute('production_item') && $product->getCustomAttribute('production_item')->getValue()) {
                if ($product->getCustomAttribute('lead_time') && $estimatedDays = $product->getCustomAttribute('lead_time')->getValue()) {
                    return $estimatedDays;
                } else {
                    return $this->getConfigGeneral('default_lead_time_production');
                }
            } else if ($product->getCustomAttribute('non_productive_item_shipping') && $deliveryDate = $product->getCustomAttribute('non_productive_item_shipping')->getValue()) {
                return $deliveryDate;
            } else {
                return $this->getConfigGeneral('default_lead_time_nonproduction');
            }
        } else {
            return $this->getConfigGeneral('default_lead_time_production');
        }
    }

    /**
     * 
     * @param type $sku
     * @return boolean
     */
    public function getItemProductionStatus($sku)
    {
        $product = $this->getProduct($sku);

        $status = false;

        if ($product) {
            if ($productionItem = $product->getCustomAttribute('production_item')) {
                if ($productionItem->getValue()) {
                    $status = true;
                }
            }
        }

        return $status;
    }

    /**
     * 
     * @param type $storeId
     * @return bool
     */
    public function isEnabled($storeId = null)
    {
        return $this->getConfigGeneral('enabled', $storeId);
    }

    /**
     * 
     * @param type $code
     * @param type $storeId
     * @return string|null
     */
    public function getConfigGeneral($code = '', $storeId = null)
    {
        $code = ($code !== '') ? '/' . $code : '';

        return $this->getConfigValue(static::CONFIG_MODULE_PATH . '/general' . $code, $storeId);
    }

    /**
     * 
     * @param type $fullPath
     * @param type $storeId
     * @return string|null
     */
    public function getConfigValue($fullPath, $storeId) 
    {
        return $this->scopeConfig->getValue($fullPath, \Magento\Store\Model\ScopeInterface::SCOPE_STORE);
    }

    /**
     * 
     * @return boolean
     */
    public function getCartItemStatus():bool      
    {
        $productionItem = $inStockItem = false;
        $items = $this->cart->create()->getQuote()->getAllVisibleItems();
        foreach ($items as $item) {
            $itemStatus = $this->getItemProductionStatus($item->getSku());
            if ($itemStatus) {
                $productionItem = true;
            } else {
                $inStockItem = true;
            }
        }

        if ($productionItem && $inStockItem) {
            return true;
        }

        return false;
    }
    
    /**
     * 
     * @param string $estimateDays
     * @return string
     */
    public function getProductionItemEstimation($estimateDays):string
    {
        $estimatedDays = '+' . $estimateDays . ' weekdays';
        $timeStamp = $this->dateTime->timestamp($estimatedDays);
        $deliveryDate = $this->dateTime->gmtDate('m/d/Y', $timeStamp);
        $deliveryDate = '<span>Ships by</span> ' . $deliveryDate;
        return $deliveryDate;
    }

}
