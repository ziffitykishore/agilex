<?php

namespace SomethingDigital\ShipperHqCustomizations\Plugin;

use Magento\Customer\Model\Session;
use ShipperHQ\WS\Rate\Request\CustomerDetailsFactory;

class CustomerGroup
{
    protected $session;

    public function __construct(
        Session $session,
        CustomerDetailsFactory $customerDetailsFactory
    ) {
        $this->session = $session;
        $this->customerDetailsFactory = $customerDetailsFactory;
    }

    public function afterGetCustomerGroupDetails(\ShipperHQ\Shipper\Model\Carrier\Processor\ShipperMapper $subject, $result, $request)
    {
        $shippingRateGroup = $this->getShippingRateGroup();
        if ($shippingRateGroup) {
            $custGroupDetails = $this->customerDetailsFactory->create(
                ['customerGroup' => $shippingRateGroup]
            );
            return $custGroupDetails;
        } else {
            return $result;
        }
    }

    /**
     * @return string
     */
    protected function getShippingRateGroup()
    {
        if (($shippingRateGroup = $this->session->getCustomerDataObject()->getCustomAttribute('shipping_rate_group'))) {
            return $shippingRateGroup->getValue();
        } else {
            return false;
        }
    }
}
