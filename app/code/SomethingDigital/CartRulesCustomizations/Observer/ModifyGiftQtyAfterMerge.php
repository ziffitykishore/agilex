<?php

namespace SomethingDigital\CartRulesCustomizations\Observer;

use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;
use Magento\Quote\Api\CartRepositoryInterface;
use SomethingDigital\CustomerSpecificPricing\Model\Quote;

class ModifyGiftQtyAfterMerge implements ObserverInterface
{
    protected $quoteRepository;
    protected $quote;

    public function __construct(
        CartRepositoryInterface $quoteRepository,
        Quote $quote
    ) {
        $this->quoteRepository = $quoteRepository;
        $this->quote = $quote;
    }

    /**
     * Gift products are added to the cart after Cart Price Rule is applied and only 1 item of specific gift product can be in the cart.
     * Because during the cart merge, Magento adds quantities of items we have to change quantity of gift product to 1.
     * @param Observer $observer
     * @return void
     */
    public function execute(Observer $observer)
    {
        $quote = $observer->getEvent()->getQuote();

        foreach ($quote->getAllVisibleItems() as $item) {

            $options = $item->getOptions();
            if ($options) {
                foreach ($options as $option) {
                    if ($option->getCode() == 'free_gift' && $option->getValue() == 1) {
                       $item->setQty(1);
                       $item->save();
                    }
                }
            }
        }

        $this->quote->repriceCustomerQuote(null, $quote);

        if ($quote && $quote->getId()) {
            $quoteModel = $this->quoteRepository->get($quote->getId());
            $this->quoteRepository->save($quoteModel);
        }
    }
}