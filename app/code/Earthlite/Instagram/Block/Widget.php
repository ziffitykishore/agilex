<?php

namespace Earthlite\Instagram\Block;

use Magento\Framework\View\Element\Template;
use Magento\Widget\Block\BlockInterface;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Store\Model\ScopeInterface;
use Magento\Backend\Block\Template\Context;

/**
 * Block class for the Instagram widget template
 */
class Widget extends Template implements BlockInterface
{
    /**
     * Instagram feed module status configuration.
     */
    const IS_ENABLE = 'instagram/feed/enabled';

    /**
     * Access token configuration for Instagram.
     */
    const ACCESS_TOKEN = 'instagram/feed/accesstoken';

    /**
     * Instagram feed API endpoint configuration.
     */
    const API_ENDPOINT = 'instagram/feed/api_endpoint';

    /**
     * @var string
     */
    protected $_template = 'Earthlite_Instagram::instagram.phtml';

    /**
     * @var ScopeConfigInterface
     */
    private $scopeConfig;

    /**
     * @param Context $context
     * @param ScopeConfigInterface $scopeConfig
     * @param array $data
     */
    public function __construct(
        Context $context,
        ScopeConfigInterface $scopeConfig,
        array $data = []
    ) {
        $this->scopeConfig = $scopeConfig;
        parent::__construct($context, $data);
    }

    public function isEnabled()
    {
        return $this->scopeConfig->getValue(
            self::IS_ENABLE,
            ScopeInterface::SCOPE_STORE
        );
    }

    public function getAccessToken()
    {
        return $this->scopeConfig->getValue(
            self::ACCESS_TOKEN,
            ScopeInterface::SCOPE_STORE
        );
    }

    public function getApiEndpoint()
    {
        return $this->scopeConfig->getValue(
            self::API_ENDPOINT,
            ScopeInterface::SCOPE_STORE
        );
    }
}
