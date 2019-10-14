<?php

namespace PartySupplies\Customer\Ui\Component\Form;

use Magento\Framework\View\Element\UiComponent\ContextInterface;
use Magento\Framework\View\Element\ComponentVisibilityInterface;
use PartySupplies\Customer\Helper\Constant;
use Magento\Customer\Model\CustomerRegistry;

class AssociatedUsersFieldset extends \Magento\Ui\Component\Form\Fieldset implements ComponentVisibilityInterface
{
    /**
     * @var ContextInterface
     */
    protected $context;
    
    /**
     *
     * @var CustomerRegistry
     */
    protected $customerRegistry;

    /**
     * @param ContextInterface $context
     * @param CustomerRegistry $customerRegistry
     * @param array            $components
     * @param array            $data
     */
    public function __construct(
        ContextInterface $context,
        CustomerRegistry $customerRegistry,
        array $components = [],
        array $data = []
    ) {
        $this->context = $context;
        $this->customerRegistry = $customerRegistry;
        parent::__construct($context, $components, $data);
    }

    /**
     * Can show Associated Users tab in tabs or not
     *
     * @return boolean
     */
    public function isComponentVisible(): bool
    {
        $customerId = $this->context->getRequestParam('id');
        if ($customerId) {
            $customer = $this->customerRegistry->retrieve($customerId);
            if ($customer->getAccountType() == Constant::CUSTOMER) {
                return false;
            }
        }
        return (bool)$customerId;
    }
}
