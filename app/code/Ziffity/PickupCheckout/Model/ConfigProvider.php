<?php

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace Ziffity\PickupCheckout\Model;

use Magento\Checkout\Model\ConfigProviderInterface;
use Magento\Framework\View\LayoutInterface;

class ConfigProvider implements ConfigProviderInterface
{
    /** @var LayoutInterface  */
    protected $_layout;
    
    protected $sourceCollection;
    
    protected $regionModel;

    public function __construct(
        LayoutInterface $layout,
        \Magento\Inventory\Model\ResourceModel\Source\Collection $source,
        \Magento\Directory\Model\Region $region
    ) {
        $this->_layout = $layout;
        $this->sourceCollection = $source;
        $this->regionModel = $region;
    }

    public function getConfig()
    {
        $currentStore = isset($_COOKIE['storeLocation']) ? json_decode($_COOKIE['storeLocation'],true) : null;
        if(isset($currentStore)) {
            $selectedLocation = $this->sourceCollection->addFieldToFilter('enabled',1)->addFieldToFilter('source_code',$currentStore["code"])->load()->getFirstItem();
            $region = $this->regionModel->load($selectedLocation->getRegionId());
        }

        return [
          'pickup_store' => [
              'street' => $selectedLocation->getStreet(),
              'city' => $selectedLocation->getCity(),
              'region_name' => $region->getName(),
              'country_id' => $selectedLocation->getCountryId(),
              'postcode' => $selectedLocation->getPostcode()
          ]
        ];
    }
}