<?php

namespace SomethingDigital\CheckoutAddress\Model\Ui;

use Magento\Checkout\Model\ConfigProviderInterface;
use Magento\Customer\Model\Session;

class ConfigProvider implements ConfigProviderInterface
{
    /**
     * @var Session
     */
    protected $session;

    /**
     * ConfigProvider constructor.
     * @param Session $session
     */
    public function __construct(
        Session $session
    ) {
        $this->session = $session;
    }

    /**
     * @return array
     */
    public function getConfig()
    {
        return [
            'payment' => [
                'billing_address' => [
                    'id' => $this->session->getCustomerDataObject()->getDefaultBilling(),
                ]
            ]
        ];
    }
}
