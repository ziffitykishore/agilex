<?php

namespace SomethingDigital\Catalog\Block\Entity\PageBuilder\Block\Catalog;

use Magento\Catalog\Api\CategoryRepositoryInterface;

class Category extends \Gene\BlueFoot\Block\Entity\PageBuilder\Block\Catalog\Category
{
    /**
     * @var \Magento\Eav\Model\Entity\Collection\AbstractCollection;
     */
    protected $_productCollection;
    /**
     * @var \Magento\Catalog\Model\ResourceModel\Product\CollectionFactory
     */
    private $productCollectionFactory;
    
    public function __construct(
        \Magento\Catalog\Block\Product\Context $context,
        \Magento\Framework\Data\Helper\PostHelper $postDataHelper,
        \Magento\Catalog\Model\Layer\Resolver $layerResolver,
        CategoryRepositoryInterface $categoryRepository,
        \Magento\Framework\Url\Helper\Data $urlHelper,
        \Gene\BlueFoot\Model\Stage\Render $render,
        \Magento\Framework\Data\CollectionFactory $dataCollectionFactory,
        \Magento\Catalog\Model\ResourceModel\Product\CollectionFactory $productCollectionFactory,
        array $data = []
    ) {
        parent::__construct(
            $context,
            $postDataHelper,
            $layerResolver,
            $categoryRepository,
            $urlHelper,
            $render,
            $dataCollectionFactory,
            $productCollectionFactory,
            $data
        );
        $this->productCollectionFactory = $productCollectionFactory;
    }
    /**
     * @return \Magento\Catalog\Model\ResourceModel\Product\Collection
     */
    public function _getProductCollection()
    {
        return $this->productCollectionFactory->create();
    }
    /**
     * @return \Magento\Catalog\Model\ResourceModel\Product\Collection
     */
    public function getProductCollection()
    {
        /* @var $dataModel \Gene\BlueFoot\Model\Attribute\Data\Widget\Category */
        $dataModel = $this->getEntity()
            ->getResource()
            ->getAttribute(
                'category_id'
            )
            ->getDataModel($this->getEntity());
        if ($dataModel instanceof  \Gene\BlueFoot\Model\Attribute\Data\Widget\Category
            && method_exists($dataModel, 'getProductCollection')
        ) {
            return $dataModel->getProductCollection();
        }
        return false;
    }
}