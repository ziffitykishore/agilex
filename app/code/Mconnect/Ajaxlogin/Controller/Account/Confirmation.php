<?php
/**
 *
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Mconnect\Ajaxlogin\Controller\Account;

use Magento\Customer\Model\Url;
use Magento\Framework\App\Action\Context;
use Magento\Customer\Model\Session;
use Magento\Framework\App\ObjectManager;
use Magento\Framework\View\Result\PageFactory;
use Magento\Store\Model\StoreManagerInterface;
use Magento\Customer\Api\AccountManagementInterface;
use Magento\Framework\Exception\State\InvalidTransitionException;

class Confirmation extends \Magento\Framework\App\Action\Action
{
    /**
     * @var \Magento\Store\Model\StoreManagerInterface
     */
    protected $storeManager;

    /**
     * @var \Magento\Customer\Api\AccountManagementInterface
     */
    protected $customerAccountManagement;

    /**
     * @var Session
     */
    protected $session;

    /**
     * @var PageFactory
     */
    protected $resultPageFactory;

    /**
     * @var Url
     */
    private $customerUrl;
    
    protected $resultJsonFactory;

    /**
     * @param Context $context
     * @param Session $customerSession
     * @param PageFactory $resultPageFactory
     * @param StoreManagerInterface $storeManager
     * @param AccountManagementInterface $customerAccountManagement
     * @param Url $customerUrl
     */
    public function __construct(
        Context $context,
        Session $customerSession,
        PageFactory $resultPageFactory,
        StoreManagerInterface $storeManager,
        AccountManagementInterface $customerAccountManagement,
        Url $customerUrl = null,
        \Magento\Framework\Controller\Result\JsonFactory $resultJsonFactory
    ) {
        $this->session = $customerSession;
        $this->resultPageFactory = $resultPageFactory;
        $this->storeManager = $storeManager;
        $this->customerAccountManagement = $customerAccountManagement;
        $this->customerUrl = $customerUrl ?: ObjectManager::getInstance()->get(Url::class);
        $this->resultJsonFactory = $resultJsonFactory;
        parent::__construct($context);
    }

    /**
     * Send confirmation link to specified email
     *
     * @return \Magento\Framework\Controller\Result\Redirect|\Magento\Framework\View\Result\Page
     */
    public function execute()
    {
        // try to confirm by email
        $email = $this->getRequest()->getParam('email');
        $response = [
            'errors' => true,
            'message' => __('email should not empty.')
        ];
        if ($email) {
            try {
                $this->customerAccountManagement->resendConfirmation(
                    $email,
                    $this->storeManager->getStore()->getWebsiteId()
                );
                $response = [
                    'errors' => false,
                    'message' => __('Please check your email for confirmation key.')
                ];
                //$this->messageManager->addSuccess(__('Please check your email for confirmation key.'));
            } catch (InvalidTransitionException $e) {
                $response = [
                    'errors' => true,
                    'message' => __($e->getMessage())
                ];
                //$this->messageManager->addSuccess(__('This email does not require confirmation.'));
            } catch (\Exception $e) {
                //$this->messageManager->addException($e, __('Wrong email.'));
                $response = [
                    'errors' => true,
                    'message' => __($e->getMessage(). 'Wrong email.')
                ];
                //$resultRedirect->setPath('*/*/*', ['email' => $email, '_secure' => true]);
                //return $resultRedirect;
            }
        }
        /** @var \Magento\Framework\Controller\Result\Json $resultJson */
        $resultJson = $this->resultJsonFactory->create();
        return $resultJson->setData($response);
    }
}
