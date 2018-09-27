<?php

namespace Ziffity\CustomerRegIp\Block\Adminhtml\Edit\Tab\View;

use Magento\Customer\Controller\RegistryConstants;

class Regip extends \Magento\Backend\Block\Template
{
    /**
     * Core registry
     *
     * @var \Magento\Framework\Registry
     */
    protected $_coreRegistry;
    protected $_customerRepositoryInterface;

    /**
     * @param \Magento\Backend\Block\Template\Context $context
     * @param \Magento\Framework\Registry $registry
     * @param array $data
     */
    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        \Magento\Framework\Registry $registry,
        \Magento\Customer\Api\CustomerRepositoryInterface $customerRepositoryInterface,
        array $data = []
    ) {
        $this->_coreRegistry = $registry;
        $this->scopeConfig = $context->getScopeConfig();
        $this->_customerRepositoryInterface = $customerRepositoryInterface;

        parent::__construct($context, $data);
    }
    
        /**
     * @return \Magento\Framework\Phrase
     */
    public function getTabLabel()
    {
        return __('Remote IP during registration');
    }
    /**
     * @return \Magento\Framework\Phrase
     */
    public function getTabTitle()
    {
        return __('Remote IP during registration');
    }
    /**
     * @return bool
     */
    public function canShowTab()
    {
        if ($this->getCustomerId()) {
            return true;
        }
        return false;
    }

    /**
     * Tab class getter
     *
     * @return string
     */
    public function getTabClass()
    {
        return '';
    }

    /**
     * Tab should be loaded trough Ajax call
     *
     * @return bool
     */
    public function isAjaxLoaded()
    {
        return false;
    }
    
    /**
     * @return string|null
     */
    public function getCustomerId()
    {
        return $this->_coreRegistry->registry(RegistryConstants::CURRENT_CUSTOMER_ID);
    }

    public function getCustomer(){
        return $this->_customerRepositoryInterface->getById($this->getCustomerId());
    }

    /**
     * Return true if the customer was created in the admin store view
     *
     * @return bool
     */
    public function isCustomerCreatedInAdmin()
    {
        return $this->getCustomer()->getStoreId() == 0;
    }

    public function getCustomerRegIp()
    {
        $value = null;
        $remoteAddr = $this->getCustomer()->getCustomAttribute('registration_remote_ip');
        if($remoteAddr){
           $value = $remoteAddr->getValue();
        }
        /*DEBUG*/
//      $remoteAddr = dns_get_record('google.com', DNS_A);
//      $value = $remoteAddr[0]['ip'];
        return $value;
    }

    /**
     * Return the customer registration ip
     *
     * @return string
     */
    public function getCustomerRegIpHtml()
    {
        $remoteAddr = $this->getCustomerRegIp();
        if (!$this->isValidIp()) {
            $html = __('- REGISTRATION IP UNAVAILABLE -');
        } else {
            $html = __($remoteAddr);
        }
        return $html;
    }
    /**
     *
     * @return bool
     */
    public function isValidIp()
    {
        $remoteAddr = $this->getCustomerRegIp();
        return !empty($remoteAddr);
    }
    /**
     *
     * @return string
     */
    public function getAjaxLookupUrl()
    {
        return $this->getUrl(
            'customerregip/index/lookup', array('ip' => $this->getCustomerRegIp())
        );
    }

    /**
     *
     * @return bool
     */
    public function isIpInfoDbEnabled()
    {
        return (bool)trim($this->scopeConfig->getValue('customerregip/general/ipinfodb_api_key'));
    }
}
