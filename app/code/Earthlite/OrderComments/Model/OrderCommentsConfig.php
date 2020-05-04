<?php
declare(strict_types = 1);
namespace Earthlite\OrderComments\Model;

use \Magento\Checkout\Model\ConfigProviderInterface;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Store\Model\ScopeInterface;

/**
 * class OrderCommentsConfig
 */
class OrderCommentsConfig implements ConfigProviderInterface 
{
    const XML_PATH_MODULE_STATUS = 'earthlite_checkout/ordercomments/enable';
    const XML_PATH_ORDER_COMMENTS_TITLE = 'earthlite_checkout/ordercomments/title';
            
    /**
     *
     * @var ScopeConfigInterface 
     */
    protected $scopeConfigInterface;
    
    /**
     * 
     * @param ScopeConfigInterface $scopeConfigInterface
     */
    public function __construct(
        ScopeConfigInterface $scopeConfigInterface
    ) {
        $this->scopeConfigInterface = $scopeConfigInterface;
    }
    
    /**
     * 
     * @return array
     */
    public function getConfig():array
    {
        $orderComments['order_comments_status'] = (int)$this->getModuleStatus();
        $orderComments['order_comments_title'] = $this->getTitle();
        return $orderComments;
    }
    
    /**
     * 
     * @return bool
     */
    protected function getModuleStatus()
    {
        return $this->scopeConfigInterface->getValue(self::XML_PATH_MODULE_STATUS, ScopeInterface::SCOPE_WEBSITE);
    }
    
    /**
     * 
     * @return string
     */
    protected function getTitle()
    {
        return $this->scopeConfigInterface->getValue(self::XML_PATH_ORDER_COMMENTS_TITLE, ScopeInterface::SCOPE_STORE);
    }

}
