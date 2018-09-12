<?php
namespace Ziffity\Webforms\Controller\Index;

use \Ziffity\Webforms\Model\DataFactory;
use Magento\Store\Model\ScopeInterface;
use \Magento\Framework\Controller\ResultFactory;

class Post extends \Magento\Framework\App\Action\Action
{
    const XML_PATH_EMAIL_RECIPIENT_NAME = 'trans_email/ident_support/name';
    const XML_PATH_EMAIL_RECIPIENT_EMAIL = 'trans_email/ident_support/email';
     
    protected $_inlineTranslation;
    protected $_transportBuilder;
    protected $scopeConfig;
    protected $_logLoggerInterface;
    protected $request;
     
    public function __construct(
        DataFactory $feedback,
        \Magento\Framework\App\Request\Http $request,            
        \Magento\Framework\App\Action\Context $context,
        \Magento\Framework\Translate\Inline\StateInterface $inlineTranslation,
        \Magento\Framework\Mail\Template\TransportBuilder $transportBuilder,
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig,
        \Psr\Log\LoggerInterface $loggerInterface
        )
    {
        $this->feed = $feedback;
        $this->request = $request;
        $this->_inlineTranslation = $inlineTranslation;
        $this->_transportBuilder = $transportBuilder;
        $this->scopeConfig = $scopeConfig;
        $this->_logLoggerInterface = $loggerInterface;
        $this->messageManager = $context->getMessageManager();    
        parent::__construct($context);
    }
     
    public function execute()
    {
    
        $email = $this->scopeConfig->getValue('trans_email/ident_support/email',ScopeInterface::SCOPE_STORE);
        $name  = $this->scopeConfig->getValue('trans_email/ident_support/name',ScopeInterface::SCOPE_STORE);
        $resultRedirect = $this->resultFactory->create(ResultFactory::TYPE_REDIRECT);
        $post = $this->getRequest()->getPost();
	$model = $this->feed->create();
        if($post['form_type']=='contact'){
            $data=array('cust_name'=>$post['cust_name'],'cust_email'=>$post['cust_email'],'cust_phone'=>$post['cust_phone'],'cust_comments'=>$post['cust_comments'],'form_type'=>$post['form_type']);
        }else if($post['form_type']=='coin'){
            $data=array('cust_name'=>$post['cust_name'],'cust_email'=>$post['cust_email'],'cust_phone'=>$post['cust_phone'],'cust_find'=>$post['cust_find'],'form_type'=>$post['form_type']);            
        }else if($post['form_type']=='catalog'){
            $data=array('cust_name'=>$post['cust_name'],'cust_addr_one'=>$post['cust_addr_one'],'cust_addr_two'=>$post['cust_addr_two'],'cust_city'=>$post['cust_city'],'cust_state'=>$post['cust_state'],'cust_zip'=>$post['cust_zip'],'form_type'=>$post['form_type']);            
        }
	$model->setData($data);
	$model->save();        
                
                        try
                        {
                           if($post['form_type']=='contact'||$post['form_type']=='coin'){
                                $this->_inlineTranslation->suspend();
                                $storeScope = \Magento\Store\Model\ScopeInterface::SCOPE_STORE;
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
                                    ]
                                    )
                                    ->setTemplateVars([
                                        'name'  => $post['cust_name'],
                                        'fname' => $post['cust_fn']                ])
                                    ->setFrom($sender)
                                    ->addTo($post['cust_email'],$post['cust_name'])
                                    ->getTransport();
                                    $transport->sendMessage();
                                    $this->_inlineTranslation->resume();
                                    if($post['form_type']=='contact'){
                                        $this->messageManager->addSuccess('Thank you for contacting us. A customer service representative will respond to you shortly.');
                                        $resultRedirect->setUrl($this->_redirect->getRefererUrl());
                                        return $resultRedirect;
                                    }else if($post['form_type']=='coin'){
                                        $this->messageManager->addSuccess('Thank you for your interest. One of our specialist will get in touch with you shortly.');                                    
                                        $resultRedirect->setUrl($this->_redirect->getRefererUrl());
                                        return $resultRedirect;
                                    }
                           }

                        } catch(\Exception $e){
                            $this->_inlineTranslation->resume();
                            $this->messageManager->addError('Something Went Wrong'.$e->getMessage());
                            $this->_logLoggerInterface->debug($e->getMessage());
                            $resultRedirect->setUrl($this->_redirect->getRefererUrl());
                            return $resultRedirect;

                        }
                   
                if($post['form_type']=='catalog'){
                    $this->messageManager->addSuccess('Thank you for requesting a catalog. You\'ll receive your catalog in 2-3 weeks.');                   
                    $resultRedirect->setUrl($this->_redirect->getRefererUrl());
                    return $resultRedirect;
                }
        
        
    }
}
