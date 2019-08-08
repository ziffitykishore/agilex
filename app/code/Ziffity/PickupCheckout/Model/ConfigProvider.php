<?php

namespace Ziffity\PickupCheckout\Model;

use Magento\Checkout\Model\ConfigProviderInterface;
use Magento\Framework\View\LayoutInterface;

class ConfigProvider implements ConfigProviderInterface
{
    protected $_layout;
    
    protected $sourceCollection;
    
    protected $regionModel;

    protected $zipcodeCollection;
    
    protected $_storeManager;
    
    protected $_helperCore;

    protected $_modelStock;
    
    protected $_modelPos;
    
    public function __construct(
        LayoutInterface $layout,
        \Magento\Directory\Model\ResourceModel\Region\Collection $region,
        \Ziffity\Zipcode\Model\ResourceModel\Data\Collection $zipcodeCollection,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Wyomind\Core\Helper\Data $helperCore,
        \Wyomind\AdvancedInventory\Model\Stock $modelStock,
        \Wyomind\PointOfSale\Model\PointOfSale $modelPos
    ) {
        $this->_layout = $layout;
        $this->regionModel = $region;
        $this->zipcodeCollection = $zipcodeCollection;
        $this->_storeManager = $storeManager;
        $this->_helperCore = $helperCore;
        $this->_modelStock = $modelStock;
        $this->_modelPos = $modelPos;
    }

    public function getConfig()
    {        
        $storeId = $this->_storeManager->getStore()->getStoreId();
        $storeName = $this->_storeManager->getStore()->getName();
        $pos = $this->_modelPos->getPlacesByStoreId($storeId, true);

        $pickupStore = [];
        foreach ($pos as $place) {
            $regionData =  $this->regionModel->addFieldToFilter('code', ['eq' => $place->getState()])->getFirstItem();
            $pickupStore['street_line_1'] = $place->getAddressLine1();
            $pickupStore['street_line_2'] = $place->getAddressLine2();
            $pickupStore['city'] = $place->getCity();
            $pickupStore['region'] = $regionData->getDefaultName();
            $pickupStore['country'] = $place->getCountryCode();
            $pickupStore['postcode'] = $place->getPostalCode();
            $pickupStore['phone'] = $place->getMainPhone();
        }

        if(isset($storeName)) {
            $allowedZipcode = $this->zipcodeCollection->addFieldToFilter('is_active', 1)->addFieldToFilter('source_code', $storeName)->load()->getFirstItem();
        }

        if(isset($allowedZipcode) && $allowedZipcode->getAllowedZipcodeList()) {
           $zipcodeList = explode(",", $allowedZipcode->getAllowedZipcodeList());
        }

        if(isset($_COOKIE['is_pickup']) && $_COOKIE['is_pickup'] == 'true') {
            return [
              'pickup_store' => [
                  'street_line_1' => $pickupStore['street_line_1'],
                  'street_line_2' => $pickupStore['street_line_2'],
                  'city' => $pickupStore['city'],
                  'region_name' => $pickupStore['region'],
                  'country_id' => $pickupStore['country'],
                  'postcode' => $pickupStore['postcode'],
                  'phone' => $pickupStore['phone']
              ]
            ];
        } else {
            return [
                'allowed_zipcode' => isset($zipcodeList) ? $zipcodeList : []
            ];
        }
    }
}