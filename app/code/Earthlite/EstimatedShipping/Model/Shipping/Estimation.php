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

    public function __construct(
        ProductRepositoryInterface $productRepository,
        DateTime $dateTime,
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig
    ) {
    
        $this->productRepository = $productRepository;    
        $this->dateTime = $dateTime;
        $this->scopeConfig = $scopeConfig;
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
            if($productionItem = $product->getCustomAttribute('production_item'))
            {                
                if($product->getCustomAttribute('requested_ship_date') && $productionItem->getValue()) 
                {
                    $estimatedDays = $product->getCustomAttribute('requested_ship_date')->getValue();
                    $estimatedDays = '+'.$estimatedDays.' weekdays';
                    $timeStamp = $this->dateTime->timestamp($estimatedDays);
                    $deliveryDate = $this->dateTime->gmtDate('d/m/Y', $timeStamp);
                    $deliveryDate = 'Ships by '. $deliveryDate;
                    return $deliveryDate;
                }
                else if($product->getCustomAttribute('non_productive_item_shipping')) 
                {
                    $deliveryDate = $product->getCustomAttribute('non_productive_item_shipping')->getValue();
                    $deliveryDate = 'Ships Within '.$deliveryDate;
                    return $deliveryDate;
                }
                else
                {
                    return false;
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
                return false;
            }
        }
        else
        {
            return false;
        }
    }

    public function getItemProductionStatus($sku)
    {
        $product = $this->getProduct($sku);

        if($product) 
        {
            if($productionItem = $product->getCustomAttribute('production_item'))
            {
                if($productionItem->getValue())
                {
                    return true;
                }
                else
                {
                    return false;
                }
            }
            else
            {
                return false;
            }
        }
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
}
