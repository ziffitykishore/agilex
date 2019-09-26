<?php

namespace PartySupplies\Customer\Plugin;

use Magento\Customer\Controller\Adminhtml\Index\Index;
use PartySupplies\Customer\Helper\Constant;

class CustomerGrid
{
    
    /**
     *
     * @param Index                                    $subject
     * @param \Magento\Backend\Model\View\Result\Page  $result
     *
     * @return \Magento\Backend\Model\View\Result\Page $result
     */
    public function afterExecute(Index $subject, $result)
    {
        $accountType = $subject->getRequest()->getParam('account_type');
        
        if (isset($accountType) && $accountType === Constant::COMPANY) {
            $result->getConfig()->getTitle()->prepend(__('Companies'));
        } elseif (isset($accountType) && $accountType === Constant::CUSTOMER) {
            $result->getConfig()->getTitle()->prepend(__('Users'));
        }
        
        return $result;
    }
}
