<?php

namespace SomethingDigital\CheckoutAddress\Plugin\Checkout\Model;

class DefaultConfigProvider
{
    public function afterGetConfig(\Magento\Checkout\Model\DefaultConfigProvider $subject, array $result)
    {
        if (isset($result['customerData']['addresses'])) {
            $result['customerData']['addresses'] = array_reverse($result['customerData']['addresses']);
        }

        return $result;
    }
}
