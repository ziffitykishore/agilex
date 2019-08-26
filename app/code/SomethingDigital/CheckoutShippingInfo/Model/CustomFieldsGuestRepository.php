<?php

namespace SomethingDigital\CheckoutShippingInfo\Model;

use Magento\Quote\Model\QuoteIdMaskFactory;
use SomethingDigital\CheckoutShippingInfo\Api\CustomFieldsGuestRepositoryInterface;
use SomethingDigital\CheckoutShippingInfo\Api\CustomFieldsRepositoryInterface;
use SomethingDigital\CheckoutShippingInfo\Api\Data\CustomFieldsInterface;

/**
 * Class CustomFieldsGuestRepository
 *
 * @category Model/Repository
 * @package  Bodak\CheckoutCustomForm\Model
 */
class CustomFieldsGuestRepository implements CustomFieldsGuestRepositoryInterface
{
    /**
     * @var QuoteIdMaskFactory
     */
    protected $quoteIdMaskFactory;

    /**
     * @var CustomFieldsRepositoryInterface
     */
    protected $customFieldsRepository;

    /**
     * @param QuoteIdMaskFactory              $quoteIdMaskFactory
     * @param CustomFieldsRepositoryInterface $customFieldsRepository
     */
    public function __construct(
        QuoteIdMaskFactory $quoteIdMaskFactory,
        CustomFieldsRepositoryInterface $customFieldsRepository
    ) {
        $this->quoteIdMaskFactory     = $quoteIdMaskFactory;
        $this->customFieldsRepository = $customFieldsRepository;
    }

    /**
     * @param string                $cartId
     * @param CustomFieldsInterface $customFields
     * @return CustomFieldsInterface
     */
    public function saveCustomFields(
        string $cartId,
        CustomFieldsInterface $customFields
    ): CustomFieldsInterface {
        $quoteIdMask = $this->quoteIdMaskFactory->create()->load($cartId, 'masked_id');
        return $this->customFieldsRepository->saveCustomFields((int)$quoteIdMask->getQuoteId(), $customFields);
    }
}
