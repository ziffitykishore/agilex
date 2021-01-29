<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Travers\CustomerLinking\Block\Adminhtml\Edit\Tab\View;

use Magento\Customer\Api\AccountManagementInterface;
use Magento\Customer\Controller\RegistryConstants;
use Magento\Customer\Model\Address\Mapper;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Customer\Model\Customer;

/**
 * Adminhtml customer view personal information sales block.
 *
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
class PersonalInfo extends \Magento\Customer\Block\Adminhtml\Edit\Tab\View\PersonalInfo
{
    /**
     * get last account linking message
     */
    public function getLinkingMessage()
    {
        $cusAttr = $this->getCustomer()->getcustomAttribute('last_account_linking_message'); 
        if($cusAttr)
            return $cusAttr->getValue();
        return '';
    }

    /**
     * get last account linking date
     */
    public function getLinkingDate()
    {
        $cusAttr = $this->getCustomer()->getcustomAttribute('last_account_linking_date'); 
        if($cusAttr)
            return $cusAttr->getValue();
        return '';
    }

}
   