<?php

namespace Ziffity\AjaxLogin\Controller\Customer\Ajax;

use Magento\Customer\Api\AccountManagementInterface;
use Magento\Customer\Model\AccountManagement;
use Magento\Customer\Model\Session;
use Magento\Framework\App\Action\Context;
use Magento\Framework\Escaper;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\Exception\SecurityViolationException;
use Magento\Framework\Json\Helper\Data as JsonHelper;

/**
 * Description of ResetPassword
 *
 * @author linux
 */
class ResetPassword extends \Magento\Framework\App\Action\Action{
   
    /** @var Magento\Customer\Api\AccountManagementInterface */
    protected $customerAccountManagement;

    /** @var Magento\Framework\Escaper */
    protected $escaper;

    /**
     * @var Magento\Customer\Model\Session
     */
    protected $session;
    
    /**
     *
     * @var Magento\Framework\Json\Helper\Data
     */
    protected $jsonHelper;

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
        JsonHelper $jsonHelper
    ) {
        $this->session = $customerSession;
        $this->customerAccountManagement = $customerAccountManagement;
        $this->escaper = $escaper;
        $this->jsonHelper = $jsonHelper;
        parent::__construct($context);
    }

    /**
     * Forgot customer password action
     *
     * @return \Magento\Framework\Controller\Result\Redirect
     */
    public function execute()
    {
        $email = $this->getRequest()->getParam('email');
        if ($email) {
            if (!\Zend_Validate::is($email, 'EmailAddress')) {
                $this->session->setForgottenEmail($email);
                $error        = true;
                $success      = false;
                $message      = __('Please correct the email address.');
            }
            try {
                $this->customerAccountManagement->initiatePasswordReset(
                    $email,
                    AccountManagement::EMAIL_RESET
                );
                $error        = false;
                $success      = true;
                $message      = __(
                    'If there is an account associated with %1 you will receive an email with a link to reset your password.',
                    $this->escaper->escapeHtml($email)
                );
            } catch (NoSuchEntityException $e) {
                $success        = false;
                $error          = true;
                $message        = __(
                    'There is no account associated with your email address',
                    $this->escaper->escapeHtml($email)
                );
            } catch (SecurityViolationException $exception) {
                $success        = false;
                $error          = true;
                $message        = __($exception->getMessage());
            } catch (\Exception $exception) {
                $success        = false;
                $error          = true;
                $message        = __('We\'re unable to send the password reset email.');
            }
        }
        $result     =   [
            'success'   => $success,
            'error'     => $error,
            'message'   => $message
        ];
        $this->getResponse()->setBody($this->jsonHelper->jsonEncode($result));
    }
}
