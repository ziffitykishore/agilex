<?php

/**
 * Copyright © 2016 Wyomind. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace Wyomind\AdvancedInventory\Plugin\PointOfSale\Block\Adminhtml;

class Manage
{

    protected $_helperPermissions;
    protected $_messageManager = null;
    protected $_url = null;

    public function __construct(
        \Wyomind\AdvancedInventory\Helper\Permissions $helperPermissions,
        \Magento\Framework\Message\ManagerInterface $messageManager,
             \Magento\Framework\UrlInterface $urlInterface
    ) {
        $this->_helperPermissions = $helperPermissions;
        $this->_messageManager = $messageManager;
        $this->_url = $urlInterface;
    }

    public function after_construct($subject, $return)
    {
        if (!$this->_helperPermissions->hasAllPermissions()) {
            $subject->removeButton("add");
            $subject->removeButton("import");
            $subject->removeButton("export");
            $this->_messageManager->addError(__("You are not allowed to create a POS/WH, or to import/export a csv file.<br/>Please check the user permissions: ")."<a href='".$this->_url->getUrl("advancedinventory/permissions/index")."'>".__("Permissions")."</a>");
        }
        return $return;
    }
}
