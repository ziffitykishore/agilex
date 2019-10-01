<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2019 Amasty (https://www.amasty.com)
 * @package Amasty_Groupcat
 */


namespace Amasty\Groupcat\Plugin\Catalog\Model;

use Amasty\Groupcat\Helper\Data;
use Amasty\Groupcat\Model\ProductRuleProvider;
use Magento\Framework\Registry;
use Magento\Search\Model\EngineResolver;

class Layer
{
    /**
     * @var ProductRuleProvider
     */
    private $ruleProvider;

    /**
     * @var Data
     */
    private $helper;

    /**
     * @var Registry
     */
    private $coreRegistry;

    /**
     * @var EngineResolver
     */
    private $searchEngineResolver;

    public function __construct(
        ProductRuleProvider $ruleProvider,
        Data $helper,
        Registry $coreRegistry,
        EngineResolver $searchEngineResolver
    ) {
        $this->ruleProvider = $ruleProvider;
        $this->helper = $helper;
        $this->coreRegistry = $coreRegistry;
        $this->searchEngineResolver = $searchEngineResolver;
    }

    /**
     * Prepare Product Collection for layred Navigation.
     * Add restricted product filter to search engine.
     * In search_request.xml added filter for amasty_groupcat_entity_id
     *
     * @param \Magento\Catalog\Model\Layer                            $subject
     * @param \Magento\Catalog\Model\ResourceModel\Product\Collection $collection
     *
     * @return array|null
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function beforePrepareProductCollection($subject, $collection)
    {
        if (!$this->helper->isModuleEnabled() || $this->coreRegistry->registry('amasty_ignore_product_filter')) {
            return null;
        }

        $collection->setFlag('groupcat_filter_applied', 1);
        $productIds = $this->ruleProvider->getRestrictedProductIds();

        if ($productIds) {
            // add filter to product fulltext search | catalog product collection
            if ($this->searchEngineResolver->getCurrentSearchEngine() === EngineResolver::CATALOG_SEARCH_MYSQL_ENGINE) {
                $collection->addFieldToFilter('amasty_groupcat_mysql_entity_id', ['nin' => $productIds]);
            } else {
                $collection->addFieldToFilter('amasty_groupcat_elastic_entity_id', $productIds);
            }

            return [$collection];
        }

        return null;
    }
}
