<?php

namespace Ziffity\Deliverydate\Controller\Delivery;

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

        if(isset($data['date'])){

            $deliveryDate = date('Y-m-d',strtotime($this->getRequest()->getParam('date')));

            $today = new \DateTime();
            $today->setTimeZone(new \DateTimeZone($data['timeZone']));

            if(strtotime($deliveryDate) == strtotime($today->format('Y-m-d'))){
                $currentTime = $today->format('Y-m-d H:i:s');
                $currentTime = date('H:i', strtotime($currentTime.' + '.$this->configProvider->getTimeOffset().' hours'));
            }

            $timeIntervals = $this->timeIntervalCollection->toOptionArray();
            $deliveryCollection = $this->deliverydateCollection->create();
            $deliveryList = $deliveryCollection->getDeliveryByDate($deliveryDate);
            $deliverySlots = [];
            foreach ($deliveryList as $delivery) {
                $deliverySlots[] = $delivery->getTime();
            }
            $deliverySlotsCount = array_count_values($deliverySlots);
            $quota = $this->configProvider->getTimeQuota($this->getRequest()->getParam('date'));

            $availableTimeSlots = [];
            foreach ($timeIntervals as $timeSlot) {
                $timeSlot['disabled'] = false;

                if (isset($currentTime) && strtotime($currentTime) > strtotime(substr($timeSlot['label'], 0, 5))){
                    $timeSlot['disabled'] = true;
                }

                if( isset($deliverySlotsCount[$timeSlot['label']]) && $deliverySlotsCount[$timeSlot['label']] >= $quota ){
                    $timeSlot['disabled'] = true;
                }
                
                $availableTimeSlots[] = $timeSlot;
            }

        } else {
            $availableTimeSlots = array(
                ['value' => false, 'label' => 'Please Choose Another Date', 'disabled' => true]
            );
        }

        //To sort time slots
        usort($availableTimeSlots, array($this, "sortTimeSlot"));
        $resultJson = $this->resultJsonFactory->create();
        $resultJson->setData($availableTimeSlots);

        return $resultJson;
    }

    public function sortTimeSlot($data1, $data2)
    {
        return $data1['value'] > $data2['value'];
    }
}