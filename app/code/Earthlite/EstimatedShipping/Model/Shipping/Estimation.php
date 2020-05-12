<?php

namespace Earthlite\EstimatedShipping\Model\Shipping;

use Magento\Catalog\Api\ProductRepositoryInterface;
use Magento\Framework\Stdlib\DateTime\DateTime;

class Estimation
{

    protected $productRepository; 

    protected $dateTime;

    public function __construct(
        ProductRepositoryInterface $productRepository,
        DateTime $dateTime
    ) {
    
        $this->productRepository = $productRepository;    
        $this->dateTime = $dateTime;
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

        if($product) {
            if($product->getCustomAttribute('requested_ship_date')) {
                $estimatedDays = $product->getCustomAttribute('requested_ship_date')->getValue();
                $estimatedDays = '+'.$estimatedDays.' weekdays';
                $timeStamp = $this->dateTime->timestamp($estimatedDays);
                $deliveryDate = $this->dateTime->gmtDate('d/m/Y', $timeStamp);                
                return $deliveryDate;
            }
            else
            {
                return false;
            }
        }
    }

}
