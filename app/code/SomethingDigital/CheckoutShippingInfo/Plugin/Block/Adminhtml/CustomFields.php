<?php

namespace SomethingDigital\CheckoutShippingInfo\Plugin\Block\Adminhtml;

use Magento\Framework\Exception\LocalizedException;
use Magento\Sales\Block\Adminhtml\Order\View\Info;
use SomethingDigital\CheckoutShippingInfo\Api\CustomFieldsRepositoryInterface;

class CustomFields
{

    protected $customFieldsRepository;

    public function __construct(CustomFieldsRepositoryInterface $customFieldsRepository)
    {
        $this->customFieldsRepository = $customFieldsRepository;
    }

    /**
     * Modify after to html.
     *
     * @param Info   $subject Info
     * @param string $result  Result
     *
     * @return string
     * @throws LocalizedException
     */
    public function afterToHtml(Info $subject, $result) {
        $block = $subject->getLayout()->getBlock('order_custom_fields');
        if ($block !== false) {
            $block->setOrderCustomFields(
                $this->customFieldsRepository->getCustomFields($subject->getOrder())
            );
            $result = $result . $block->toHtml();
        }

        return $result;
    }
}
