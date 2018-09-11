<?php
/**
 * Core Helper to use all needed methods
 */

namespace Ziffity\Core\Helper;

use Magento\Framework\App\Helper\AbstractHelper;
use Magento\Framework\App\Helper\Context;

class Data extends AbstractHelper
{

    /**
     * @var \Magento\Framework\App\Config\ScopeConfigInterface
     */
    protected $scopeConfig;

    /**
     * Constructor
     * @param Context $context
     */
    public function __construct(
        Context $context
    ) {
        $this->scopeConfig  = $context->getScopeConfig();
        parent::__construct($context);
    }

    /**
     * @return \Magento\Framework\App\Config\ScopeConfigInterface
     */
    public function getScopeConfig($path)
    {
        return $this->scopeConfig->getValue($path);
    }
}