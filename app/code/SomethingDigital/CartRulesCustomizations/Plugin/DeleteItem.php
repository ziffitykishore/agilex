<?php

namespace SomethingDigital\CartRulesCustomizations\Plugin;

use Magento\Checkout\Controller\Cart\Delete;
use Magento\Framework\Session\SessionManagerInterface;
use Magento\Checkout\Model\Session;

class DeleteItem
{
    protected $session;
    
    public function __construct(
        SessionManagerInterface $session,
        Session $checkoutSession
    ) {
        $this->session = $session;
        $this->checkoutSession = $checkoutSession;
    }

    public function beforeExecute(Delete $subject)
    {
        $id = (int)$subject->getRequest()->getParam('id');

        $removedGifts = [];
        $quoteItem = $this->getItemById($id);
        $removedGifts = $this->session->getRemovedGifts();
        $removedGifts[] = $quoteItem->getSku();
        $this->session->setRemovedGifts($removedGifts);
    }

    public function getItemById($itemId)
    {
        foreach ($this->checkoutSession->getQuote()->getAllVisibleItems() as $item) {
            if ($item->getId() == $itemId) {
                return $item;
            }
        }

        return false;
    }
}