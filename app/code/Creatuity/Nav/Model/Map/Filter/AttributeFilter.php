<?php

namespace Creatuity\Nav\Model\Map\Filter;

use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Store\Model\ScopeInterface;

class AttributeFilter
{
    /**
     * @var string
     */
    protected $attribute;

    /**
     * @var string
     */
    protected $condition;

    /**
     * @var string
     */
    protected $path;

    /**
     * @var ScopeConfigInterface
     */
    protected $scopeConfig;

    /**
     *
     * @param string $attribute
     * @param string $condition
     * @param string $path
     * @param ScopeConfigInterface $scopeConfig
     */
    public function __construct(
        $attribute,
        $condition,
        $path,
        ScopeConfigInterface $scopeConfig
    ) {
        $this->attribute = $attribute;
        $this->condition = $condition;
        $this->path = $path;
        $this->scopeConfig = $scopeConfig;
    }

    /**
     * To get attribute name
     *
     * @return stting
     */
    public function getAttribute()
    {
        return $this->attribute;
    }

    /**
     * To get attribute value
     *
     * @return string
     */
    public function getCondition()
    {
        if ($this->condition) {
            return $this->condition;
        } else {
            return $this->scopeConfig->getValue(
                $this->path,
                ScopeInterface::SCOPE_STORE
            );
        }
    }
}
