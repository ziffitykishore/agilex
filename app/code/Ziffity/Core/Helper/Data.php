<?php
/**
 * Core Helper to use all needed methods
 */

namespace Ziffity\Core\Helper;

use Magento\Framework\App\Helper\AbstractHelper;
use Magento\Framework\App\Helper\Context;
use Magento\Framework\HTTP\PhpEnvironment\RemoteAddress;
use Magento\Store\Model\ScopeInterface;
use Magento\Customer\Model\Context as CustomerContext;
use Magento\Framework\App\Http\Context as HttpContext;

class Data extends AbstractHelper
{
    const C_SITEKEY = 'mconnect_ajaxlogin/googlecaptcha/sitekey';
    const C_SECRETKEY = 'mconnect_ajaxlogin/googlecaptcha/secretkey';
    const OUT_OF_STOCK_MODULE_KEY = 'cataloginventory/stock_status_label/enable_out_of_stock_status_label';

    /**
     * @var \Magento\Framework\App\Config\ScopeConfigInterface
     */
    protected $scopeConfig;

    /**
     *
     * @var \Magento\Framework\HTTP\PhpEnvironment\RemoteAddress 
     */
    protected $_remoteAddress;

    /**
    * @var \Magento\Framework\Registry
    */
    protected $_registry;

    /**
    * @var HttpContext
    */
    protected $httpContext;

    /**
     * Constructor
     * @param Context $context
     * @param RemoteAddress $remoteAddress
     */
    public function __construct(
        Context $context,
        RemoteAddress $remoteAddress ,
        \Magento\Framework\Registry $registry,
        \Magento\Framework\Module\Manager $moduleManager,
        HttpContext $httpContext
    ) {
        $this->scopeConfig  = $context->getScopeConfig();
        $this->_remoteAddress = $remoteAddress;
        $this->_registry = $registry;
        $this->_moduleManager = $moduleManager;
        $this->httpContext = $httpContext;
        parent::__construct($context);
    }

    /**
     * @return \Magento\Framework\App\Config\ScopeConfigInterface
     */
    public function getScopeConfig($path)
    {
        return $this->scopeConfig->getValue($path, ScopeInterface::SCOPE_STORE);
    }

    /**
     * 
     * @param type $data
     * @param type $useCaptcha
     * @return boolean
     */
    public function validateGCaptcha($data, $useCaptcha = 1)
    {
        $captchaflag = true;

        try {
            $googleSecretKey = $this->getScopeConfig(self::C_SECRETKEY);
            if ($useCaptcha == 1 && $googleSecretKey) {
                if (isset($data['g-recaptcha-response'])) {
                    $gUrl = "https://www.google.com/recaptcha/api/siteverify?";
                    $resCaptcha = $data['g-recaptcha-response'];
                    $remoteIp = $this->_remoteAddress->getRemoteAddress();
                    $gParams = "secret=" . $googleSecretKey . "&response=" . $resCaptcha . "&remoteip=" . $remoteIp . "";
                    $googleResponse = file_get_contents($gUrl . $gParams);
                    $response = json_decode($googleResponse, true);
                    if((bool)$response['success'] == true){
                        $captchaflag = false;
                    }
                }
            }
        } catch (Exception $ex) {
            $this->logger('captchaLog', 'ERROR: ==> '.$ex->getMessage(), true);
        }

        return $captchaflag;
    }

    public function logger($filename = 'clog', $message = null, $enable = false)
    {
        if ($enable) {
            $writer = new \Zend\Log\Writer\Stream(BP . '/var/log/' . $filename . '.log');
            $logger = new \Zend\Log\Logger();
            $logger->addWriter($writer);
            $logger->info(print_r($message, true));
        }
    }
    
    public function setGCaptcha()
    {
        $this->_registry->register('gcaptcha', true);
    }

    /**
     * Retrieving captcha registered
     * @return string
     */
    public function getGCaptcha()
    {
        return $this->_registry->registry('gcaptcha');
    }
    
    public function getRegister(){
        return $this->_registry;
    }
    
    /**
     * Check for the status of Out of Stock and show the label to the user if it listing page
     * 
     * @return int
     */
    public function getOutOfStockStatus($page = '') {

       $showOutOfStockStatus = 0;
       if($page) {
            $outOfStockModuleStatus = $this->_moduleManager->isEnabled('Ziffity_StockStatus');
            if ($outOfStockModuleStatus) {
                $outOfStockStatus = $this->getScopeConfig(self::OUT_OF_STOCK_MODULE_KEY);
                $statusArray = explode(",", $outOfStockStatus);
                $showOutOfStockStatus = in_array($page, $statusArray) ? 1: 0;                    
            }
       }
       return $showOutOfStockStatus;
    }
    
    /**
     * To check customer logged in
     * 
     * @return boolean
     */
    public function getIsCustomerLoggedIn()
    {
        return $this->httpContext->getValue(customerContext::CONTEXT_AUTH);
    }
}
