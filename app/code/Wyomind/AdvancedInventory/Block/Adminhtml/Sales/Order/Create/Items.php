<?php

/**
 * Copyright © 2016 Wyomind. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace Wyomind\AdvancedInventory\Block\Adminhtml\Sales\Order\Create;

class Items extends \Magento\Framework\View\Element\Template
{

    protected $_coreHelper = null;

    public function __construct(
    \Magento\Framework\View\Element\Template\Context $context,
            \Wyomind\Core\Helper\Data $coreHelper,
            array $data = []
    )
    {

        $coreHelper->constructor($this, func_get_args());
        parent::__construct($context, $data);
        $this->_coreHelper = $coreHelper;
    }

    public function isMultiAssignationEnabled()
    {
        return $this->_coreHelper->getDefaultConfig("advancedinventory/settings/multiple_assignation_enabled") == 1;
    }

}
