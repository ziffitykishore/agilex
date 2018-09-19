<?php
namespace Ziffity\Promo\Observer;

class HandleValidation implements \Magento\Framework\Event\ObserverInterface
{

    protected $coreHelper;
    protected $_isHandled = array();

    public function __construct(
        \Ziffity\Promo\Helper\Data $coreHelper
    ) {
        $this->coreHelper = $coreHelper;
    }
  
    public function execute(\Magento\Framework\Event\Observer $observer)
    {
        try {
            $rule = $observer->getEvent()->getRule();

            if (!in_array($rule->getSimpleAction(), array('ampromo_items', 'ampromo_cart'))) {
                return $this;
            }

            if (isset($this->_isHandled[$rule->getId()])) {
                return $this;
            }

            $this->_isHandled[$rule->getId()] = true;

            $promoSku = $rule->getPromoSku();
            if (!$promoSku) {
                return $this;
            }
            $quote = $observer->getEvent()->getQuote();

            $qty = $this->coreHelper->_getFreeItemsQty($rule, $quote);
            if (!$qty) {
                //@todo  - add new field for label table
                // and show message like "Add 2 more products to get free items"
                return $this;
            }

            $session = $this->coreHelper->getCheckoutSession();
            if ($session->getAmpromoId() != $quote->getId()) {
                $session->setAmpromoDeletedItems(null);
                $session->setAmpromoMessages(null);
                $session->setAmpromoId($quote->getId());
            }

            $promoSku = explode(',', $promoSku);
            foreach ($promoSku as $sku) {
                $sku = trim($sku);
                if (!$sku) {
                    continue;
                }
                $product = $this->coreHelper->_loadProduct($sku, $qty);
                if (!$product) {
                    continue;
                }
                if ($this->coreHelper->_addProductToQuote($quote, $product, $qty)) {
    //              $message = $rule->getStoreLabel(1);
    //              if ($message){
    //                  $Helper->_showMessage($message, false);	
    //              }
                }
            }
        } catch (Exception $ex) {
            $this->coreHelper->logger('promoError', $ex->getMessage(), true);
        }
        return $this;
    }
}
