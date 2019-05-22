<?php

/**
 * EYEMAGINE - The leading Magento Solution Partner
 *
 * HubSpot Integration with Magento
 *
 * @author    EYEMAGINE <magento@eyemaginetech.com>
 * @copyright Copyright (c) 2016 EYEMAGINE Technology, LLC (http://www.eyemaginetech.com)
 * @license   http://www.eyemaginetech.com/license
 */
namespace Eyemagine\HubSpot\Controller\Sync;

use Magento\Framework\App\Action\Context;
use Eyemagine\HubSpot\Controller\AbstractSync;
use Magento\Framework\Controller\Result\JsonFactory;
use Eyemagine\HubSpot\Helper\Sync as HelperSync;
use Magento\Customer\Model\ResourceModel\Customer\CollectionFactory as CustomerCollection;
use Exception;

/**
 * Class GetCustomers
 *
 * @package Eyemagine\HubSpot\Controller\Sync
 */
class GetCustomers extends AbstractSync
{

    /**
     *
     * @var \Eyemagine\HubSpot\Helper\Sync
     */
    protected $helper;

    /**
     *
     * @var \Magento\Customer\Model\ResourceModel\Customer\CollectionFactory
     */
    protected $customerCollection;
    
     /**
     *
     * @var array
     */
    protected $excludeCustomerData;

    /**
     *
     * @param \Magento\Framework\App\Action\Context $context
     * @param \Magento\Framework\Controller\Result\JsonFactory $resultJsonFactory
     * @param \Eyemagine\HubSpot\Helper\Sync $helperSync
     * @param \Magento\Customer\Model\ResourceModel\Customer\CollectionFactory $customerCollection
     */
    public function __construct(
        Context $context,
        JsonFactory $resultJsonFactory,
        HelperSync $helper,
        CustomerCollection $customerCollection
    ) {
        parent::__construct($context, $resultJsonFactory);
        
        $this->helper = $helper;
        
        $this->customerCollection = $customerCollection;
        
        $this->excludeCustomerData = array(
            'password_hash',
            'rp_token',
            'rp_token_created_at'
        );
    }

    /**
     * Get customer data
     *
     * @return \Magento\Framework\Controller\Result\Json
     */
    public function execute()
    {
        try {
            if (! $this->helper->authenticate()) {
                return $this->outputError($this->helper->getErrorCode(), $this->helper->getErrorMessage(), null);
            }
            
            $request = $this->getRequest();
            $multistore = $request->getParam('multistore', self::IS_MULTISTORE);
            $start = gmdate('Y-m-d H:i:s', $request->getParam('start', 0));
            $end = gmdate('Y-m-d H:i:s', time() - 300);
            $entityId = $request->getParam('id', '0');
            $maxperpage = $request->getParam('maxperpage', self::MAX_CUSTOMER_PERPAGE);
            $websiteId = $this->helper->getWebsiteId();
            $storeId = $this->helper->getStoreId();
            $customerData = array();
            
            $custGroups = $this->helper->getCustomerGroups();
            
            $collection = $this->customerCollection->create()
                ->addAttributeToSelect('*')
                ->addFieldToFilter('updated_at', array(
                'from' => $start,
                'to' => $end,
                'date' => true
                ))
                ->addFieldToFilter('entity_id', array(
                    'gt' => $entityId
                ))
                ->setOrder('updated_at', self::SORT_ORDER_ASC)
                ->setOrder('entity_id', self::SORT_ORDER_ASC)
                ->setPageSize($maxperpage);
              
            // only add the filter if website id > 0
            if (! ($multistore) && $websiteId) {
                $collection->addFieldToFilter('website_id', array(
                    'eq' => $websiteId
                ));
            }
            
            foreach ($collection as $customer) {
                if ($customer->getDefaultBilling()) {
                    $customer->setDefaultBillingAddress(
                        $this->helper->convertAttributeData(
                            $customer->getDefaultBillingAddress()
                        )
                    );
                }
                
                // clear unwanted data
                foreach ($this->excludeCustomerData as $exclude) {
                    $customer->unsetData($exclude);
                }
                
                $groupId = (int) $customer->getGroupId();
                
                if (isset($custGroups[$groupId])) {
                    $customer->setCustomerGroup($custGroups[$groupId]);
                }
                $customerData[$customer->getId()] = $customer->getData();
            }
        } catch (Exception $e) {
            return $this->outputError(self::ERROR_CODE_UNKNOWN_EXCEPTION, 'Unknown exception on request', $e);
        }
        
        return $this->outputJson(array(
            'customers' => $customerData,
            'website' => $websiteId,
            'store' => $storeId,
	    'start' => $start,
            'custgroups' => $custGroups
        ));
    }
}
