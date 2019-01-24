<?php

namespace Ziffity\Header\Observer;

use Magento\Framework\Event\ObserverInterface;
use Magento\Framework\Event\Observer;
use Magento\Catalog\Api\CategoryRepositoryInterface;
use Magento\Store\Model\StoreManagerInterface;
use Magento\Framework\UrlInterface;

class Topmenu implements ObserverInterface
{

    const CATEGORY_IMG_PATH = 'catalog/category/';

    protected $categoryRepository;
    protected $categoryCollection;
    protected $storeManager;

    public function __construct(
        \Magento\Catalog\Model\ResourceModel\Category\CollectionFactory $categoryCollection,
        StoreManagerInterface $storeManager,
        CategoryRepositoryInterface $categoryRepository
    )
    {
        $this->_categoryCollection = $categoryCollection;
        $this->_storeManager = $storeManager;
        $this->categoryRepository = $categoryRepository;
    }

    public function execute(Observer $observer)
    {
        $transport = $observer->getTransport();
        $html = $transport->getHtml();
        $menuTree = $transport->getMenuTree();

        $parentLevel = $menuTree->getLevel();
        $childLevel = $parentLevel === null ? 0 : $parentLevel + 1;
        $menuId = $menuTree->getId();

        if ($childLevel == 1) {

            $html .= '<div class="category_image"><img class="magebees_lazyload" alt="" data-src="' . $this->getCategoryImage($menuId) . '"/></div>';
        }

        $transport->setHtml($html);
    }

    /**
     * to get category image URL
     * @param int $categoryId
     * @return string
     */
    protected function getCategoryImage($categoryId)
    {
        $categoryIdElements = explode('-', $categoryId);
        $category = $this->categoryRepository->get(end($categoryIdElements));
        $data = $this->getCategoryData($category->getId());
        $categoryImage = false;
        if (!empty($data['image_url'])) {
            $imageUrl = $data['image_url'];
            $mediaUrl = $this->getMediaUrl();
            $categoryImage = $mediaUrl . SELF::CATEGORY_IMG_PATH . $imageUrl;
        }
        return $categoryImage;
    }

    public function getCategoryCollection()
    {
        $collection = $this->_categoryCollection->create()->addAttributeToSelect('*');
        return $collection;
    }

    public function getCategoryData($menuId = null)
    {
        $collection = $this->getCategoryCollection();

        $categoryData = [];
        foreach ($collection as $categories) {
            $data = $categories->getData();
            if ($data['entity_id'] == $menuId && array_key_exists('custom_image', $data)) {
                $categoryData['id'] = $data['entity_id'];
                $categoryData['image_url'] = $data['custom_image'];
            }
        }

        return $categoryData;
    }

    public function getMediaUrl()
    {
        return $this->_storeManager->getStore()
                    ->getBaseUrl(UrlInterface::URL_TYPE_MEDIA);
    }

}
