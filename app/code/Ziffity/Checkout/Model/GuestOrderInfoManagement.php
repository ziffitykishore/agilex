<?php
 
namespace Ziffity\Checkout\Model;

use Ziffity\Checkout\Api\GuestOrderInfoManagementInterface;
use Magento\Quote\Model\QuoteIdMaskFactory;
use Ziffity\Checkout\Api\OrderInfoManagementInterface;
use Ziffity\Checkout\Api\Data\OrderInfoInterface;

class GuestOrderInfoManagement implements GuestOrderInfoManagementInterface
{
    /**
     * @var QuoteIdMaskFactory
     */
    protected $quoteIdMaskFactory;

    /**
     * @var OrderInfoManagementInterface
     */
    protected $orderInfoManagement;
    
    /**
     * GuestOrderInfoManagement constructor.
     * @param QuoteIdMaskFactory $quoteIdMaskFactory
     * @param OrderInfoManagementInterface $orderInfoManagement
     */
    public function __construct(
        QuoteIdMaskFactory $quoteIdMaskFactory,
        OrderInfoManagementInterface $orderInfoManagement
    ) {
        $this->quoteIdMaskFactory = $quoteIdMaskFactory;
        $this->orderInfoManagement = $orderInfoManagement;
    }

    /**
     * {@inheritDoc}
     */
    public function saveStoreInfo(
        $cartId,
        OrderInfoInterface $orderInfo
    ) {
        $quoteIdMask = $this->quoteIdMaskFactory->create()
            ->load($cartId, 'masked_id');
                            
        return $this->orderInfoManagement->saveStoreInfo(
            $quoteIdMask->getQuoteId(),
            $orderInfo
        );
    }
}
