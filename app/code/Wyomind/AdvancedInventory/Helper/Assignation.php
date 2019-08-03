<?php

/*
 * Copyright © 2016 Wyomind. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace Wyomind\AdvancedInventory\Helper;

class Assignation extends \Magento\Framework\App\Helper\AbstractHelper
{

    protected $_helperCore = null;
    protected $_sessionQuote = null;
    protected $_posCollectionFactory = null;

    public function __construct(
    \Magento\Framework\App\Helper\Context $context,
            \Wyomind\Core\Helper\Data $helperCore,
            \Magento\Backend\Model\Session\Quote $sessionQuote,
            \Wyomind\AdvancedInventory\Model\ResourceModel\PointOfSale\CollectionFactory $posCollectionFactory
    )
    {
        $this->_helperCore = $helperCore;
        $this->_sessionQuote = $sessionQuote;
        $this->_posCollectionFactory = $posCollectionFactory;
        parent::__construct($context);
    }

    public function isUpdatable($status)
    {
        $disallowed = $this->_helperCore->getStoreConfig("advancedinventory/settings/disallow_assignation_status");
        return !in_array($status, explode(',', $disallowed));
    }

    
    /* "Creating order in admin" tools */
    
    public function isAdminQuote()
    {
        return $this->_sessionQuote->getQuote() !== null;
    }

    public function getAdminQuoteStoreId()
    {
        return $this->_sessionQuote->getQuote()->getStoreId();
    }

    public function getAdminQuoteDefaultAssignation() {
        $storeId = $this->getAdminQuoteStoreId();
        return $this->_helperCore->getStoreConfig("advancedinventory/settings/default_assignation_admin_order", $storeId);   
    }
    
    
    public function isAutoAssignationEnabled()
    {
        return $this->_helperCore->getDefaultConfig("advancedinventory/settings/autoassign_order") == 1;
    }
    
    public function isAIEnabled()
    {
        return $this->_helperCore->getDefaultConfig("advancedinventory/settings/enabled") == 1;
    }
    
    public function isMultiAssignationEnabled()
    {
        return $this->_helperCore->getDefaultConfig("advancedinventory/settings/multiple_assignation_enabled") == 1;
    }
    
    public function getAdminQuotePOSList()
    {
        if ($this->isAdminQuote()) {
            return $this->_posCollectionFactory->create()->getPlacesByStoreId($this->getAdminQuoteStoreId(),null);
        } else {
            return [];
        }
    }

}
