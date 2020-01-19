<?php

namespace SomethingDigital\CustomerSpecificPricing\Plugin;

use Magento\Customer\Controller\Account\LoginPost;
use Magento\Framework\Exception\NoSuchEntityException;
use SomethingDigital\CustomerSpecificPricing\Model\Quote;
use Magento\Quote\Api\CartRepositoryInterface;

class UpdateCustomerQuoteAfterMerge
{
    private $quote;

    public function __construct(
        Quote $quote
    ) {
        $this->quote = $quote;
    }

    public function afterExecute(LoginPost $subject, $result)
    {
        $this->quote->repriceCustomerQuote();
        return $result;
    }
}
