<?php

namespace Ziffity\LocationSelector\Controller\Index;

class Index extends \Magento\Framework\App\Action\Action
{
    
    protected $resultJsonFactory;
    
    protected $sourceCollection;
    
    protected $regionModel;
    
    protected $sourceList = [];

    public function __construct(
        \Magento\Framework\App\Action\Context $context,
        \Magento\Framework\Controller\Result\JsonFactory $jsonFactory,
        \Magento\Inventory\Model\ResourceModel\Source\Collection $sourceCollection,
        \Magento\Directory\Model\Region $region
    ) {
        parent::__construct($context);
        $this->resultJsonFactory = $jsonFactory;
        $this->sourceCollection  = $sourceCollection;
        $this->regionModel = $region;
    }
    
    
    public function execute()
    {
        
        $sourceListArr = $this->sourceCollection->addFilter('enabled', 1)->load();

        foreach ($sourceListArr as $sourceItemName) {
            if($sourceItemName->getRegionId()){
                array_push(
                    $this->sourceList,
                    [
                        "code" => $sourceItemName->getSourceCode(),
                        "name" => $sourceItemName->getName()
                    ]
                );
            }
        }
        $pickupStores = array_filter($this->sourceList, function($item){
            return (strpos($item["code"], '_ps') !== false );
        });
        $pickupStores = array_values($pickupStores);

        $resultJson = $this->resultJsonFactory->create();
        $resultJson->setData($pickupStores);

        return $resultJson;
    }    
}