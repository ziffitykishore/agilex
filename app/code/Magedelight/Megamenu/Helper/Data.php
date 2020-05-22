<?php
/**
 * Magedelight
 * Copyright (C) 2017 Magedelight <info@magedelight.com>
 *
 * @category Magedelight
 * @package Magedelight_Megamenu
 * @copyright Copyright (c) 2017 Mage Delight (http://www.magedelight.com/)
 * @license http://opensource.org/licenses/gpl-3.0.html GNU General Public License,version 3 (GPL-3.0)
 * @author Magedelight <info@magedelight.com>
 */

namespace Magedelight\Megamenu\Helper;

use \Magento\Customer\Model\GroupFactory;
use \Magento\Framework\App\Helper\Context;
use \Magento\Framework\App\Helper\AbstractHelper;

class Data extends AbstractHelper
{

    public $customerGroupFactory;

    /**
     * Data constructor.
     * @param Context $context
     * @param GroupFactory $customerGroupFactory
     */
    public function __construct(
        Context $context,
        GroupFactory $customerGroupFactory
    ) {
        parent::__construct($context);
        $this->customerGroupFactory = $customerGroupFactory;
    }
    
    public function getCustomerGroupsOptions()
    {
        $groupCollection = $this->customerGroupFactory->create()->getCollection()
            ->load()
            ->toOptionHash();
        $optionString = '';
        foreach ($groupCollection as $groupId => $code) {
            $optionString .= '<option value="'.$groupId.'">'.$code.'</option>';
        }
        return $optionString;
    }
    
    public function getCustomerGroups()
    {
        $groupCollection = $this->customerGroupFactory->create()->getCollection()
            ->load()
            ->toOptionHash();
        return $groupCollection;
    }

    public function isEnabled()
    {
        return $this->getConfig('magedelight/general/megamenu_status');
    }    

    public function getConfig($config_path)
    {
        return $this->scopeConfig->getValue(
            $config_path,
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE
        );
    }
    
    public function menuTypes()
    {
        return [
          'megamenu'=>'Mega Menu Block',
          'category'=>'Category Selection',
          'pages'=>'Page Selection',
          'link'=>'External Links'
        ];
    }
    
    public function getMenuName($key)
    {
        $menuTypes = $this->menuTypes();
        return $menuTypes[$key];
    }
}
