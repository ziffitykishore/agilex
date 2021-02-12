<?php

namespace SomethingDigital\Cart\Observer;

use Magento\Framework\Event\ObserverInterface;
use Magento\Framework\Event\Observer;
use Magento\Framework\Session\SessionManagerInterface;

class AddSuffixDataToQuoteItem implements ObserverInterface
{
    /**
     * Execute observer method.
     *
     * @param Observer $observer Observer
     *
     * @return void
     */
    protected $session;

    public function __construct( 
        SessionManagerInterface $session
    ) {
        $this->session = $session;
    }

    public function execute(Observer $observer)
    {
        $item = $observer->getEvent()->getData('quote_item');
        $item = ($item->getParentItem() ? $item->getParentItem() : $item);

        if (!empty($this->session->getSkuSuffix())) {
            $suffix = json_decode($this->session->getSkuSuffix());
            if (!empty($suffix)) {
                foreach ($suffix as $key => $code) {
                    if(!empty($code)) {
                        $skuValue = explode('~', $code);
                        if($item->getProduct()->getSku() == $skuValue[1]) {
                            $item->setData(
                                'suffix',
                                $skuValue[0]
                            );
                        }
                    }
                }
            }
        }
    }
}
