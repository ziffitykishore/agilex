<?php

namespace Creatuity\Nav\Model\Data\Extractor\Magento;

use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Store\Model\ScopeInterface;

class StaticDataExtractor
{
    /**
     * @var string
     */
    protected $value;

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
     * @param string $value
     * @param string $path
     * @param ScopeConfigInterface $scopeConfig
     */
    public function __construct(
        $value,
        $path,
        ScopeConfigInterface $scopeConfig
    ) {
        $this->value = $value;
        $this->path = $path;
        $this->scopeConfig = $scopeConfig;
    }

    /**
     * To get Magento admin configuration value or static value.
     *
     * @return string
     */
    public function extract()
    {
        if ($this->value) {
            return $this->value;
        }

        if($this->path) {
            return $this->scopeConfig->getValue(
                $this->path, ScopeInterface::SCOPE_STORE
            );
        }
    }
}
