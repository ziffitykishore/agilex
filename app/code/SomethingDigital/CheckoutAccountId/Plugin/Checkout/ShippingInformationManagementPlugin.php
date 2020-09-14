<?php

namespace SomethingDigital\CheckoutAccountId\Plugin\Checkout;

use Magento\Quote\Model\QuoteRepository;
use Magento\Checkout\Model\ShippingInformationManagement;
use Magento\Checkout\Api\Data\ShippingInformationInterface;

class ShippingInformationManagementPlugin
{

    protected $quoteRepository;

    public function __construct(
        QuoteRepository $quoteRepository
    ) {
        $this->quoteRepository = $quoteRepository;
    }

    /**
     * @param \Magento\Checkout\Model\ShippingInformationManagement $subject
     * @param $cartId
     * @param \Magento\Checkout\Api\Data\ShippingInformationInterface $addressInformation
     */
    public function beforeSaveAddressInformation(
        ShippingInformationManagement $subject,
        $cartId,
        ShippingInformationInterface $addressInformation
    ) {
        $extAttributes = $addressInformation->getShippingAddress()->getExtensionAttributes();
        $traversAccountId = $extAttributes->getTraversAccountId();
        $quote = $this->quoteRepository->getActive($cartId);
        $quote->setTraversAccountId($traversAccountId);
    }
}
