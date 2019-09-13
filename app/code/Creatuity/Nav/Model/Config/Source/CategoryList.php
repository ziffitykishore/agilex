<?php

namespace Creatuity\Nav\Model\Config\Source;

use Magento\Catalog\Api\CategoryRepositoryInterface;
use Magento\Catalog\Api\Data\CategoryInterface;
use Magento\Catalog\Helper\Category as CategoryHelper;
use Magento\Framework\Option\ArrayInterface;

class CategoryList implements ArrayInterface
{
    protected $rootCategoryId;
    protected $categoryRepository;
    protected $categoryHelper;

    public function __construct(
        $rootCategoryId,
        CategoryHelper $categoryHelper,
        CategoryRepositoryInterface $categoryRepository
    ) {
        $this->rootCategoryId= $rootCategoryId;
        $this->categoryHelper= $categoryHelper;
        $this->categoryRepository = $categoryRepository;
    }

    public function toOptionArray()
    {
        $options = [];

        $rootCategoryObject = $this->categoryRepository->get($this->rootCategoryId);
        $categoryIds = explode(',', $rootCategoryObject->getAllChildren());
        foreach ($categoryIds as $categoryId) {
            $categoryObject = $this->categoryRepository->get($categoryId);
            $parentLabel = $this->getParentLabel($categoryObject);
            $label = (!empty($parentLabel)) ? $parentLabel : $categoryObject->getName();
            $options[$categoryId] = $label;
        }
        asort($options);

        return $options;
    }

    protected function getParentLabel(CategoryInterface $category)
    {
        $parentCategoryIds = $category->getPathInStore();
        if (empty($parentCategoryIds)) {
            return '';
        }

        $parentLabel = [];
        $parentCategoryIds = array_reverse(explode(',', $parentCategoryIds));

        foreach ($parentCategoryIds as $parentCategoryId) {
            $parentCategoryObj = $this->categoryRepository->get($parentCategoryId);
            $parentLabel[] = $parentCategoryObj->getName();
        }

        return implode(' / ', $parentLabel);
    }

    protected function getAllCategories(CategoryInterface $category)
    {
        $categories = '';

        foreach(explode(',', $category->getChildren()) as $subCategoryId) {
            $subCategoryObj = $this->categoryRepository->get($subCategoryId);

            if (!empty($subCategoryObj->getChildren())) {
                $categories .= $this->getAllCategories($subCategoryObj);
            } else {
                $categories .= $subCategoryId . ',';
            }
        }

        return rtrim($categories, ',');
    }
}
