<?php

namespace Ziffity\Checkout\Model\ResourceModel\Address\Attribute\Source;

use Magento\Framework\App\ObjectManager;
use Magento\Store\Model\StoreManagerInterface;

class Postcode extends \Magento\Eav\Model\Entity\Attribute\Source\Table
{

    protected $_countriesFactory;

    protected $storeManager;

    protected $coreSession;
    
    protected $sourceCollection;
    
    protected $regionModel;
    
    protected $zipcodeCollection;


    protected $sourceList = [];
    

    public function __construct(
        \Magento\Eav\Model\ResourceModel\Entity\Attribute\Option\CollectionFactory $attrOptionCollectionFactory,
        \Magento\Eav\Model\ResourceModel\Entity\Attribute\OptionFactory $attrOptionFactory,
        \Magento\Directory\Model\ResourceModel\Country\CollectionFactory $countriesFactory,
        \Magento\Framework\Session\SessionManagerInterface $coreSession,
        \Magento\Inventory\Model\ResourceModel\Source\Collection $sourceCollection,
        \Magento\Directory\Model\ResourceModel\Region\Collection $region,
        \Ziffity\Zipcode\Model\ResourceModel\Data\Collection $zipcodeCollection
    ) {
        $this->_countriesFactory = $countriesFactory;
        $this->coreSession = $coreSession;
        $this->sourceCollection  = $sourceCollection;
        $this->regionModel = $region;
        $this->zipcodeCollection = $zipcodeCollection;
        parent::__construct($attrOptionCollectionFactory, $attrOptionFactory);
    }


    public function getAllOptions($withEmpty = true, $defaultValues = false)
    {

        $selectedLocation = isset($_COOKIE['storeLocation']) ? json_decode($_COOKIE["storeLocation"],true) : null;

        if(isset($selectedLocation['code'])) {
            $allowedZipcode = $this->zipcodeCollection->addFieldToFilter('is_active', 1)->addFieldToFilter('source_code', $selectedLocation["code"])->load()->getFirstItem();
        }
        
        if(isset($allowedZipcode) && $allowedZipcode->getAllowedZipcodeList()) {
           $zipcodeList = explode(",", $allowedZipcode->getAllowedZipcodeList());
        }

        if(!isset($zipcodeList)){
            $zipcodeList = [];
            $location = $this->sourceCollection->addFieldToFilter('enabled',1)->addFieldToFilter('source_code',$selectedLocation["code"])->load()->getFirstItem();
            array_push($zipcodeList, $location->getPostcode());
        }

        foreach ($zipcodeList as $item) {
            array_push(
                $this->sourceList,
                ["value" => $item, "label" => $item]
            );
        }

        $this->_options = array_values(array_unique($this->sourceList, SORT_REGULAR));
        
        return $this->_options;
    }

    private function getStoreManager()
    {
        if (!$this->storeManager) {
            $this->storeManager = ObjectManager::getInstance()->get(StoreManagerInterface::class);
        }

        return $this->storeManager;
    }
}

