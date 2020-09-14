<?php

namespace SomethingDigital\CheckoutAccountId\Plugin;

use Magento\Framework\View\Element\Template\Context;
use Magento\CheckoutAgreements\Model\ResourceModel\Agreement\CollectionFactory;
use Magento\Checkout\Model\Session;
use Magento\Customer\Model\AddressFactory;
use Magento\Customer\Model\Session as CustomerSession;
use Magento\Checkout\Block\Checkout\LayoutProcessor;

class LayoutProcessorPlugin
{

    /**
     * @var ScopeConfigInterface
     */
    protected $scopeConfig;

    /**
     * @var Session
     */
    protected $checkoutSession;

    /**
     * @var AddressFactory
     */
    protected $customerAddressFactory;

    /**
     * @var CustomerSession
     */
    protected $customerSession;

    public function __construct(
        Context $context,
        CollectionFactory $agreementCollectionFactory,
        Session $checkoutSession,
        AddressFactory $customerAddressFactory,
        CustomerSession $session
    ) {
        $this->scopeConfig = $context->getScopeConfig();
        $this->checkoutSession = $checkoutSession;
        $this->customerAddressFactory = $customerAddressFactory;
        $this->customerSession = $session;
    }
    /**
     * @param \Magento\Checkout\Block\Checkout\LayoutProcessor $subject
     * @param array $jsLayout
     * @return array
     */
    public function afterProcess(
        LayoutProcessor $subject,
        array  $jsLayout
    ) {

        if (!$this->customerSession->isLoggedIn()) {
            $jsLayout['components']['checkout']['children']['steps']['children']['shipping-step']['children']
            ['shippingAddress']['children']['before-form']['children']['travers_account_id'] = [
                'component' => 'Magento_Ui/js/form/element/abstract',
                'config' => [
                    'customScope' => 'shippingAddress',
                    'template' => 'ui/form/field',
                    'options' => [],
                    'id' => 'travers-account-id'
                ],
                'dataScope' => 'shippingAddress.travers_account_id',
                'label' => 'Account #',
                'provider' => 'checkoutProvider',
                'visible' => true,
                'validation' => [],
                'sortOrder' => 200,
                'id' => 'travers-account-id'
            ];
        }


        return $jsLayout;
    }
}
