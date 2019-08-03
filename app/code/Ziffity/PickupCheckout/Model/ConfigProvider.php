<?php

namespace Ziffity\PickupCheckout\Model;

use Magento\Checkout\Model\ConfigProviderInterface;
use Magento\Framework\View\LayoutInterface;

class ConfigProvider implements ConfigProviderInterface
{
    /** @var LayoutInterface  */
    protected $_layout;
    
    protected $sourceCollection;
    
    protected $regionModel;

    protected $zipcodeCollection;
    
    private $_storeManager;
    
    protected $_helperCore;

    protected $_modelStock;
    
    protected $_modelPos;
    
    public function __construct(
        LayoutInterface $layout,
        \Magento\Directory\Model\Region $region,
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
        $storeName = $this->_storeManager->getStore()->getStoreId();
        $pos = $this->_modelPos->getPlacesByStoreId($storeId, true);
        $pickupStore = [];

        foreach ($pos as $place) {
            $pickupStore['street'] = $place->getAddressLine1();
            $pickupStore['city'] = $place->getCity();
            $pickupStore['region'] = $place->getState();
            $pickupStore['country'] = $place->getCountryCode();
            $pickupStore['postcode'] = $place->getCountryCode();
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
                  'street' => $pickupStore['street'],
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