<?php
namespace Ziffity\Webforms\Controller\Index;

use \Ziffity\Webforms\Model\DataFactory;
use Magento\Store\Model\ScopeInterface;
use \Magento\Framework\Controller\ResultFactory;
use Magento\Framework\HTTP\PhpEnvironment\RemoteAddress;
use Magento\Store\Model\StoreManagerInterface;

class Post extends \Magento\Framework\App\Action\Action
{
    const XML_PATH_EMAIL_RECIPIENT_NAME = 'trans_email/ident_support/name';
    const XML_PATH_EMAIL_RECIPIENT_EMAIL = 'trans_email/ident_support/email';
    const FORM_TYPE = 'form_type';

    protected $_inlineTranslation;
    protected $_transportBuilder;
    protected $scopeConfig;
    protected $_logLoggerInterface;
    protected $request;
    protected $_remoteAddress;
    protected $_customerSession;
    protected $_storeManager;

    public function __construct(
        DataFactory $feedback,
        \Magento\Framework\App\Request\Http $request,            
        \Magento\Framework\App\Action\Context $context,
        \Magento\Framework\Translate\Inline\StateInterface $inlineTranslation,
        \Magento\Framework\Mail\Template\TransportBuilder $transportBuilder,
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig,
        \Psr\Log\LoggerInterface $loggerInterface,
        RemoteAddress $remoteAddress,
	StoreManagerInterface $storeManager,            
       \Magento\Customer\Model\Session $customerSession

    )
    {
        $this->feed = $feedback;
        $this->request = $request;
        $this->_inlineTranslation = $inlineTranslation;
        $this->_transportBuilder = $transportBuilder;
        $this->scopeConfig = $scopeConfig;
        $this->_logLoggerInterface = $loggerInterface;
        $this->messageManager = $context->getMessageManager();
        $this->_remoteAddress = $remoteAddress;
        $this->_customerSession = $customerSession;
	$this->_storeManager = $storeManager;        
        parent::__construct($context);
    }

    public function execute()
    {
        $email = $this->scopeConfig->getValue('trans_email/ident_support/email',ScopeInterface::SCOPE_STORE);
        $name  = $this->scopeConfig->getValue('trans_email/ident_support/name',ScopeInterface::SCOPE_STORE);
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

            if ($post[self::FORM_TYPE] == 'contact' || $post[self::FORM_TYPE] == 'coin') {
                $this->_inlineTranslation->suspend();
                $sender = [
                    'name' => $name,
                    'email' => $email
                ];
                $transport = $this->_transportBuilder
                    ->setTemplateIdentifier('webforms_thanks_template')
                    ->setTemplateOptions(
                        [
                            'area' => 'frontend',
                            'store' => \Magento\Store\Model\Store::DEFAULT_STORE_ID,
                        ])
                    ->setTemplateVars(
                        [
                            'name' => $post['cust_name']
                        ])
                    ->setFrom($sender)
                    ->addTo($post['cust_email'],$post['cust_name'])
                    ->getTransport();
                    $transport->sendMessage();
                    $this->_inlineTranslation->resume();
            }
            $message = __('Thank you for contacting us. A customer service representative will respond to you shortly.');
            if ($post[self::FORM_TYPE] == 'coin') {
                $message = __('Thank you for your interest. One of our specialist will get in touch with you shortly.');
            } elseif ($post[self::FORM_TYPE] == 'catalog') {
                $message = __('Thank you for requesting a catalog. You\'ll receive your catalog in 2-3 weeks.');
            }
            $this->messageManager->addSuccess($message);
            $resultRedirect->setUrl($this->_redirect->getRefererUrl());
            return $resultRedirect;
        } catch(\Exception $e){
            $this->_inlineTranslation->resume();
            $this->messageManager->addError('Something Went Wrong'.$e->getMessage());
            $this->_logLoggerInterface->debug($e->getMessage());
            $resultRedirect->setUrl($this->_redirect->getRefererUrl());
            return $resultRedirect;
        }
    }
    
}
