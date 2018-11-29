<?php
namespace Ziffity\Webforms\Controller\Index;

use \Ziffity\Webforms\Model\DataFactory;
use \Magento\Framework\Controller\ResultFactory;
use Magento\Framework\HTTP\PhpEnvironment\RemoteAddress;
use Magento\Store\Model\StoreManagerInterface;

class Post extends \Magento\Framework\App\Action\Action
{
    const XML_PATH_EMAIL_RECIPIENT_NAME = 'trans_email/ident_support/name';
    const XML_PATH_EMAIL_RECIPIENT_EMAIL = 'trans_email/ident_support/email';
    const FORM_TYPE = 'form_type';

    protected $scopeConfig;
    protected $_logLoggerInterface;
    protected $request;
    protected $_remoteAddress;
    protected $_customerSession;
    protected $_storeManager;
    protected $_helper;

    public function __construct(
        DataFactory $feedback,
        \Magento\Framework\App\Request\Http $request,            
        \Magento\Framework\App\Action\Context $context,
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig,
        \Psr\Log\LoggerInterface $loggerInterface,
        RemoteAddress $remoteAddress,
	StoreManagerInterface $storeManager,            
       \Magento\Customer\Model\Session $customerSession,
       \Ziffity\Webforms\Helper\Data $helper
    )
    {
        $this->feed = $feedback;
        $this->request = $request;
        $this->scopeConfig = $scopeConfig;
        $this->_logLoggerInterface = $loggerInterface;
        $this->messageManager = $context->getMessageManager();
        $this->_remoteAddress = $remoteAddress;
        $this->_customerSession = $customerSession;
	$this->_storeManager = $storeManager;
        $this->_helper = $helper;
        parent::__construct($context);
    }

    public function execute()
    {
        $resultRedirect = $this->resultFactory->create(ResultFactory::TYPE_REDIRECT);
        $post = $this->getRequest()->getPostValue();
	$model = $this->feed->create();
        try {

            /*customer_ip*/
            $post['customer_ip'] = $this->_remoteAddress->getRemoteAddress();          
            $post['customer_id'] = $this->_customerSession->getCustomer()->getId();
            $post['store_id'] = $this->_storeManager->getStore()->getStoreId();

            $model->setData($post);
            $model->save();
            $message = $this->_helper->sendMail($post, $this->_storeManager->getStore());
            $this->messageManager->addSuccess($message);
            $resultRedirect->setUrl($this->_redirect->getRefererUrl());
            return $resultRedirect;
        } catch(\Exception $e){
            $this->messageManager->addError('Something Went Wrong'.$e->getMessage());
            $this->_logLoggerInterface->debug($e->getMessage());
            $resultRedirect->setUrl($this->_redirect->getRefererUrl());
            return $resultRedirect;
        }
    }
    
}
