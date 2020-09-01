<?php 
namespace Cenpos\SimpleWebpay\Controller\Index;

class SessionData extends \Magento\Framework\App\Action\Action
{
    protected $_customerSession;
    protected $resultPageFactory;
    protected $_paymentMethod;
    protected $_checkoutSession;
    protected $checkout;
    protected $cartManagement;
    protected $guestcartManagement;
    protected $orderRepository;
    protected $_scopeConfig;
    
    /**
     * @param \Magento\Framework\App\Action\Context $context
     * @param \Magento\Customer\Model\Session $customerSession
     */
    public function __construct(
        \Magento\Framework\App\Action\Context $context,
        \Magento\Customer\Model\Session $customerSession,
        \Cenpos\SimpleWebpay\Model\Ui\ConfigProvider $paymentMethod,
        \Magento\Checkout\Model\Session $checkoutSession,
        \Magento\Sales\Api\OrderRepositoryInterface $orderRepository,
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig
    ) {
        $this->_customerSession = $customerSession;
        parent::__construct($context);
        $this->_paymentMethod = $paymentMethod;
        $this->_checkoutSession = $checkoutSession;
        $this->orderRepository = $orderRepository;
        $this->_scopeConfig = $scopeConfig;
    }

    public function execute()
    {
        $ResponseSave = new \stdClass();
        try{
            $ip = $_SERVER["REMOTE_ADDR"];
            if($this->_paymentMethod->getConfigData('url') == null || $this->_paymentMethod->getConfigData('url') == "" ) $this->throwMessageCustom("The url credit card must be configured");

            $objectManager = \Magento\Framework\App\ObjectManager::getInstance();

            $cartObj = $objectManager->get('\Magento\Checkout\Model\Cart');

            $billingAddressInfo = $cartObj->getQuote()->getBillingAddress();

            $dataAddress = $billingAddressInfo->getData();

            $Street = "";
            if($dataAddress != null && array_key_exists("street", $dataAddress)){
                if (strpos($dataAddress['street'], "\n") !== FALSE) {
                    $Street = str_replace("\n", " ", $dataAddress['street']);
                }
                else{
                    $Street = $dataAddress['street'];
                }
            }else {
                $Street = "";
            } 

            $urlswp = $this->_paymentMethod->getConfigData('url');
            $endurlswp = substr($urlswp, strlen($urlswp) - 1);
            $urlswp = $endurlswp == "/" ? $urlswp : $urlswp ."/";

            $ch = curl_init($urlswp ."?app=genericcontroller&action=siteVerify");
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
            curl_setopt ($ch, CURLOPT_POST, 1);

            $postSend = "secretkey=".$this->_paymentMethod->getConfigData('secretkey');
            $postSend .= "&merchant=".$this->_paymentMethod->getConfigData('merchantid');
            $postSend .= "&address=".$Street;
            $postSend .= "&isrecaptcha=".(($this->_paymentMethod->getConfigData('isrecaptcha') === "1")? "true" : "false");
            $postSend .= "&recaptchaversion=".$this->_paymentMethod->getConfigData('recaptchaversion');
            if(array_key_exists("postcode", $dataAddress) && isset($dataAddress["postcode"])) $postSend .= "&zipcode=".$dataAddress["postcode"];
            if ($this->_customerSession->isLoggedIn()) {
                $customerData = $this->_customerSession->getCustomer();
                $postSend .= "&customercode=".$customerData->getId();
            }
            if(array_key_exists("email", $dataAddress) && isset($dataAddress["email"])) $postSend .= "&email=".$dataAddress["email"];
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
        } catch (\Exception $ex) {
            $ResponseSave->Message = $ex->getMessage();
            $ResponseSave->Result = -1;
        }
        
        echo json_encode($ResponseSave);
    }
}