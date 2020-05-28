<?php
/**
 *
 * Copyright © 2018 Vantiv, LLC. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Vantiv\Payment\Controller\Paypal\Express;

use Magento\Framework\Controller\ResultFactory;

class ReturnAction extends AbstractExpress
{
    /**
     * Return from PayPal and dispatch customer to order review page
     *
     * @return void|\Magento\Framework\Controller\Result\Redirect
     */
    public function execute()
    {
        /** @var \Magento\Framework\Controller\Result\Redirect $resultRedirect */
        $resultRedirect = $this->resultFactory->create(ResultFactory::TYPE_REDIRECT);

        if ($this->getRequest()->getParam('retry_authorization') == 'true'
            && is_array($this->_getCheckoutSession()->getPaypalTransactionData())
        ) {
            $this->_forward('placeOrder');
            return;
        }
        try {
            $this->_getCheckoutSession()->unsPaypalTransactionData();
            $this->_initCheckout();
            $this->_checkout->returnFromPaypal($this->_initToken());
            if ($this->_checkout->canSkipOrderReviewStep()) {
                $this->_forward('placeOrder');
            } else {
                $this->_redirect('*/*/review');
            }
            return;
        } catch (\Magento\Framework\Exception\LocalizedException $e) {
            $this->messageManager->addExceptionMessage(
                $e,
                $e->getMessage()
            );
        } catch (\Exception $e) {
            $this->messageManager->addExceptionMessage(
                $e,
                __('We can\'t process Express Checkout approval.')
            );
        }

        return $resultRedirect->setPath('checkout/cart');
    }
}
