<?php
namespace Ziffity\Promo\Controller\Index;

use Magento\Framework\App\Action\Context;
use Magento\Checkout\Model\Session;

class Restore extends \Magento\Framework\App\Action\Action
{
    protected $_checkoutSession;

    public function __construct(
        Context $context,
        Session $checkoutSession
    )
    {
        parent::__construct($context);
        $this->_checkoutSession = $checkoutSession;
    }

    public function execute()
    {
        $this->_checkoutSession->setAmpromoDeletedItems(null);
        $this->_checkoutSession->setAmpromoMessages(null);
        $resultRedirect = $this->resultRedirectFactory->create();
        $resultRedirect->setPath('checkout/cart/index');
        return $resultRedirect;
    }

}
