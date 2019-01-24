<?php
/**
 * To Load Ticker iframe from separate controller
 */
namespace Ziffity\Core\Controller\Index;

use Magento\Framework\App\Action\Context;
use Magento\Framework\Controller\Result\JsonFactory;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Store\Model\ScopeInterface;

class Iframe extends \Magento\Framework\App\Action\Action
{
    /**
     * @var \Magento\Framework\Controller\Result\JsonFactory 
     */
    protected $resultJsonFactory;

    /**
     * @var \Magento\Framework\App\Config\ScopeConfigInterface 
     */
    protected $scopeConfig;
    
    /**
     * @param Context $context
     * @param JsonFactory $resultJsonFactory
     * @param ScopeConfigInterface $scopeConfig
     */
    public function __construct (
        Context $context,
        JsonFactory $resultJsonFactory,
        ScopeConfigInterface $scopeConfig
    ) {
        $this->resultJsonFactory = $resultJsonFactory;
        $this->scopeConfig = $scopeConfig;
        parent::__construct($context);
    }

    /**
     * Execute method
     * 
     * @return \Magento\Framework\Controller\Result\JsonFactory
     */
    public function execute() {
        $html = $this->scopeConfig->getValue(
            "settings/general/ticker_iframe",
            ScopeInterface::SCOPE_STORES
        );
        $result = $this->resultJsonFactory->create();
        return $result->setData(['html' => $html]);
    }
}
