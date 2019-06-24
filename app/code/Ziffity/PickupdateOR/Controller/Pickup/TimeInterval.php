<?php

namespace Ziffity\PickupdateOR\Controller\Pickup;

class TimeInterval extends \Magento\Framework\App\Action\Action
{

    protected $resultJsonFactory;

    protected $timeIntervalCollection;

    protected $pickupdateCollection;

    protected $configProvider;

    protected $dateProvider;

    public function __construct(
        \Magento\Framework\App\Action\Context $context,
        \Magento\Framework\Controller\Result\JsonFactory $jsonFactory,
        \Ziffity\Pickupdate\Model\ResourceModel\Tinterval\Collection $timeIntervalCollection,
        \Ziffity\Pickupdate\Model\ResourceModel\Pickupdate\CollectionFactory $pickupdateCollection,
        \Ziffity\Pickupdate\Model\PickupdateConfigProvider $config,
        \Magento\Framework\Stdlib\DateTime\DateTime $date
    ) {
        parent::__construct($context);
        $this->resultJsonFactory = $jsonFactory;
        $this->timeIntervalCollection = $timeIntervalCollection;
        $this->pickupdateCollection = $pickupdateCollection;
        $this->configProvider = $config;
        $this->dateProvider = $date;
    }

    public function execute()
    {

        $data = $this->getRequest()->getParams();

        if(isset($data['date'])){

            $pickupDate = date('Y-m-d',strtotime($this->getRequest()->getParam('date')));

            $today = new \DateTime();
            $today->setTimeZone(new \DateTimeZone($data['timeZone']));

            if(strtotime($pickupDate) == strtotime($today->format('Y-m-d'))){
                $currentTime = $today->format('Y-m-d H:i:s');
                $currentTime = date('H:i', strtotime($currentTime.' + '.$this->configProvider->getTimeOffset().' hours'));
            }

            $timeIntervals = $this->timeIntervalCollection->toOptionArray();
            $pickupCollection = $this->pickupdateCollection->create();
            $pickupList = $pickupCollection->getPickupByDate($pickupDate);
            $pickupSlots = [];
            foreach ($pickupList as $pickup) {
                $pickupSlots[] = $pickup->getTime();
            }
            $pickupSlotsCount = array_count_values($pickupSlots);
            $quota = $this->configProvider->getTimeQuota($this->getRequest()->getParam('date'));

            $availableTimeSlots = [];
            foreach ($timeIntervals as $timeSlot) {
                $timeSlot['disabled'] = false;

                if (isset($currentTime) && strtotime($currentTime) > strtotime(substr($timeSlot['label'], 0, 5))){
                    $timeSlot['disabled'] = true;
                }

                if( isset($pickupSlotsCount[$timeSlot['label']]) && $pickupSlotsCount[$timeSlot['label']] >= $quota ){
                    $timeSlot['disabled'] = true;
                }
                
                $availableTimeSlots[] = $timeSlot;
            }

        } else {
            $availableTimeSlots = array(
                ['value' => 'Please Choose Another Date', 'label' => 'Please Choose Another Date', 'disabled' => true]
            );
        }

        $resultJson = $this->resultJsonFactory->create();
        $resultJson->setData($availableTimeSlots);

        return $resultJson;
    }    
}
