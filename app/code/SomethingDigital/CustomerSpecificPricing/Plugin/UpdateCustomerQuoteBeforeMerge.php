<?php

namespace SomethingDigital\CustomerSpecificPricing\Plugin;

use Magento\Customer\Model\Session;
use Psr\Log\LoggerInterface;
use SomethingDigital\CustomerSpecificPricing\Model\Quote;
use Magento\Quote\Api\CartRepositoryInterface;

class UpdateCustomerQuoteBeforeMerge
{
    private $quoteRepository;
    private $quote;
    private $logger;

    public function __construct(
        CartRepositoryInterface $quoteRepository,
        Quote $quote,
        LoggerInterface $logger
    ) {
        $this->quoteRepository = $quoteRepository;
        $this->quote = $quote;
        $this->logger = $logger;
    }

    public function beforeSetCustomerDataAsLoggedIn(Session $subject, $customer)
    {
        try {
            $customerQuote = $this->quoteRepository->getForCustomer($customer->getId());
            $this->quote->repriceCustomerQuote(false, $customerQuote->getSuffix());
        } catch (\Exception $e) {
            $this->logger->critical($e);
        }

        return [$customer];
    }
}
