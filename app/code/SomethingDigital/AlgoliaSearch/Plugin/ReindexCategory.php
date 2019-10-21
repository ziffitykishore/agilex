<?php

namespace SomethingDigital\AlgoliaSearch\Plugin;

use Magento\Catalog\Model\Product as ProductModel;
use Magento\Catalog\Model\ResourceModel\Product as ProductResource;
use Magento\Framework\Indexer\IndexerRegistry;
use Algolia\AlgoliaSearch\Helper\ConfigHelper;
use Magento\Catalog\Api\CategoryLinkManagementInterface;

class ReindexCategory
{
    /** @var Indexer */
    private $indexer;

    /** @var ConfigHelper */
    private $configHelper;

    /** @var LinkManagement */
    private $linkManagement;

    private $productModel;

    /**
     * @param IndexerRegistry $indexerRegistry
     * @param ConfigHelper $configHelper
     */
    public function __construct(
        IndexerRegistry $indexerRegistry,
        ConfigHelper $configHelper,
        CategoryLinkManagementInterface $linkManagement = null,
        ProductModel $productModel
    ) {
        $this->indexer = $indexerRegistry->get('algolia_categories');
        $this->configHelper = $configHelper;
        $this->linkManagement = $linkManagement ?: \Magento\Framework\App\ObjectManager::getInstance()
            ->get(CategoryLinkManagementInterface::class);
        $this->productModel = $productModel;
    }

    public function beforeSave(ProductResource $productResource, ProductModel $product)
    {
        $productBeforeSave = $this->productModel->load($product->getId());
        $categoriesToReindex = array_merge($productBeforeSave->getCategoryIds(), $product->getCategoryIds());

        $productResource->addCommitCallback(function () use ($product, $categoriesToReindex) {
            if (!$this->indexer->isScheduled() || $this->configHelper->isQueueActive()) {

                $this->linkManagement->assignProductToCategories(
                    $product->getSku(),
                    $product->getCategoryIds()
                );

                $this->indexer->reindexList($categoriesToReindex);
            }
        });

        return [$product];
    }
}