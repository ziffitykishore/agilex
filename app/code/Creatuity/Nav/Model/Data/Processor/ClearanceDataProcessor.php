<?php

namespace Creatuity\Nav\Model\Data\Processor;

use Magento\Catalog\Api\CategoryLinkManagementInterface;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\DataObject;

class ClearanceDataProcessor implements DataProcessorInterface
{
    protected $clearanceCategoryIdConfigPath;
    protected $clearanceCategoryId;
    protected $categoryLinkManagement;
    protected $scopeConfig;

    public function __construct(
        $clearanceCategoryIdConfigPath,
        CategoryLinkManagementInterface $categoryLinkManagement,
        ScopeConfigInterface $scopeConfig
    ) {
        $this->clearanceCategoryIdConfigPath = $clearanceCategoryIdConfigPath;
        $this->categoryLinkManagement = $categoryLinkManagement;
        $this->scopeConfig = $scopeConfig;
    }

    public function process(DataObject $productData, DataObject $intermediateData)
    {
        if ($intermediateData->getClearance()) {
            $this->addToClearanceCategory($productData);
            return;
        }

        $this->removeFromClearanceCategory($productData);
    }

    protected function addToClearanceCategory(DataObject $productData)
    {
        if ($this->isInClearanceCategory($productData)) {
            return;
        }

        $categories = $productData->getCategoryIds();
        $categories[] = $this->getClearanceCategoryId();
        $this->updateCategoryAssignment($productData, $categories);
    }

    protected function removeFromClearanceCategory(DataObject $productData)
    {
        if (!$this->isInClearanceCategory($productData)) {
            return;
        }

        $categories = array_diff(
            $productData->getCategoryIds(),
            [ $this->getClearanceCategoryId() ]
        );
        $this->updateCategoryAssignment($productData, $categories);
    }

    protected function isInClearanceCategory(DataObject $productData)
    {
        $categories = $productData->getCategoryIds();
        return isset(array_flip($categories)[$this->getClearanceCategoryId()]);
    }

    protected function updateCategoryAssignment(DataObject $productData, array $categories)
    {
        $this->categoryLinkManagement->assignProductToCategories(
            $productData->getSku(),
            $categories
        );
    }

    protected function getClearanceCategoryId()
    {
        if (!isset($this->clearanceCategoryId)) {
            $this->clearanceCategoryId = $this->scopeConfig->getValue($this->clearanceCategoryIdConfigPath);
        }

        return $this->clearanceCategoryId;
    }
}
