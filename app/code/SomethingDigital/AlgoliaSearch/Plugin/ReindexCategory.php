<?php

namespace SomethingDigital\AlgoliaSearch\Plugin;

use Magento\Catalog\Model\Product as ProductModel;
use Magento\Catalog\Model\ResourceModel\Product as ProductResource;
use Magento\Framework\Indexer\IndexerRegistry;
use Algolia\AlgoliaSearch\Helper\ConfigHelper;
use Magento\Catalog\Api\CategoryLinkManagementInterface;

class ReindexCategory
{
    /** @var Product */
    private $indexer;

    /** @var ConfigHelper */
    private $configHelper;

    private $linkManagement;

    /**
     * @param IndexerRegistry $indexerRegistry
     * @param ConfigHelper $configHelper
     */
    public function __construct(
        IndexerRegistry $indexerRegistry,
        ConfigHelper $configHelper,
        CategoryLinkManagementInterface $linkManagement = null
    ) {
        $this->indexer = $indexerRegistry->get('algolia_categories');
        $this->configHelper = $configHelper;
        $this->linkManagement = $linkManagement ?: \Magento\Framework\App\ObjectManager::getInstance()
            ->get(CategoryLinkManagementInterface::class);
    }

    public function beforeSave(ProductResource $productResource, ProductModel $product)
    {
        $productResource->addCommitCallback(function () use ($product) {
            if (!$this->indexer->isScheduled() || $this->configHelper->isQueueActive()) {

                $this->linkManagement->assignProductToCategories(
                    $product->getSku(),
                    $product->getCategoryIds()
                );

                $this->indexer->reindexAll();
            }
        });

        return [$product];
    }
}