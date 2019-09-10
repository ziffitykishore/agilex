<?php

namespace SomethingDigital\ShipperHqCustomizations\Plugin;

use Magento\Framework\Session\SessionManagerInterface;
use ShipperHQ\Shipper\Helper\CarrierCache;

class SaveDatesFromCache
{
    protected $session;

    public function __construct(
        SessionManagerInterface $coreSession
    ) {
        $this->session = $coreSession;
    }

    public function afterGetCachedQuotes(CarrierCache $subject, $result, $requestParams, $carrierCode)
    {
        $items = [];
        if (isset($result['result']->carrierGroups)) {
            foreach ($result['result']->carrierGroups as $carrierGroup) {
                foreach ($carrierGroup->carrierRates as $carrierRate) {
                    foreach ($carrierRate->shipments as $shipment) {
                        foreach ($shipment->boxedItems as $item) {
                            $items[$item->sku] = $carrierRate->deliveryDateMessage;
                        }
                    }
                }
            }
            $this->session->setItemsDeliveryDates($items);
        }

        $writer = new \Zend\Log\Writer\Stream(BP . '/var/log/test.log');
        $logger = new \Zend\Log\Logger();
        $logger->addWriter($writer);
        $logger->info(json_encode('test3'));

        return $result;
    }
}
