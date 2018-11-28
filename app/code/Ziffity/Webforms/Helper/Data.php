<?php

namespace Ziffity\Webforms\Helper;

use Magento\Framework\App\Helper\AbstractHelper;
use Magento\Framework\App\Helper\Context;
use Magento\Store\Model\ScopeInterface;

class Data extends AbstractHelper
{
    const FORM_ARRAY = ['contact', 'coin', 'catalog'];
    const W_ENABLE = 'webforms/general/enable';
    const W_CONTACT_MAIL = 'webforms/general/contact_mail';
    const W_COIN_MAIL = 'webforms/general/coin_mail';
    const W_CATALOG_MAIL = 'webforms/general/catalog_mail';
    const W_TEMPLATE = 'webforms/general/email_template';
    const FORM_TYPE = 'form_type';

    /**
     * @var \Magento\Framework\App\Config\ScopeConfigInterface
     */
    protected $scopeConfig;

    protected $_inlineTranslation;
    protected $_transportBuilder;

    /**
     * @param Context $context
     */
    public function __construct(
        Context $context,
        \Magento\Framework\Translate\Inline\StateInterface $inlineTranslation,
        \Magento\Framework\Mail\Template\TransportBuilder $transportBuilder
    )
    {
        $this->_moduleManager = $context->getModuleManager();
        $this->_logger = $context->getLogger();
        $this->_request = $context->getRequest();
        $this->_urlBuilder = $context->getUrlBuilder();
        $this->_httpHeader = $context->getHttpHeader();
        $this->_eventManager = $context->getEventManager();
        $this->_remoteAddress = $context->getRemoteAddress();
        $this->_cacheConfig = $context->getCacheConfig();
        $this->urlEncoder = $context->getUrlEncoder();
        $this->urlDecoder = $context->getUrlDecoder();
        $this->scopeConfig = $context->getScopeConfig();
        
        $this->_inlineTranslation = $inlineTranslation;
        $this->_transportBuilder = $transportBuilder;
        parent::__construct($context);
    }
    
    /**
     * @return \Magento\Framework\App\Config\ScopeConfigInterface
     */
    public function getConfigValue($path)
    {
        return $this->scopeConfig->getValue($path, ScopeInterface::SCOPE_STORE);
    }

    public function sendMail($data, $store)
    {
        $email = $this->getConfigValue('trans_email/ident_support/email');
        $name  = $this->getConfigValue('trans_email/ident_support/name');
        $sender = ['name' => $name, 'email' => $email];
        if($this->scopeConfig->getValue(SELF::W_ENABLE)){
          $this->sendCustomerMail($sender, $data, $store);
          $this->sendAdminMail($sender, $data, $store);
        }

        $message = __('Thank you for contacting us. A customer service representative will respond to you shortly.');
        if ($data[self::FORM_TYPE] == 'coin') {
            $message = __('Thank you for your interest. One of our specialist will get in touch with you shortly.');
        } elseif ($data[self::FORM_TYPE] == 'catalog') {
            $message = __('Thank you for requesting a catalog. You\'ll receive your catalog in 2-3 weeks.');
        }

        return $message;
    }

    public function mailTo($sender, $to, $template, $options, $var)
    {
        try{
            $this->_inlineTranslation->suspend();
            $transport = $this->_transportBuilder
                ->setTemplateIdentifier($template)
                ->setTemplateOptions($options)
                ->setTemplateVars($var)
                ->setFrom($sender)
                ->addTo($to)
                ->getTransport();
            $transport->sendMessage();
            $this->_inlineTranslation->resume();
        } catch (Exception $ex) {
            $this->_inlineTranslation->resume();
            $this->_logger->debug($ex->getMessage());
        }
    }

    public function sendCustomerMail($sender, $data)
    {
        $template = $this->getConfigValue('contact/email/email_template');
        $options = [
           'area' => 'frontend',
           'store' => $data['store_id']
        ];
        $var = ['name' => $data['cust_name']];
        if(isset($data['cust_email'])){
            $this->mailTo($sender, $data['cust_email'], $template, $options, $var);
        }
    }

    public function sendAdminMail($sender, $data, $store){
        $template = $this->getConfigValue('contact/email/email_template');
        if($this->getConfigValue(SELF::W_TEMPLATE)){
           $template =  $this->getConfigValue(SELF::W_TEMPLATE);
        }
        $options = [
           'area' => 'frontend',
           'store' => $data['store_id'],
        ];

        switch ($data[self::FORM_TYPE]){
            case 'contact':
                $to = $this->getConfigValue(SELF::W_CONTACT_MAIL);
                $sub = 'CSN Contact Request';
            case 'catalog':
                $to = $this->getConfigValue(SELF::W_CATALOG_MAIL);
                $sub = 'CSN Catalog Request';
                break;
            case 'coin':
                $to = $this->getConfigValue(SELF::W_COIN_MAIL);
                $sub = 'CSN Find Your Coin Request';
                break;
            default:
                $to = $sender['email'];
        }

        if(isset($data['cust_email'])){
            $sender = [
                'name' => $data['cust_name'],
                'email' => $data['cust_email']
            ];
        }

        $data['sub'] = $sub;
        $data['store_name'] = $store->getName();
        $data['type'] = isset($data['customer_id']) ? 'Customer' : 'Guest';
        $data['ip'] = $data['customer_ip'];
        $data['date'] = date('M d, Y h:i:s A');
        $data['name'] = $data['cust_name'];
        $data['email'] = isset($data['cust_email']) ? $data['cust_email'] : false;
        $data['phone'] = isset($data['cust_phone']) ? $data['cust_phone'] : false;
        $data['message'] = isset($data['cust_comments']) ? $data['cust_comments'] : false;
        $data['looking_for'] = isset($data['cust_find']) ? $data['cust_find'] : false;
        $data['address'] = isset($data['cust_addr_one']) ? $data['cust_addr_one'] : false;
        $data['city'] = isset($data['cust_city']) ? $data['cust_city'] : false;
        $data['state'] = isset($data['cust_state']) ? $data['cust_state'] : false;
        $data['postal_code'] = isset($data['cust_zip']) ? $data['cust_zip'] : false;
        
        clog('wf', $data, true);
        $this->mailTo($sender, $to, $template, $options, $data);
    }

}
