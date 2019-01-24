<?php

namespace Ziffity\Promo\Controller\Order;

class Reorder extends \Magento\Sales\Controller\Order\Reorder
{
    
    public function execute() {
        $result = $this->orderLoader->load($this->_request);
        if ($result instanceof \Magento\Framework\Controller\ResultInterface) {
            return $result;
        }
        $order = $this->_coreRegistry->registry('current_order');
        /** @var \Magento\Framework\Controller\Result\Redirect $resultRedirect */
        $resultRedirect = $this->resultRedirectFactory->create();

        /* @var $cart \Magento\Checkout\Model\Cart */
        $cart = $this->_objectManager->get(\Magento\Checkout\Model\Cart::class);
        $items = $order->getItemsCollection();
        foreach ($items as $item) {
            try {
                if ($item->getPrice() == 0) { // if free product not added in cart
                    continue;
                }
                $cart->addOrderItem($item);
            } catch (\Magento\Framework\Exception\LocalizedException $e) {
                if ($this->_objectManager->get(\Magento\Checkout\Model\Session::class)->getUseNotice(true)) {
                    $this->messageManager->addNotice($e->getMessage());
                } else {
                    $this->messageManager->addError($e->getMessage());
                }
                return $resultRedirect->setPath('*/*/history');
            } catch (\Exception $e) {
                $this->messageManager->addException($e, __('We can\'t add this item to your shopping cart right now.'));
                return $resultRedirect->setPath('checkout/cart');
            }
        }

        $cart->save();
        return $resultRedirect->setPath('checkout/cart');
    }
}
