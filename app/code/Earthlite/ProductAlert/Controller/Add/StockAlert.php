<?php

namespace Earthlite\ProductAlert\Controller\Add;

use Magento\Framework\App\Action\HttpPostActionInterface;
use Magento\Framework\App\Action\Action;
use Magento\Framework\App\Action\Context;
use Magento\Customer\Api\AccountManagementInterface;
use Magento\Store\Model\StoreManagerInterface;
use Magento\Catalog\Api\ProductRepositoryInterface;
use Magento\ProductAlert\Model\Stock;
use Magento\Framework\Controller\ResultFactory;
use Magento\Framework\Controller\Result\Redirect;

/**
 * Controller for guest users stock alert subscription
 */
class StockAlert extends Action implements HttpPostActionInterface
{
    /**
     * @var AccountManagementInterface
     */
    protected $customerAccountManagement;

    /**
     * @var StoreManagerInterface
     */
    protected $storeManager;

    /**
     * @var ProductRepositoryInterface
     */
    protected $productRepository;

    /**
     * @var Stock
     */
    protected $productAlertModel;

    /**
     * @param Context $context
     * @param AccountManagementInterface $customerAccountManagement
     * @param StoreManagerInterface $storeManager
     * @param ProductRepositoryInterface $productRepository
     * @param Stock $productAlertModel
     */
    public function __construct(
        Context $context,
        AccountManagementInterface $customerAccountManagement,
        StoreManagerInterface $storeManager,
        ProductRepositoryInterface $productRepository,
        Stock $productAlertModel
    ) {
        parent::__construct($context);
        $this->customerAccountManagement = $customerAccountManagement;
        $this->storeManager = $storeManager;
        $this->productRepository = $productRepository;
        $this->productAlertModel = $productAlertModel;
    }

    /**
     * Stock alert subscription for Guest users.
     *
     * @return Redirect
     */
    public function execute()
    {
        if (!$this->getRequest()->isPost()) {
            return $this->resultRedirectFactory->create()->setPath('*/*/');
        }

        if ($this->isCustomerExist($this->getRequest()->getParam('email'))) {
            return $this->resultRedirectFactory->create()->setPath('customer/account/login');
        }

        try {
            $product = $this->productRepository->getById((int)$this->getRequest()->getParam('product_id'));
            $store = $this->storeManager->getStore();
            $this->productAlertModel->setCustomerId(0)
                ->setProductId($product->getId())
                ->setEmail($this->getRequest()->getParam('email'))
                ->setWebsiteId($store->getWebsiteId())
                ->setStoreId($store->getId())
                ->save();
            $this->messageManager->addSuccessMessage(__('Alert subscription has been saved.'));
        } catch (NoSuchEntityException $noEntityException) {
            $this->messageManager->addErrorMessage(__('The product does not exist.'));
            $resultRedirect = $this->resultFactory->create(ResultFactory::TYPE_REDIRECT);
            $resultRedirect->setUrl($this->_redirect->getRedirectUrl());

            return $resultRedirect;
        } catch (\Exception $e) {
            $this->messageManager->addExceptionMessage(
                $e,
                __("The alert subscription couldn't update at this time. Please try again later.")
            );
        }
        $resultRedirect = $this->resultFactory->create(ResultFactory::TYPE_REDIRECT);
        $resultRedirect->setUrl($this->_redirect->getRedirectUrl());

        return $resultRedirect;
    }

    /**
     * Checks whether the customer is already exist or not.
     * 
     * @param string $email
     * @return bool
     */
    protected function isCustomerExist(string $email)
    {
        return !$this->customerAccountManagement->isEmailAvailable(
            $email,
            (int)$this->storeManager->getWebsite()->getId()
        );
    }
}
