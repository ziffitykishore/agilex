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

    public function __construct(
        LayoutInterface $layout,
        \Magento\Inventory\Model\ResourceModel\Source\Collection $source,
        \Magento\Directory\Model\Region $region,
        \Ziffity\Zipcode\Model\ResourceModel\Data\Collection $zipcodeCollection
    ) {
        $this->_layout = $layout;
        $this->sourceCollection = $source;
        $this->regionModel = $region;
        $this->zipcodeCollection = $zipcodeCollection;
    }

    public function getConfig()
    {
        $currentStore = isset($_COOKIE['storeLocation']) ? json_decode($_COOKIE['storeLocation'],true) : null;
        if(isset($currentStore)) {
            $selectedLocation = $this->sourceCollection->addFieldToFilter('enabled',1)->addFieldToFilter('source_code',$currentStore["code"])->load()->getFirstItem();
            $region = $this->regionModel->load($selectedLocation->getRegionId());
        }

        if(isset($currentStore['code'])) {
            $allowedZipcode = $this->zipcodeCollection->addFieldToFilter('is_active', 1)->addFieldToFilter('source_code', $currentStore["code"])->load()->getFirstItem();
        }

        if($allowedZipcode->getAllowedZipcodeList()) {
           $zipcodeList = explode(",", $allowedZipcode->getAllowedZipcodeList());
        }

        if(isset($_COOKIE['is_pickup']) && $_COOKIE['is_pickup'] == 'true') {
            return [
              'pickup_store' => [
                  'street' => $selectedLocation->getStreet(),
                  'city' => $selectedLocation->getCity(),
                  'region_name' => $region->getName(),
                  'country_id' => $selectedLocation->getCountryId(),
                  'postcode' => $selectedLocation->getPostcode()
              ]
            ];
        } else {
            return [
                'allowed_zipcode' => $zipcodeList
            ];
        }
    }
}