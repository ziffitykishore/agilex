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
    
    protected $sourceList = [];
    

    public function __construct(
        \Magento\Eav\Model\ResourceModel\Entity\Attribute\Option\CollectionFactory $attrOptionCollectionFactory,
        \Magento\Eav\Model\ResourceModel\Entity\Attribute\OptionFactory $attrOptionFactory,
        \Magento\Directory\Model\ResourceModel\Country\CollectionFactory $countriesFactory,
        \Magento\Framework\Session\SessionManagerInterface $coreSession,
        \Magento\Inventory\Model\ResourceModel\Source\Collection $sourceCollection,
        \Magento\Directory\Model\ResourceModel\Region\Collection $region
    ) {
        $this->_countriesFactory = $countriesFactory;
        $this->coreSession = $coreSession;
        $this->sourceCollection  = $sourceCollection;
        $this->regionModel = $region;
        parent::__construct($attrOptionCollectionFactory, $attrOptionFactory);
    }


    public function getAllOptions($withEmpty = true, $defaultValues = false)
    {

        $selectedLocation = json_decode($_COOKIE["storeLocation"],true);
        $source = clone $this->sourceCollection;
        
        $state = $this->sourceCollection->addFieldToFilter('enabled',1)->addFieldToFilter('source_code',$selectedLocation["code"])->load()->getFirstItem();
        $regionId = $state->getRegionId();

        $sourceListArr = $source->addFieldToFilter('enabled', 1)->addFieldToFilter('region_id',$regionId)->load();

        foreach ($sourceListArr as $sourceItemName) {
            if($sourceItemName->getRegionId()){
                array_push(
                    $this->sourceList,
                    ["value" => $sourceItemName->getPostcode(), "label" => $sourceItemName->getPostcode()]
                );
            }
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

