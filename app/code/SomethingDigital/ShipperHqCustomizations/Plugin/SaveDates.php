<?php

namespace SomethingDigital\ShipperHqCustomizations\Plugin;

use Magento\Framework\Session\SessionManagerInterface;

class SaveDates
{
    protected $session;

    public function __construct(
        SessionManagerInterface $coreSession
    ) {
        $this->session = $coreSession;
    }

    public function afterSendAndReceive(\ShipperHQ\WS\Client\WebServiceClient $subject, $result, $requestObj, $webServiceURL, $timeout)
    {
        $items = [];
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

        return $result;
    }
}
