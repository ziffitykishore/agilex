<?php

namespace PartySupplies\LayeredNavigation\ViewModel;

use Magento\Framework\View\Element\Block\ArgumentInterface;

class Category implements ArgumentInterface
{

    /**
     * @var \Magento\Store\Model\StoreManagerInterface
     */
    protected $storeManager;

    /**
     * @var \Magento\Catalog\Model\CategoryRepository
     */
    protected $categoryRepository;

    /**
     * @var \Magento\Framework\UrlInterface
     */
    protected $urlInterface;

    /**
     *
     * @param \Magento\Store\Model\StoreManagerInterface $storeManager
     * @param \Magento\Catalog\Model\CategoryRepository $categoryRepository
     * @param \Magento\Framework\UrlInterface $urlInterface
     */
    public function __construct(
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Magento\Catalog\Model\CategoryRepository $categoryRepository,
        \Magento\Framework\UrlInterface $urlInterface
    ) {
        $this->storeManager = $storeManager;
        $this->categoryRepository = $categoryRepository;
        $this->urlInterface = $urlInterface;
    }

    /**
     * To return current url
     *
     * @param int $id
     * @return string
     */
    public function getCurrentUrl($id)
    {
        if (is_numeric($id)) {
            return $this->categoryRepository->get(
                $id,
                $this->storeManager->getStore()->getId()
            )->getUrl();
        } else {
            return $this->urlInterface->getCurrentUrl();
        }
    }
 
    /**
     * To return filter url
     *
     * @return string
     */
    public function getFilterBaseUrl()
    {
        $rootCategory = $this->categoryRepository->get(
            $this->storeManager->getStore()->getRootCategoryId(),
            $this->storeManager->getStore()->getId()
        );
        $children = $rootCategory->getChildrenCategories();

        foreach ($children as $category) {
            if ($category !== null) {
                return $category->getUrl();
            }
        }
    }
}
