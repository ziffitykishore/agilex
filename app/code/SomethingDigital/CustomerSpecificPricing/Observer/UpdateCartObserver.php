<?php

namespace SomethingDigital\CustomerSpecificPricing\Observer;

use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;
use SomethingDigital\CustomerSpecificPricing\Model\Quote;

class UpdateCartObserver implements ObserverInterface
{
    protected $quote;

    public function __construct(
        Quote $quote
    ) {
        $this->quote = $quote;
    }

    /**
     * @param Observer $observer
     * @return void
     */
    public function execute(Observer $observer)
    {
        $this->quote->repriceCustomerQuote();
    }
}
