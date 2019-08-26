<?php

namespace SomethingDigital\CheckoutShippingInfo\Model;

use Magento\Quote\Api\CartRepositoryInterface;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\Exception\CouldNotSaveException;
use Magento\Sales\Model\Order;
use SomethingDigital\CheckoutShippingInfo\Api\CustomFieldsRepositoryInterface;
use SomethingDigital\CheckoutShippingInfo\Api\Data\CustomFieldsInterface;

/**
 * Class CustomFieldsRepository
 *
 * @category Model/Repository
 * @package  Bodak\CheckoutCustomForm\Model
 */
class CustomFieldsRepository implements CustomFieldsRepositoryInterface
{
    /**
     * Quote repository.
     *
     * @var CartRepositoryInterface
     */
    protected $cartRepository;

    /**
     * ScopeConfigInterface
     *
     * @var ScopeConfigInterface
     */
    protected $scopeConfig;

    /**
     * CustomFieldsInterface
     *
     * @var CustomFieldsInterface
     */
    protected $customFields;

    /**
     * CustomFieldsRepository constructor.
     *
     * @param CartRepositoryInterface $cartRepository CartRepositoryInterface
     * @param ScopeConfigInterface    $scopeConfig    ScopeConfigInterface
     * @param CustomFieldsInterface   $customFields   CustomFieldsInterface
     */
    public function __construct(
        CartRepositoryInterface $cartRepository,
        ScopeConfigInterface $scopeConfig,
        CustomFieldsInterface $customFields
    ) {
        $this->cartRepository = $cartRepository;
        $this->scopeConfig    = $scopeConfig;
        $this->customFields   = $customFields;
    }
    /**
     * Save checkout custom fields
     *
     * @param int                                                      $cartId       Cart id
     * @param \SomethingDigital\CheckoutShippingInfo\Api\Data\CustomFieldsInterface $customFields Custom fields
     *
     * @return \SomethingDigital\CheckoutShippingInfo\Api\Data\CustomFieldsInterface
     * @throws CouldNotSaveException
     * @throws NoSuchEntityException
     */
    public function saveCustomFields(
        int $cartId,
        CustomFieldsInterface $customFields
    ): CustomFieldsInterface {
        $cart = $this->cartRepository->getActive($cartId);
        if (!$cart->getItemsCount()) {
            throw new NoSuchEntityException(__('Cart %1 is empty', $cartId));
        }

        try {
            $cart->setData(
                CustomFieldsInterface::CHECKOUT_PONUMBER,
                $customFields->getCheckoutPonumber()
            );
            $cart->setData(
                CustomFieldsInterface::CHECKOUT_SHIPTOPO,
                $customFields->getCheckoutShiptopo()
            );
            $cart->setData(
                CustomFieldsInterface::CHECKOUT_DELIVERYPOINT,
                $customFields->getCheckoutDeliverypoint()
            );
            $cart->setData(
                CustomFieldsInterface::CHECKOUT_ORDERNOTES,
                $customFields->getCheckoutOrdernotes()
            );

            $this->cartRepository->save($cart);
        } catch (\Exception $e) {
            throw new CouldNotSaveException(__('Custom order data could not be saved!'));
        }

        return $customFields;
    }

    /**
     * Get checkout custom fields by given order id
     *
     * @param Order $order Order
     *
     * @return CustomFieldsInterface
     * @throws NoSuchEntityException
     */
    public function getCustomFields(Order $order): CustomFieldsInterface
    {
        if (!$order->getId()) {
            throw new NoSuchEntityException(__('Order %1 does not exist', $order));
        }

        $this->customFields->setCheckoutPonumber(
            $order->getData(CustomFieldsInterface::CHECKOUT_PONUMBER)
        );
        $this->customFields->setCheckoutShiptopo(
            $order->getData(CustomFieldsInterface::CHECKOUT_SHIPTOPO)
        );
        $this->customFields->setCheckoutDeliverypoint(
            $order->getData(CustomFieldsInterface::CHECKOUT_DELIVERYPOINT)
        );
        $this->customFields->setCheckoutOrdernotes(
            $order->getData(CustomFieldsInterface::CHECKOUT_ORDERNOTES)
        );


        return $this->customFields;
    }
}
