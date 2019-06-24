<?php

namespace Ziffity\Deliverydate\Controller\Sidebar;

class TimeInterval extends \Magento\Framework\App\Action\Action
{

    protected $resultJsonFactory;

    protected $timeIntervalCollection;

    protected $deliverydateCollection;

    protected $configProvider;

    protected $dateProvider;

    public function __construct(
        \Magento\Framework\App\Action\Context $context,
        \Magento\Framework\Controller\Result\JsonFactory $jsonFactory,
        \Amasty\Deliverydate\Model\ResourceModel\Tinterval\Collection $timeIntervalCollection,
        \Amasty\Deliverydate\Model\ResourceModel\Deliverydate\CollectionFactory $deliverydateCollection,
        \Ziffity\Deliverydate\Model\DeliverydateConfigProvider $config,
        \Magento\Framework\Stdlib\DateTime\DateTime $date
    ) {
        parent::__construct($context);
        $this->resultJsonFactory = $jsonFactory;
        $this->timeIntervalCollection = $timeIntervalCollection;
        $this->deliverydateCollection = $deliverydateCollection;
        $this->configProvider = $config;
        $this->dateProvider = $date;
    }

    public function execute()
    {

        $data = $this->getRequest()->getParams();

        if(isset($data['timeIntervalId'])){

            $timeIntervals = $this->timeIntervalCollection->toOptionArray();

            $availableTimeSlots = [];
            foreach ($timeIntervals as $timeSlot) 
            {
                if((int)$timeSlot['value'] === (int)$data['timeIntervalId'])
                {
                    $availableTimeSlots[] = $timeSlot;
                }                
            }

        } else {
            $availableTimeSlots = array(
                ['timeInterval' => 'Invalid Time Interval']
            );
        }

        $resultJson = $this->resultJsonFactory->create();
        $resultJson->setData($availableTimeSlots);

        return $resultJson;
    }    
}
