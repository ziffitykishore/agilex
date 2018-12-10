<?php
namespace Mconnect\Ajaxlogin\Controller\Forgetpassword; 
/*path of the Controller*/
use Magento\Customer\Model\Session;
use Magento\Framework\App\Action\Context;
use Magento\Framework\View\Result\PageFactory;

class ForgetPassword extends \Magento\Framework\App\Action\Action
/*class name is file name*/
{

    public function execute()
    {
        echo 's';
        die();
        /** @var \Magento\Framework\View\Result\Page $resultPage */
//        $resultPage = $this->resultPageFactory->create();
//        $resultPage->getLayout()->getBlock('forgotPassword')->setEmailValue($this->session->getForgottenEmail());
//
//        $this->session->unsForgottenEmail();
//
//        return $resultPage;
    }

}
