<?php

namespace SomethingDigital\CustomerSpecificPricing\Plugin;

use Magento\Customer\Model\Session;
use Magento\Framework\Exception\NoSuchEntityException;
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
        try {
            $customerQuote = $this->quoteRepository->getForCustomer($customer->getId());
            $this->quote->repriceCustomerQuote(false, $customerQuote->getSuffix());
        } catch (NoSuchEntityException $e) {
            //No need to log this error
        }

        return [$customer];
    }
}
