<?php
/**
 *
 * Copyright � 2015 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Mconnect\Ajaxlogin\Controller\Popup;

use Magento\Customer\Api\AccountManagementInterface;
use Magento\Customer\Model\AccountManagement;
use Magento\Customer\Model\Session;
use Magento\Framework\App\Action\Action;
use Magento\Framework\App\Action\Context;
use Magento\Framework\Escaper;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\Exception\SecurityViolationException;
use Magento\Framework\Json\Helper\Data as JsonHelper;

class Forgot extends Action
{
    /** @var AccountManagementInterface */
    protected $customerAccountManagement;

    /** @var Escaper */
    protected $escaper;

    /**
     * @var Session
     */
    protected $session;
    protected $jsonHelper;
    protected $layoutFactory;

    /**
     * @param Context                    $context
     * @param Session                    $customerSession
     * @param AccountManagementInterface $customerAccountManagement
     * @param Escaper                    $escaper
     * @param JsonHelper                 $jsonHelper
     */
    public function __construct(
        Context $context,
        Session $customerSession,
        AccountManagementInterface $customerAccountManagement,
        Escaper $escaper,
        JsonHelper $jsonHelper,
        \Magento\Framework\View\LayoutFactory $layoutFactory
    ) {
        $this->session                   = $customerSession;
        $this->customerAccountManagement = $customerAccountManagement;
        $this->escaper                   = $escaper;
        $this->jsonHelper                = $jsonHelper;
        $this->layoutFactory             = $layoutFactory;
        parent::__construct($context);
    }

    /**
     * Forgot customer password action
     *
     * @return \Magento\Framework\Controller\Result\Redirect
     */
    public function execute()
    {
        $blockMsg = $this->layoutFactory->create()->getMessagesBlock();
        
        /** @var \Magento\Framework\Controller\Result\Redirect $resultRedirect */
        $email = (string) $this->getRequest()->getParam('email');
        if ($email) {
            if (!\Zend_Validate::is($email, 'EmailAddress')) {
                $this->session->setForgottenEmail($email);
                $error        = true;
                $success      = false;
                $message      = __('Please correct the email address.');
                $this->messageManager->addError($message);
            }
            try {
                $this->customerAccountManagement->initiatePasswordReset(
                    $email,
                    AccountManagement::EMAIL_RESET
                );
                $error        = true;
                $success      = true;
                $message      = __(
                    'If there is an account associated with %1 you will receive an email with a link to reset your password.',
                    $this->escaper->escapeHtml($email)
                );
                $this->messageManager->addSuccess($message);
            } catch (NoSuchEntityException $e) {
                $success        = false;
                $error          = true;
                $message        = __(
                    'If there is an account associated with %1 you will receive an email with a link to reset your password.',
                    $this->escaper->escapeHtml($email)
                );
                $this->messageManager->addSuccess($message);
                // Do nothing, we don't want anyone to use this action to determine which email accounts are registered.
            } catch (SecurityViolationException $exception) {
                $success        = false;
                $error          = true;
                $message        = __($exception->getMessage());
                $this->messageManager->addError($message);
            } catch (\Exception $exception) {
                $success        = false;
                $error          = true;
                $message        = __('We\'re unable to send the password reset email.');
                $this->messageManager->addError($message);
            }
        }
        $blockMsg->setMessages( $this->messageManager->getMessages(true) );
        $result     =   [
            'success'   => $success,
            'error'     => $error,
            'message'   => $blockMsg->getGroupedHtml(),
        ];
        $this->getResponse()->setBody($this->jsonHelper->jsonEncode($result));
    }
}
