<?php

namespace SomethingDigital\ShipperHqCustomizations\Plugin;

use Magento\Framework\Session\SessionManagerInterface;
use ShipperHQ\WS\Client\WebServiceClient;

class SaveDates
{
    protected $session;

    public function __construct(
        SessionManagerInterface $coreSession
    ) {
        $this->session = $coreSession;
    }

    public function afterSendAndReceive(WebServiceClient $subject, $result, $requestObj, $webServiceURL, $timeout)
    {
        $items = [];
        if (isset($result['result']->carrierGroups)) {
            foreach ($result['result']->carrierGroups as $carrierGroup) {
                foreach ($carrierGroup->carrierRates as $carrierRate) {
                    if (isset($carrierRate->shipments)) {
                        $skus = [];
                        foreach ($carrierRate->shipments as $shipment) {
                            foreach ($shipment->boxedItems as $item) {
                                $skus[] = $item->sku;
                            }
                        }
                        foreach ($carrierRate->rates as $key => $rate) {
                            foreach ($skus as $sku) {
                                $items[$sku][$rate->code] = $rate->deliveryMessage;
                            }
                        }
                    }
                }
            }
            $this->session->setItemsDeliveryDates($items);
        }

        return $result;
    }
}
