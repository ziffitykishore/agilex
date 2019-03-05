<?php

namespace SomethingDigital\BlueFootCustomizations\Block\Entity\PageBuilder\Block;

use Magento\Catalog\Model\CategoryRepository;
use Magento\Catalog\Helper\Category as CategoryHelper;

class AbstractBlock extends \Gene\BlueFoot\Block\Entity\PageBuilder\Block\AbstractBlock
{
    protected $categoryHelper;
    protected $categoryRepository;
    
    /**
     * AbstractBlock constructor.
     *
     * @param \Magento\Framework\View\Element\Template\Context $context
     * @param \Gene\BlueFoot\Model\Stage\Render                $render
     * @param \Magento\Framework\Data\CollectionFactory        $dataCollectionFactory
     * @param \Magento\Catalog\Helper\Category                 $categoryHelper
     * @param \Magento\Catalog\Model\CategoryRepository        $categoryRepository
     * @param array                                            $data
     */
    public function __construct(
        \Magento\Framework\View\Element\Template\Context $context,
        \Gene\BlueFoot\Model\Stage\Render $render,
        \Magento\Framework\Data\CollectionFactory $dataCollectionFactory,
        CategoryHelper $categoryHelper,
        CategoryRepository $categoryRepository,
        array $data = []
    ) {
        parent::__construct($context, $render, $dataCollectionFactory, $data);
        $this->categoryHelper = $categoryHelper;
        $this->categoryRepository = $categoryRepository;
    }
    
    /**
     * Get Category Subcategories
     * 
     * @param int $categoryId
     * @return array
     */
    public function getCategorySubcategories($categoryId)
    {
        $categoryObj = $this->categoryRepository->get($categoryId);
        $subcategories = $categoryObj->getChildrenCategories();
        return $subcategories;
    }
}
