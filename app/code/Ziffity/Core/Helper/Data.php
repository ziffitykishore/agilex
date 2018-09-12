<?php
/**
 * Core Helper to use all needed methods
 */

namespace Ziffity\Core\Helper;

use Magento\Framework\App\Helper\AbstractHelper;
use Magento\Framework\App\Helper\Context;
use Magento\Framework\HTTP\PhpEnvironment\RemoteAddress;

class Data extends AbstractHelper
{
    const C_SITEKEY = 'mconnect_ajaxlogin/googlecaptcha/sitekey';
    const C_SECRETKEY = 'mconnect_ajaxlogin/googlecaptcha/secretkey';

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
     * Constructor
     * @param Context $context
     * @param RemoteAddress $remoteAddress
     */
    public function __construct(
        Context $context,
        RemoteAddress $remoteAddress   
    ) {
        $this->scopeConfig  = $context->getScopeConfig();
        $this->_remoteAddress = $remoteAddress;
        parent::__construct($context);
    }

    /**
     * @return \Magento\Framework\App\Config\ScopeConfigInterface
     */
    public function getScopeConfig($path)
    {
        return $this->scopeConfig->getValue($path);
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
}