<?php

namespace SomethingDigital\BlueFootCustomizations\Block\Entity\PageBuilder\Block;

use Magento\Catalog\Model\CategoryRepository;
use Magento\Catalog\Helper\Category as CategoryHelper;
use Magento\Catalog\Model\CategoryFactory;

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
        CategoryFactory $categoryFactory,
        array $data = []
    ) {
        parent::__construct($context, $render, $dataCollectionFactory, $data);
        $this->categoryHelper = $categoryHelper;
        $this->categoryRepository = $categoryRepository;
        $this->categoryFactory = $categoryFactory;
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
        $subcategories = $categoryObj->getChildren();

        $collection = $this->categoryFactory->create()->getCollection()->addAttributeToSelect('*')
              ->addAttributeToFilter('is_active', 1)
              ->setOrder('name', 'ASC')
              ->addIdFilter($subcategories);
        return $collection;
    }
}
