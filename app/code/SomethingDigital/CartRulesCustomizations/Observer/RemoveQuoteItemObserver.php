<?php

namespace SomethingDigital\CartRulesCustomizations\Observer;

use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;
use Magento\Framework\Session\SessionManagerInterface;

class RemoveQuoteItemObserver implements ObserverInterface
{
    protected $session;

    public function __construct(
        SessionManagerInterface $session
    ) {
        $this->session = $session;
    }

    /**
     * @param Observer $observer
     * @return void
     */
    public function execute(Observer $observer)
    {
        $removedGifts = [];
        $quoteItem = $observer->getEvent()->getQuoteItem();
        $removedGifts = $this->session->getRemovedGifts();
        $removedGifts[] = $quoteItem->getSku();
        $this->session->setRemovedGifts($removedGifts);
    }
}