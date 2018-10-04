<?php 

namespace Ziffity\Core\Controller\Adminhtml\Post;
  
use Magento\Store\Model\ScopeInterface; 
use \Magento\Framework\Controller\ResultFactory;

class Save extends \Magefan\Blog\Controller\Adminhtml\Post 
{ 

    const XML_PATH_EMAIL_RECIPIENT_NAME = 'trans_email/ident_support/name'; 
    const XML_PATH_EMAIL_RECIPIENT_EMAIL = 'trans_email/ident_support/email'; 
    protected $_transportBuilder;
    protected $_inlineTranslation; 
    protected $scopeConfig; 
    protected $_logLoggerInterface; 
    protected $request; 
    protected $subscriber;

    public function __construct( 
        \Magento\Framework\App\Request\Http $request, 
        \Magento\Backend\App\Action\Context $context, 
        \Magento\Framework\Translate\Inline\StateInterface $inlineTranslation, 
        \Magento\Framework\Mail\Template\TransportBuilder $transportBuilder, 
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig, 
        \Psr\Log\LoggerInterface $loggerInterface,
        \Magento\Newsletter\Model\ResourceModel\Subscriber\CollectionFactory $subscriberFactory,
        \Magento\Framework\App\Request\DataPersistorInterface $data 
    ) { 

        $this->request = $request;
        $this->_inlineTranslation = $inlineTranslation; 
        $this->_transportBuilder = $transportBuilder; 
        $this->scopeConfig = $scopeConfig; 
        $this->_logLoggerInterface = $loggerInterface; 
        $this->messageManager = $context->getMessageManager(); 
        $this->subscriber =$subscriberFactory;
        parent::__construct($context,$data); 
    } 
    
    public function getSubscriberCollection()
    {   
        return $this->subscriber->create();
    }

    protected function _afterSave($model, $request)
    { 
        $data=$this->getRequest()->getPost();
        if(!$data['post_id']){
            
            $subscriberCollection = $this->getSubscriberCollection();
            $subscriberCollection->setOrder('subscriber_id','DESC');
            $subscriberCollection->setPageSize(3)->load();
            $email = $this->scopeConfig->getValue('trans_email/ident_support/email',ScopeInterface::SCOPE_STORE); 
            $name = $this->scopeConfig->getValue('trans_email/ident_support/name',ScopeInterface::SCOPE_STORE); 
            $resultRedirect = $this->resultFactory->create(ResultFactory::TYPE_REDIRECT); 
            foreach ($subscriberCollection as $customer) {
                $recipient=$customer->getEmail();
                $recipientId=$customer->getId();
                $recipientCode=$customer->getCode();
                try { 
                    $this->_inlineTranslation->suspend();
                    $sender = [ 'name' => $name, 'email' => $email ];
                    $transport = $this->_transportBuilder ->setTemplateIdentifier('blog_status_template')
                    ->setTemplateOptions( [ 'area' => 'frontend', 'store' => \Magento\Store\Model\Store::DEFAULT_STORE_ID, ])
                    ->setTemplateVars([
                        'title' => $data['title'],
                        'content' => $data['content'], 
                        'short_content' => $data['short_content'],
                        'url_key' => $data['identifier'],
                        'id' => $recipientId,
                        'code' => $recipientCode,
                        'hide_header' => true
                     ]) 
                    ->setFrom($sender)
                    ->addTo($recipient) 
                    ->getTransport();
                    $transport->sendMessage();
                }
                catch(\Exception $e){
                    $this->_inlineTranslation->resume(); 
                    $this->messageManager->addError('Something Went Wrong'.$e->getMessage());
                    $this->_logLoggerInterface->debug($e->getMessage());
                    $resultRedirect->setUrl($this->_redirect->getRefererUrl());
                    return $resultRedirect; 
                }   
            }
            $this->_inlineTranslation->resume();
            $message = __('Mail Notification Sent');
            $this->messageManager->addSuccess($message);
            $resultRedirect->setUrl($this->_redirect->getRefererUrl());
            return $resultRedirect;
        }
    } 
}