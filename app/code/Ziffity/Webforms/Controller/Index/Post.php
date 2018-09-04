<?php
namespace Ziffity\Webforms\Controller\Index;
 
use \Magento\Framework\ObjectManagerInterface;
use \Ziffity\Webforms\Model\DataFactory;

 
class Post extends \Magento\Framework\App\Action\Action
{
    const XML_PATH_EMAIL_RECIPIENT_NAME = 'trans_email/ident_support/name';
    const XML_PATH_EMAIL_RECIPIENT_EMAIL = 'trans_email/ident_support/email';
     
    protected $_inlineTranslation;
    protected $_transportBuilder;
    protected $_scopeConfig;
    protected $_logLoggerInterface;
     
    public function __construct(

        ObjectManagerInterface $objectManager,
        DataFactory $feedback,
        \Magento\Framework\App\Action\Context $context,
        \Magento\Framework\Translate\Inline\StateInterface $inlineTranslation,
        \Magento\Framework\Mail\Template\TransportBuilder $transportBuilder,
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig,
        \Psr\Log\LoggerInterface $loggerInterface,
        array $data = []
         
        )
    {

        $this->feed = $feedback;
        $this->_objectManager = $objectManager;

        $this->_inlineTranslation = $inlineTranslation;
        $this->_transportBuilder = $transportBuilder;
        $this->_scopeConfig = $scopeConfig;
        $this->_logLoggerInterface = $loggerInterface;
        $this->messageManager = $context->getMessageManager();
         
         
        parent::__construct($context);
         
         
    }
     
    public function execute()
    {
        $post = $this->getRequest()->getPost();

	$model = $this->feed->create();
	$data=array('customer_name'=>$post['customer_name'],'customer_email'=>$post['customer_email'],'customer_phone'=>$post['customer_phone'],'customer_comments'=>$post['customer_comments']);
	$model->setData($data);
	$model->save();        
	//$this->_redirect('cms/index/index');
        //$this->messageManager->addSuccess(__('Your Feedback Has Been Submitted Successfully.'));


        try
        {
            // Send Mail
            $this->_inlineTranslation->suspend();
            $storeScope = \Magento\Store\Model\ScopeInterface::SCOPE_STORE;
             
            $sender = [
                'name' => 'Owner',
                'email' => 'ajithkumarm107@gmail.com'
            ];
             
            $sentToEmail = $this->_scopeConfig ->getValue('trans_email/ident_general/email',\Magento\Store\Model\ScopeInterface::SCOPE_STORE);
             
            $sentToName = $this->_scopeConfig ->getValue('trans_email/ident_general/name',\Magento\Store\Model\ScopeInterface::SCOPE_STORE);
             
             
    


            $transport = $this->_transportBuilder
            ->setTemplateIdentifier('comments_thanks_template')
            ->setTemplateOptions(
                [
                    'area' => 'frontend',
                    'store' => \Magento\Store\Model\Store::DEFAULT_STORE_ID,
                ]
                )
                ->setTemplateVars([
                    'name'  => $post['customer_name'],
	            'email'  => $post['customer_email'],		
                    'phone'  => $post['customer_phone'],
		    'comments'=> $post['customer_comments']
                ])
                ->setFrom($sender)
                ->addTo($post['customer_email'],$post['customer_name'])
                //->addBcc($sentToEmail,$sentToName)
                ->getTransport();
                 
                $transport->sendMessage();


                 
                $this->_inlineTranslation->resume();
                $this->messageManager->addSuccess('Email sent successfully');
                $this->_redirect('cms/index/index');
                 
        } catch(\Exception $e){
            $this->messageManager->addError($e->getMessage());
            $this->_logLoggerInterface->debug($e->getMessage());
            exit;
        }
         
         
         
    }
}
