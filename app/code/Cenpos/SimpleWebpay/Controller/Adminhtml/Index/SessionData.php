<?php 
namespace Cenpos\SimpleWebpay\Controller\Adminhtml\Index;

use Magento\Backend\App\Action;
use Magento\Backend\App\Action\Context;

class SessionData extends Action
{
	/**
	 * @var \Cenpos\SimpleWebpay\Model\Ui\ConfigProvider
	 */
    protected $_paymentMethod;

	/**
	 * @var \Magento\Customer\Api\CustomerRepositoryInterface
	 */
    protected $_customerRepository;
    
    /**
    * @var \Magento\Backend\Model\Session\Quote
    */
   protected $_quoteSession;

	/**
	 * SessionData constructor.
	 * @param Context $context
	 * @param \Cenpos\SimpleWebpay\Model\Ui\ConfigProvider $paymentMethod
	 * @param \Magento\Customer\Api\CustomerRepositoryInterface $customerRepository
	 */
    public function __construct(
        Context $context,
        \Cenpos\SimpleWebpay\Model\Ui\ConfigProvider $paymentMethod,
        \Magento\Customer\Api\CustomerRepositoryInterface $customerRepository,
        \Magento\Backend\Model\Session\Quote $quoteSession
    ) {
        parent::__construct($context);
        $this->_paymentMethod = $paymentMethod;
        $this->_customerRepository = $customerRepository;
        $this->_quoteSession = $quoteSession;
    }

	/**
	 * @return \Magento\Framework\App\ResponseInterface|\Magento\Framework\Controller\ResultInterface|void
	 */
	public function execute()
    {   

        
        $ResponseSave = new \stdClass();
        try {

            $dataCardSet = $this->_quoteSession->getRecurringData();

            if(isset($dataCardSet) && !empty($dataCardSet["webpayrecurringsaletokenid"])){
                
                $ResponseSave->Message = "Data was created before";
                $ResponseSave->Result = 450;
                $ResponseSave->Data = $dataCardSet;
            }else{
                $ip = $_SERVER["REMOTE_ADDR"];
                if (empty($this->_paymentMethod->getConfigData('url'))) {
                    throw new \Exception("The url credit card must be configured");
                }

                $urlswp = $this->_paymentMethod->getConfigData('url');
                $endurlswp = substr($urlswp, strlen($urlswp) - 1);
                $urlswp = $endurlswp == "/" ? $urlswp : $urlswp ."/";
                
                $ch = curl_init($this->_paymentMethod->getConfigData('url')."?app=genericcontroller&action=siteVerify");
                curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
                curl_setopt ($ch, CURLOPT_POST, 1);

                $postSend = "secretkey=" . $this->_paymentMethod->getConfigData('secretkey');
                $postSend .= "&merchant=" . $this->_paymentMethod->getConfigData('merchantid');
                $postSend .= "&address=". $this->getRequest()->getParam('address');
                $postSend .= "&isrecaptcha=".(($this->_paymentMethod->getConfigData('isrecaptcha') === "1")? "true" : "false");
                $postSend .= "&recaptchaversion=".$this->_paymentMethod->getConfigData('recaptchaversion');
                $postSend .= "&zipcode=".$this->getRequest()->getParam('zipcode');

                $customerEmail = $this->getRequest()->getParam('email');

                if (!empty($customerEmail)) {
                    $customer = $this->_customerRepository->get($customerEmail);
                    if($customer) {
                        $postSend .= "&customercode=" . $customer->getId();
                        $postSend .= "&email=" . $customerEmail;
                    }
                }

                $postSend .= "&ip=$ip";
                curl_setopt ($ch, CURLOPT_POSTFIELDS, $postSend);

                curl_setopt($ch,CURLOPT_RETURNTRANSFER, true);

                $ResponseSave = curl_exec($ch);

                $error = curl_error($ch);
                curl_close($ch);
                if(!empty($error))  {
                    throw new \Exception($error);
                }
            
                $ResponseSave = json_decode($ResponseSave);

                if($ResponseSave->Result != 0) {
                    throw new \Exception($ResponseSave->Message);
                }
            }
        } catch (\Exception $ex) {
            if (!isset($ResponseSave)){
                $ResponseSave = new \stdClass();
            }
            $ResponseSave->Message = $ex->getMessage();
            $ResponseSave->Result = -1;
        }
        
        echo json_encode($ResponseSave);
    }
}