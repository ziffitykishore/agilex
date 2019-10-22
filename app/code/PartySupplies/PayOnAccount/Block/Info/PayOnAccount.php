<?php

namespace PartySupplies\PayOnAccount\Block\Info;

/**
 *
 * @SuppressWarnings(PHPMD.CamelCasePropertyName)
 */
class PayOnAccount extends \Magento\Payment\Block\Info
{
    /**
     * @var string
     */
    protected $_template = 'PartySupplies_PayOnAccount::info/payonaccount.phtml';

    /**
     * @return string
     */
    public function toPdf()
    {
        $this->setTemplate('PartySupplis_PayOnAccount::info/pdf/payonaccount.phtml');
        return $this->toHtml();
    }
}
