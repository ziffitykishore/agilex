<?php
/**
 * Captcha verification
 */
namespace Ziffity\Core\Observer;

use Magento\Framework\App\Action\Action;
use Magento\Framework\App\ActionFlag;
use Magento\Framework\App\Request\Http;
use Magento\Framework\App\Response\RedirectInterface;
use Magento\Framework\App\ResponseInterface;
use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;
use Magento\Framework\Message\ManagerInterface;
use Ziffity\Core\Helper\Data as HelperData;
use Magento\Framework\Controller\Result\JsonFactory;
        
/**
 * Class Captcha
 * @package Ziffity\Core\Observer
 */
class Captcha implements ObserverInterface
{
    
    /**
     * @var \Magento\Framework\App\ResponseInterface
     */
    protected $_responseInterface;

    /**
     * @var \Ziffity\Core\Helper\Data
     */
    protected $_helperData;

    /**
     * @var \Magento\Framework\App\Request\Http
     */
    protected $_request;

    /**
     * @var ActionFlag
     */
    private $_actionFlag;

    /**
     * @var \Magento\Framework\Message\ManagerInterface
     */
    protected $messageManager;

    /**
     * @var \Magento\Framework\App\Response\RedirectInterface
     */
    protected $redirect;

    /**
     * @var \Magento\Framework\Controller\Result\JsonFactory 
     */
    protected $resultJsonFactory;

    /**
     * Captcha constructor.
     * @param \Ziffity\Core\Helper\Data $helperData
     * @param \Magento\Framework\App\Request\Http $request
     * @param \Magento\Framework\Message\ManagerInterface $messageManager
     * @param \Magento\Framework\App\ActionFlag $actionFlag
     * @param \Magento\Framework\App\ResponseInterface $responseInterface
     * @param \Magento\Framework\App\Response\RedirectInterface $redirect
     * @param \Magento\Framework\Controller\Result\JsonFactory $resultJson
     */
    public function __construct(
        HelperData $helperData,
        Http $request,
        ManagerInterface $messageManager,
        ActionFlag $actionFlag,
        ResponseInterface $responseInterface,
        RedirectInterface $redirect,
        JsonFactory $resultJson
    )
    {
        $this->_helperData = $helperData;
        $this->_request = $request;
        $this->messageManager = $messageManager;
        $this->_actionFlag = $actionFlag;
        $this->_responseInterface = $responseInterface;
        $this->redirect = $redirect;
        $this->resultJsonFactory = $resultJson;
    }

    /**
     * @param Observer $observer
     */
    public function execute(Observer $observer)
    {
        $helper = $this->_helperData;
        $googleSecretKey = $helper->getScopeConfig($helper::C_SECRETKEY);
        $params = $this->_request->getParams();
        if ($googleSecretKey && isset($params['g-captcha'])) {
            if (isset($params['g-recaptcha-response'])) {
                $capRes = $this->_helperData->validateGCaptcha($params);
                $helper->logger('captchaLog', $capRes, true);
                if ($capRes) {
                    $this->redirectUrlError(__('Missing required parameters recaptcha!'));
                }
            }else{
                $this->redirectUrlError(__('Missing required parameters recaptcha!'));
            }
        }
    }

    /**
     * @param $message
     */
    public function redirectUrlError($message)
    {
        $params = $this->_request->getParams();
        if (isset($params['isAjax']) && isset($params['g-captcha'])) {
            $errHtml = '<div class="messages"><div class="message message-error error">'
                    . '<div data-ui-id="messages-message-error">'. $message
                    . '</div></div></div>';
            $response = [
                'errors' => true,
                'message' => $errHtml,
            ];
            echo json_encode($response);
            exit;
        }
        $this->messageManager->addErrorMessage($message);
        $this->_actionFlag->set('', Action::FLAG_NO_DISPATCH, true);
        $this->_responseInterface->setRedirect($this->redirect->getRefererUrl());
    }
}
