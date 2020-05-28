<?php

namespace SomethingDigital\Search\Model;

use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Store\Model\ScopeInterface;
use SomethingDigital\SearchCustomization\Plugin\Suffix;

/**
 * Class PopularSearchTerms
 *
 * @package SomethingDigital\Search\Model
 */
class PopularSearchTerms
{
    const XML_PATH_CACHEABLE_SEARCH_TERM = 'catalog/search/cacheable_search_term';

    /**
     * @var ScopeConfigInterface
     */
    protected $scopeConfig;

    /**
     * PopularSearchTerms constructor.
     *
     * @param ScopeConfigInterface $scopeConfig
     */
    public function __construct(
        ScopeConfigInterface $scopeConfig,
        Suffix $searchSuffix
    ) {
        $this->scopeConfig = $scopeConfig;
        $this->searchSuffix = $searchSuffix;
    }

    /**
     * Check if is cacheable search term
     *
     * @param string $term
     * @param int $storeId
     * @return bool
     */
    public function isCacheable(string $term, int $storeId)
    {
        if ($this->searchSuffix->suffixFlag) {
            return false;
        }

        return $this->scopeConfig->isSetFlag(
            self::XML_PATH_CACHEABLE_SEARCH_TERM,
            ScopeInterface::SCOPE_STORE,
            $storeId
        );
    }
}
