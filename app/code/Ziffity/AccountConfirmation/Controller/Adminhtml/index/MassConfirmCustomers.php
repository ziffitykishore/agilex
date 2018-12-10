<?php

namespace Ziffity\AccountConfirmation\Controller\Adminhtml\Index;

use Magento\Customer\Controller\Adminhtml\Index\AbstractMassAction;
use Magento\Backend\App\Action\Context;
use Magento\Customer\Model\ResourceModel\Customer\CollectionFactory;
use Magento\Eav\Model\Entity\Collection\AbstractCollection;
use Magento\Ui\Component\MassAction\Filter;
use Magento\Customer\Api\CustomerRepositoryInterface;
use Magento\Framework\Controller\ResultFactory;
use Magento\Customer\Model\EmailNotificationInterface;
use Magento\Framework\App\ObjectManager;

/**
 * Class MassConfirmCustomers
 */
class MassConfirmCustomers extends AbstractMassAction
{
    /**
     * @var CustomerRepositoryInterface
     */
    protected $customerRepository;

    /**
     * @param Context $context
     * @param Filter $filter
     * @param CollectionFactory $collectionFactory
     * @param CustomerRepositoryInterface $customerRepository
     */
    public function __construct(
        Context $context,
        Filter $filter,
        CollectionFactory $collectionFactory,
        CustomerRepositoryInterface $customerRepository
    ) {
        parent::__construct($context, $filter, $collectionFactory);
        $this->customerRepository = $customerRepository;
    }

    /**
     * @param AbstractCollection $collection
     * @return \Magento\Backend\Model\View\Result\Redirect
     */
    protected function massAction(AbstractCollection $collection)
    {
        $confirmedCustomers = 0;
        foreach ($collection->getAllIds() as $customerId) {
            $customer = $this->customerRepository->getById($customerId);
            if ($customer->getConfirmation()) {
                $customer->setConfirmation(null);
                $this->customerRepository->save($customer);
                $this->getEmailNotification()->newAccount($customer, EmailNotificationInterface::NEW_ACCOUNT_EMAIL_CONFIRMED);
                $confirmedCustomers++;
            }
        }

        if ($confirmedCustomers) {
            $this->messageManager->addSuccess(__('A total of %1 record(s) were manually confirmed.', $confirmedCustomers));
        }

        /** @var \Magento\Backend\Model\View\Result\Redirect $resultRedirect */
        $resultRedirect = $this->resultFactory->create(ResultFactory::TYPE_REDIRECT);
        $resultRedirect->setPath($this->getComponentRefererUrl());

        return $resultRedirect;
    }

    /**
     * Get email notification
     *
     * @return EmailNotificationInterface
     */
    private function getEmailNotification()
    {
        return ObjectManager::getInstance()->get(EmailNotificationInterface::class);
    }
}
