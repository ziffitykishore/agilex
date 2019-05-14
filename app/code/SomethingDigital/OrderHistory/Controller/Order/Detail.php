<?php

namespace SomethingDigital\OrderHistory\Controller\Order;

use SomethingDigital\OrderHistory\Model\OrdersApi;
use Magento\Framework\DataObjectFactory;
use Magento\Customer\Model\Session;
use Magento\Customer\Api\CustomerRepositoryInterface;
use Magento\Framework\View\Result\PageFactory;
use Magento\Framework\Controller\ResultFactory;
use Magento\Framework\Registry;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\App\Action\Action;
use Magento\Framework\App\Action\Context;

class Detail extends Action
{
    /**
     * @var Session
     */
    private $session;

    /**
     * @var CustomerRepositoryInterface
     */
    private $customerRepo;

    /**
     * @var OrdersApi
     */
    private $ordersApi;

    /**
     * @var DataObjectFactory
     */
    private $dataObjectFactory;

    /**
     * @var Registry
     */
    private $registry;

    /**
     * @var PageFactory
     */
    private $resultPageFactory;

    public function __construct(
        Context $context,
        Session $session,
        CustomerRepositoryInterface $customerRepo,
        OrdersApi $ordersApi,
        Registry $registry,
        PageFactory $resultPageFactory,
        DataObjectFactory $dataObjectFactory
    ) {
        $this->session = $session;
        $this->customerRepo = $customerRepo;
        $this->dataObjectFactory = $dataObjectFactory;
        $this->ordersApi = $ordersApi;
        $this->registry = $registry;
        $this->resultPageFactory = $resultPageFactory;
        parent::__construct($context);
    }

    public function execute()
    {
        /** @var \Magento\Framework\View\Result\Page $resultPage */
        $resultPage = $this->resultPageFactory->create();
        $orderId = (int)$this->getRequest()->getParam('order');
        try {
            $apiResult = $this->ordersApi->getOrder($orderId);
        } catch (LocalizedException $e) {
            $this->messageManager->addErrorMessage(__('Something went wrong when loading your order'));
            $resultRedirect = $this->resultFactory->create(ResultFactory::TYPE_REDIRECT);
            return $resultRedirect->setUrl($this->_redirect->getRefererUrl());
        }
        $order = $this->dataObjectFactory->create();
        $order->setData($apiResult['body']);
        $this->registry->register('sx_current_order', $order);
        return $resultPage;
    }
}
