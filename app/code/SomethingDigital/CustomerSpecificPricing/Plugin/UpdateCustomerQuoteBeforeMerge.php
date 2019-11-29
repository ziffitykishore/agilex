<?php

namespace SomethingDigital\CustomerSpecificPricing\Plugin;

use Magento\Customer\Model\Session;
use SomethingDigital\CustomerSpecificPricing\Model\Quote;
use Magento\Quote\Api\CartRepositoryInterface;

class UpdateCustomerQuoteBeforeMerge
{
    private $quoteRepository;
    private $quote;

    public function __construct(
        CartRepositoryInterface $quoteRepository,
        Quote $quote
    ) {
        $this->quoteRepository = $quoteRepository;
        $this->quote = $quote;
    }

    public function beforeSetCustomerDataAsLoggedIn(Session $subject, $customer)
    {
        $customerQuote = $this->quoteRepository->getForCustomer($customer->getId());

        $this->quote->repriceCustomerQuote(false, $customerQuote->getSuffix());

        return [$customer];
    }
}
