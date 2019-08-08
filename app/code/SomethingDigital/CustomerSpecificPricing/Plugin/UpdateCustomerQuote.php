<?php

namespace SomethingDigital\CustomerSpecificPricing\Plugin;

use Magento\Customer\Model\Session;
use SomethingDigital\CustomerSpecificPricing\Model\Quote;

class UpdateCustomerQuote
{
    /**
    * @var CustomerSession
    */
    private $customerSession;

    /**
     * @var Quote
     */
    private $quote;

    public function __construct(
        Session $customerSession,
        Quote $quote
    ) {
        $this->customerSession = $customerSession;
        $this->quote = $quote;
    }

    public function afterLoadCustomerQuote(\Magento\Checkout\Model\Session $subject, $result)
    {
        $this->quote->repriceCustomerQuote();

        return $result;
    }
}
