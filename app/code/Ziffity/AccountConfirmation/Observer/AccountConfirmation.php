<?php

namespace Ziffity\AccountConfirmation\Observer;
use  Magento\Store\Model\ScopeInterface;
use Magento\Framework\Event\ObserverInterface;
use Magento\Framework\Event\Observer;

class AccountConfirmation implements ObserverInterface { 
    const XML_PATH_REGISTER_EMAIL_IDENTITY = 'customer/create_account/email_identity';
    const XML_PATH_EMAIL_TEMPLATE= 'ziff/ziffity_accountconfirmation/resend_confirmation_mail';
    const DAYS_DURATION = 'ziff/ziffity_accountconfirmation/days_duration';

    protected $customerFactory;
    protected $date;
    
    /**
     * Store manager
     *
     * @var \Magento\Store\Model\StoreManagerInterface
     */
    private $storeManager;

    /**
     * @var \Magento\Framework\Translate\Inline\StateInterface
     */
    private $inlineTranslation;

    /**
     * @var \Magento\Framework\Mail\Template\TransportBuilder
     */
    protected $transportBuilder;
    
    protected $_messageManager;

    protected $scopeConfig;
    
    protected $logger;
    
    protected  $customerModelFactory;

    /**
     * @param \Magento\Store\Model\StoreManagerInterface $storeManager
     * @param \Magento\Framework\Translate\Inline\StateInterface $inlineTranslation
     * @param \Magento\Framework\Mail\Template\TransportBuilder $transportBuilder
     */

    public function __construct(
        \Magento\Customer\Model\ResourceModel\Customer\CollectionFactory $customerFactory,
        \Magento\Customer\Model\CustomerFactory $customerModelFactory,
        \Magento\Framework\Stdlib\DateTime\DateTime $date,
         \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Magento\Framework\Translate\Inline\StateInterface $inlineTranslation,
        \Magento\Framework\Mail\Template\TransportBuilder $transportBuilder,
        \Magento\Framework\Message\ManagerInterface $messageManager,
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig,
        \Psr\Log\LoggerInterface $logger
    ) {
        $this->customerFactory = $customerFactory;
        $this->customer = $customerModelFactory;
        $this->date = $date;
        $this->storeManager = $storeManager;
        $this->inlineTranslation = $inlineTranslation;
        $this->_transportBuilder = $transportBuilder;
        $this->messageManager = $messageManager;
        $this->scopeConfig = $scopeConfig;
        $this->logger = $logger;
    }
    
    /**
     * Return store configuration value of your template field that which id you set for template
     *
     * @param string $path
     * @param int $storeId
     * @return mixed
     */
    public function getConfigValue($path)
    {
        $storeId = \Magento\Store\Model\Store::DEFAULT_STORE_ID;
        return $this->scopeConfig->getValue(
            $path,
            ScopeInterface::SCOPE_STORE,
            $storeId
        );
    }

    public function getCollection(){
        $collection = $this->customerFactory->create();
        $currentDateTime = $this->date->gmtDate();
        $duration =  $this->getConfigValue(self::DAYS_DURATION);
        $collection->addAttributeToSelect('confirmation');
        $collection->addAttributeToFilter('confirmation', array('neq' => NULL), 'left');
        $collection->addFieldToFilter('created_at', array('lteq' => $currentDateTime));
        $collection->getSelect()->where(new \Zend_Db_Expr('DATEDIFF(CURRENT_TIMESTAMP, `created_at`) < ' . $duration));
        return $collection;
    }    

    public function execute(Observer $observer) {
        $sender = $this->getConfigValue(self::XML_PATH_REGISTER_EMAIL_IDENTITY);
        $templateId = $this->getConfigValue(self::XML_PATH_EMAIL_TEMPLATE);
        $collection = $this->getCollection();
        foreach ($collection as $collection) {
            $receiver = $collection->getEmail();
            $receiverName = $collection->getName();
            $storeId = $collection->getStoreId();
            $customer = $this->customer->create();
            $customerModel = $customer->load($collection->getEntityId());
            $this->inlineTranslation->suspend();      
            try{
             $storeScope = ScopeInterface::SCOPE_STORE; 
             $transport = $this->_transportBuilder
                 ->setTemplateIdentifier($templateId)
                 ->setTemplateOptions(
                 [
                    'area' => \Magento\Framework\App\Area::AREA_FRONTEND, 
                    'store' => \Magento\Store\Model\Store::DEFAULT_STORE_ID,
                 ]
              )
            ->setTemplateVars(['customer' => $customerModel])
            ->setFrom($sender)
            ->addTo($receiver, $storeId)
            ->getTransport();
            $transport->sendMessage();
            $this->logger->alert(__("Mail Sent Successfully!"));
            $this->inlineTranslation->resume();
            } catch (\Exception $e){
                  $this->inlineTranslation->resume();
                  $this->logger->critical($e->getMessage());
            }
        }
        return $this;
    }

}
