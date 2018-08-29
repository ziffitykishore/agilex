<?php
namespace Ziffity\Reviewnotification\Observer;
use \Magento\Framework\Event\ObserverInterface;
use \Magento\Framework\Event\Observer;

class Productreview implements ObserverInterface
{
    const CSN_SENDER_EMAIL = "webmaster@shopcsntv.com";
    
    const CSN_SENDER_NAME = "Shopcsntv";
    
    const CSN_RECEIVER_EMAIL = "trans_email/ident_review/email";
    
    const REVIEW_TEMPLATE_ID = "review_notification_template";
    
    const REVIEW_URL = "review/product/edit/id/";
    protected $transportBuilder;
    protected $inlineTranslation;
    protected $scopeConfig;
    protected $storeManager;
    protected $request;
    protected $url;
    protected $productFactory;
    protected $logger;
    public function __construct(
        \Magento\Framework\Mail\Template\TransportBuilder $transportBuilder,
        \Magento\Framework\Translate\Inline\StateInterface $inlineTranslation,
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Magento\Framework\App\RequestInterface $request,
        \Magento\Backend\Model\UrlInterface $url, 
        \Magento\Catalog\Model\ProductFactory $productFactory,
        \Psr\Log\LoggerInterface $logger
    )
    {
        $this->_transportBuilder = $transportBuilder;
        $this->_scopeConfig = $scopeConfig;
        $this->_storeManager = $storeManager;
        $this->_request = $request;
        $this->_url = $url;
        $this->_productFactory = $productFactory;
        $this->_logger = $logger;
    }

    public function execute(Observer $observer)
    {
        try {
            $dataObject = $observer->getEvent()->getDataObject();
            $storeScope = \Magento\Store\Model\ScopeInterface::SCOPE_STORE;
            $store = $this->_storeManager->getStore()->getId();
            $postData = $this->_request->getParams();
            $productId = $postData['id'];
            $product = $this->_productFactory->create()->load($productId);
            $url = $this->_url->getUrl(self::REVIEW_URL, ['_current' => true, 'id' => $dataObject->getReviewId()]);
            $templateId = self::REVIEW_TEMPLATE_ID;
            $templateOptions = array('area' => \Magento\Framework\App\Area::AREA_FRONTEND,
                'store' => 1);
            $toInfo = $this->_scopeConfig->getValue(self::CSN_RECEIVER_EMAIL,
                $storeScope);
            $sender = [
                'email' => self::CSN_SENDER_EMAIL,
                'name' => self::CSN_SENDER_NAME
            ];

            $transport = $this->_transportBuilder
                    ->setTemplateIdentifier($templateId)
                    ->setTemplateOptions($templateOptions)
                    ->setTemplateVars([
                        'product_name'  => $product->getName(),
                        'customer_name' => $postData['nickname'],
                        'review_title'  => $postData['title'],
                        'review_detail' => $postData['detail'],
                        'review_url'    => $url
                        ])
                    ->setFrom($sender)
                    ->addTo($toInfo)
                    ->getTransport();
            $transport->sendMessage();
        } catch (Exception $ex) {
            $this->_logger->debug($ex->getMessage());
        }
    }

}
