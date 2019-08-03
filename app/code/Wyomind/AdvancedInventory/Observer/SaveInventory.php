<?php

/**
 * Copyright © 2016 Wyomind. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace Wyomind\AdvancedInventory\Observer;

class SaveInventory implements \Magento\Framework\Event\ObserverInterface
{

    protected $_managerInterface = null;
    protected $_request = null;

    public function __construct(
    \Magento\Framework\Message\ManagerInterface $messageManager,
            \Magento\Framework\HTTP\PhpEnvironment\Request $request
    )
    {
        $this->_managerInterface = $messageManager;
        $this->_request = $request;
    }

    public function execute(\Magento\Framework\Event\Observer $observer)
    {
        if (!$this->_request->getParam("isAjax")) {
            $om = \Magento\Framework\App\ObjectManager::getInstance();
            $coreHelper = $om->get("\Wyomind\Core\Helper\Data");
            $coreHelper->setDefaultConfig("cataloginventory/options/can_subtract", 0);
            $this->_managerInterface->addWarning(__('Advanced Inventory notice : `Decrease Stock When Order is Placed` must be disabled.'));
        }
    }

}
