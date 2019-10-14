<?php

namespace PartySupplies\Customer\Controller\Adminhtml\Index;

use Magento\Customer\Controller\Adminhtml\Index\Edit as GridEdit;
use Magento\Customer\Api\Data\CustomerInterface;
use Magento\Framework\Exception\NoSuchEntityException;

class Edit extends GridEdit
{
    /**
     * 
     * @return \Magento\Framework\View\Result\PageFactory
     */
    public function execute()
    {
        $customerId = $this->initCurrentCustomer();

        $customerData = [];
        $customerData['account'] = [];
        $customerData['address'] = [];
        $customer = null;
        $isExistingCustomer = (bool)$customerId;
        if ($isExistingCustomer) {
            try {
                $customer = $this->_customerRepository->getById($customerId);
                $customerData['account'] = $this->customerMapper->toFlatArray($customer);
                $customerData['account'][CustomerInterface::ID] = $customerId;
                try {
                    $addresses = $customer->getAddresses();
                    foreach ($addresses as $address) {
                        $customerData['address'][$address->getId()] = $this->addressMapper->toFlatArray($address);
                        $customerData['address'][$address->getId()]['id'] = $address->getId();
                    }
                } catch (NoSuchEntityException $e) {
                    //do nothing
                }
            } catch (NoSuchEntityException $e) {
                $this->messageManager->addException($e, __('Something went wrong while editing the customer.'));
                $resultRedirect = $this->resultRedirectFactory->create();
                $resultRedirect->setPath('customer/*/index');
                return $resultRedirect;
            }
        }
        $customerData['customer_id'] = $customerId;
        $this->_getSession()->setCustomerData($customerData);

        $resultPage = $this->resultPageFactory->create();
        $resultPage->setActiveMenu('Magento_Customer::customer_manage');
        $this->prepareDefaultCustomerTitle($resultPage);
        $resultPage->setActiveMenu('Magento_Customer::customer');

        $resultPage->getConfig()->getTitle()->prepend(__('New User'));

        if (strpos($this->_redirect->getRefererUrl(), 'account_type/company')) {
            $resultPage->getConfig()->getTitle()->prepend(__('New Company'));
        }
        if ($isExistingCustomer) {
            $resultPage->getConfig()->getTitle()->prepend($this->_viewHelper->getCustomerName($customer));
        }
        return $resultPage;
    }    
}
