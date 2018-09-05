<?php
namespace Ziffity\Webforms\Controller\Index;
 
use \Magento\Framework\ObjectManagerInterface;
use \Ziffity\Webforms\Model\CatalogFactory;

 
class CatalogPost extends \Magento\Framework\App\Action\Action
{
    const XML_PATH_EMAIL_RECIPIENT_NAME = 'trans_email/ident_support/name';
    const XML_PATH_EMAIL_RECIPIENT_EMAIL = 'trans_email/ident_support/email';
     
    protected $_inlineTranslation;
    protected $_transportBuilder;
    protected $_scopeConfig;
    protected $_logLoggerInterface;
     
    public function __construct(

        ObjectManagerInterface $objectManager,
        CatalogFactory $feedback,
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
	$data=array('customer_fn'=>$post['customer_fn'],'customer_ln'=>$post['customer_ln'],'customer_addr_one'=>$post['customer_addr_one'],'customer_addr_two'=>$post['customer_addr_two'],'customer_city'=>$post['customer_city'],'customer_state'=>$post['customer_state'],'customer_zip'=>$post['customer_zip']);
	$model->setData($data);
	$model->save();        
	$this->_redirect('comments/index/catalog');
        $this->messageManager->addSuccess(__('Thank you for requesting a catalog. You\'ll receive your catalog in 2-3 weeks.'));
         
         
         
    }
}
