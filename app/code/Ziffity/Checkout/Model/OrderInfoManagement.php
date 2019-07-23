<?php
 
namespace Ziffity\Checkout\Model;

use Ziffity\Checkout\Api\OrderInfoManagementInterface;
use Ziffity\Checkout\Model\Data\OrderInfo;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\Exception\CouldNotSaveException;
use Magento\Quote\Api\CartRepositoryInterface;
use Ziffity\Checkout\Api\Data\OrderInfoInterface;

class OrderInfoManagement implements OrderInfoManagementInterface
{
    /**
     * Quote repository.
     *
     * @var \Magento\Quote\Api\CartRepositoryInterface
     */
    protected $quoteRepository;

    /**
     *
     * @param \CartRepositoryInterface $quoteRepository
     */
    public function __construct(
        CartRepositoryInterface $quoteRepository
    ) {
        $this->quoteRepository = $quoteRepository;
    }

    /**
     * @param int $cartId
     * @param OrderInfoInterface $orderInfo
     * @return $quote
     * @throws CouldNotSaveException
     * @throws NoSuchEntityException
     */
    public function saveStoreInfo(
        $cartId,
        OrderInfoInterface $orderInfo
    ) {
         $quote = $this->quoteRepository->getActive($cartId);
         
        if (!$quote->getItemsCount()) {
              throw new NoSuchEntityException(
                  __('Cart %1 doesn\'t contain products', $cartId)
              );
        }
        
        $location = $orderInfo->getStoreLocation();
        $address = $orderInfo->getStoreAddress();

        try {
             $quote->setData(OrderInfo::STORE_LOCATION, strip_tags($location));
             $quote->setData(OrderInfo::STORE_ADDRESS, $address);
             $this->quoteRepository->save($quote);
        } catch (\Exception $e) {
               throw new CouldNotSaveException(
                   __('The store Info could not be saved')
               );
        }

         return $address;
    }
}
