<?php

namespace Earthlite\EstimatedShipping\Model\Shipping;

use Magento\Catalog\Api\ProductRepositoryInterface;
use Magento\Framework\Stdlib\DateTime\DateTime;

class Estimation
{
    const CONFIG_MODULE_PATH = 'estimate_shipping';

    protected $productRepository; 

    protected $dateTime;

    protected $scopeConfig;

    protected $cart;

    public function __construct(
        ProductRepositoryInterface $productRepository,
        DateTime $dateTime,
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig,
        \Magento\Checkout\Model\CartFactory $cart
    ) {
    
        $this->productRepository = $productRepository;    
        $this->dateTime = $dateTime;
        $this->scopeConfig = $scopeConfig;
        $this->cart = $cart;
    }

    public function getProduct($sku)
    {        
        try 
        {
            $product = $this->productRepository->get($sku);
        } catch (\Magento\Framework\Exception\NoSuchEntityException $e){
            $product = false;
        }

        return $product;
    }

    public function getEstimatedShipping($sku)
    {                
        $product = $this->getProduct($sku);

        if($product && $this->isEnabled()) 
        {
            if($product->getCustomAttribute('production_item') && $product->getCustomAttribute('production_item')->getValue())
            {                                
                if($product->getCustomAttribute('lead_time')) 
                {                                   
                    $estimatedDays = $product->getCustomAttribute('lead_time')->getValue();

                    $deliveryDate = $this->getProductionItemEstimation($estimatedDays);                    
                    
                    return $deliveryDate;
                }                
                else
                {
                    $estimatedDays = $this->getConfigGeneral('default_lead_time_production');

                    $deliveryDate = $this->getProductionItemEstimation($estimatedDays);                    
                    
                    return $deliveryDate;                    
                }
            }
            else if($product->getCustomAttribute('non_productive_item_shipping')) 
            {
                $deliveryDate = $product->getCustomAttribute('non_productive_item_shipping')->getValue();
                $deliveryDate = 'Ships Within '.$deliveryDate;
                return $deliveryDate;
            }
            else
            {
                return $this->getConfigGeneral('default_lead_time_nonproduction');
            }
        }        
    }

    public function getQuoteEstimatedShipping($sku)
    {                
        $product = $this->getProduct($sku);

        if($product && $this->isEnabled()) 
        {
            if($product->getCustomAttribute('production_item') && $product->getCustomAttribute('production_item')->getValue())
            {                
                if($product->getCustomAttribute('lead_time') && $estimatedDays = $product->getCustomAttribute('lead_time')->getValue()) 
                {                                        
                    return $estimatedDays;
                }
                else
                { 
                    return $this->getConfigGeneral('default_lead_time_production');
                }
            }
            else if($product->getCustomAttribute('non_productive_item_shipping') && $deliveryDate = $product->getCustomAttribute('non_productive_item_shipping')->getValue()) 
            {                
                return $deliveryDate;
            }
            else
            {
                return $this->getConfigGeneral('default_lead_time_nonproduction');
            }
        }
        else
        {
            return $this->getConfigGeneral('default_lead_time_production');
        }
    }

    public function getItemProductionStatus($sku)
    {        
        $product = $this->getProduct($sku);

        $status = false;

        if($product) 
        {
            if($productionItem = $product->getCustomAttribute('production_item'))
            {
                if($productionItem->getValue())
                {
                    $status = true;
                }
                
            }
        }

        return $status;
    }

    public function isEnabled($storeId = null)
    {        
        return $this->getConfigGeneral('enabled', $storeId);
    }

    public function getConfigGeneral($code = '', $storeId = null)
    {
        $code = ($code !== '') ? '/' . $code : '';

        return $this->getConfigValue(static::CONFIG_MODULE_PATH . '/general' . $code, $storeId);
    }

    public function getConfigValue($fullPath, $storeId)
    {        
        return $this->scopeConfig->getValue($fullPath, \Magento\Store\Model\ScopeInterface::SCOPE_STORE);
    }

    public function getCartItemStatus()
    {
        $productionItem = $inStockItem = false;
        $items = $this->cart->create()->getQuote()->getAllVisibleItems();
        foreach ($items as $item) {
            $itemStatus = $this->getItemProductionStatus($item->getSku());
            if($itemStatus)
            {
                $productionItem = true;
            }
            else
            {
                $inStockItem = true;
            }
        }
        
        if ($productionItem && $inStockItem) 
        {
            return true;
        }

        return false;
    }

    public function getProductionItemEstimation($estimateDays)
    {        
        $estimatedDays = '+'.$estimateDays.' weekdays';
        $timeStamp = $this->dateTime->timestamp($estimatedDays);
        $deliveryDate = $this->dateTime->gmtDate('d/m/Y', $timeStamp);
        $deliveryDate = 'Ships by '. $deliveryDate;
        return $deliveryDate;
    }
}
