<?php

namespace Ziffity\Promo\Observer;

class InitFreeItems implements \Magento\Framework\Event\ObserverInterface
{

    protected $coreHelper;
    protected $request;

    public function __construct(
        \Ziffity\Core\Helper\Data $coreHelper,
        \Magento\Framework\App\RequestInterface $request
    ) {
        $this->coreHelper = $coreHelper;
        $this->request = $request;
    }

    public function execute(\Magento\Framework\Event\Observer $observer)
    {
        try{
            /**
             * TODO: workaround fix need to check in future
             */
            if($this->request->getFullActionName() == '__'){
                return $this;
            }
            $this->_isHandled = array();
            $quote = $observer->getQuote();
            if (!$quote) {
                return $this;
            }
            foreach ($quote->getItemsCollection() as $item) {
                if (!$item) {
                    continue;
                }
                if (!$item->getOptionByCode('ampromo_rule')) {
                    continue;
                }
                $this->coreHelper->getRegister()->unregister('ampromo_del');
                $this->coreHelper->getRegister()->register('ampromo_del', $item->getId());
                $item->isDeleted(true);
                $item->setData('qty_to_add', '0.0000');
                $quote->removeItem($item->getId());
            }
        } catch (Exception $ex) {
            $this->coreHelper->logger('promoError', $ex->getMessage(), true);
        }
        return $this;
    }

}
