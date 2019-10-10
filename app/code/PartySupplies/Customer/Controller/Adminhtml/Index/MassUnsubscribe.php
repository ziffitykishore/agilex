<?php

namespace PartySupplies\Customer\Controller\Adminhtml\Index;

use Magento\Customer\Controller\Adminhtml\Index\MassUnsubscribe as MassUnsubscribeCustomer;
use Magento\Customer\Api\CustomerRepositoryInterface;
use Magento\Framework\Controller\ResultFactory;
use Magento\Backend\App\Action\Context;
use Magento\Newsletter\Model\SubscriberFactory;
use Magento\Ui\Component\MassAction\Filter;
use Magento\Customer\Model\ResourceModel\Customer\CollectionFactory;
use Magento\Eav\Model\Entity\Collection\AbstractCollection;

class MassUnsubscribe extends MassUnsubscribeCustomer
{
    /**
     * @var CustomerRepositoryInterface
     */
    protected $customerRepository;

    /**
     * @var SubscriberFactory
     */
    protected $subscriberFactory;

    /**
     * @param Context $context
     * @param Filter $filter
     * @param CollectionFactory $collectionFactory
     * @param CustomerRepositoryInterface $customerRepository
     * @param SubscriberFactory $subscriberFactory
     */
    public function __construct(
        Context $context,
        Filter $filter,
        CollectionFactory $collectionFactory,
        CustomerRepositoryInterface $customerRepository,
        SubscriberFactory $subscriberFactory
    ) {
        parent::__construct($context, $filter, $collectionFactory, $customerRepository, $subscriberFactory);
        $this->customerRepository = $customerRepository;
        $this->subscriberFactory = $subscriberFactory;
    }

    /**
     * 
     * @param AbstractCollection $collection
     * @return \Magento\Backend\Model\View\Result\Redirect
     */
    protected function massAction(AbstractCollection $collection)
    {
        $customersUpdated = 0;
        foreach ($collection->getAllIds() as $customerId) {
            $this->customerRepository->getById($customerId);
            $this->subscriberFactory->create()->unsubscribeCustomerById($customerId);
            $customersUpdated++;
        }

        if ($customersUpdated) {
            $this->messageManager->addSuccess(__('A total of %1 record(s) were updated.', $customersUpdated));
        }

        $resultRedirect = $this->resultFactory->create(ResultFactory::TYPE_REDIRECT);
        $resultRedirect->setPath($this->_redirect->getRefererUrl());

        return $resultRedirect;
    }    
}
