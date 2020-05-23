<?php

declare(strict_types = 1);

namespace Earthlite\Category\Plugin\Controller\Category;

use Magento\Catalog\Controller\Category\View;
use Magento\Catalog\Api\CategoryRepositoryInterfaceFactory;
use Magento\Store\Model\StoreManagerInterface;
use Magento\Framework\Controller\Result\ForwardFactory;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\Controller\ResultInterface;
use Magento\Store\Model\ScopeInterface;

/**
 * Class ViewPlugin
 */
class ViewPlugin
{
    const CATEGORY_LISTING_PAGE_LEVEL = 2;
    
    const XML_PATH_CLP = 'earthlite_category/category_general/clp_enable';
    
    /**
     *
     * @var CategoryRepositoryInterfaceFactory 
     */
    protected $categoryRepositoryInterfaceFactory;
    
    /**
     *
     * @var StoreManagerInterface 
     */
    protected $storeManagerInterface;
    
    /**
     *
     * @var ForwardFactory 
     */
    protected $resultForwardFactory;
    
    /**
     *
     * @var ScopeConfigInterface
     */
    protected $scopeConfig;

    /**
     * 
     * @param CategoryRepositoryInterfaceFactory $categoryRepositoryInterfaceFactory
     * @param StoreManagerInterface $storeManagerInterface
     * @param ForwardFactory $resultForwardFactory
     * @param ScopeConfigInterface $scopeConfig
     */
    public function __construct(
        CategoryRepositoryInterfaceFactory $categoryRepositoryInterfaceFactory,
        StoreManagerInterface $storeManagerInterface,
        ForwardFactory $resultForwardFactory,
        ScopeConfigInterface $scopeConfig
    ) {
        $this->categoryRepositoryInterfaceFactory = $categoryRepositoryInterfaceFactory;
        $this->storeManagerInterface = $storeManagerInterface;
        $this->resultForwardFactory = $resultForwardFactory;
        $this->scopeConfig = $scopeConfig;
    }
    
    /**
     * 
     * @param View $subject
     * @param \Earthlite\Category\Plugin\Controller\Category\callable $proceed
     * @return ResultInterface
     */
    public function aroundExecute(View $subject, callable $proceed)
    {
        $categoryId = $subject->getRequest()->getParam('id');
        /** @var \Magento\Catalog\Api\CategoryRepositoryInterface $categoryRepository */
        $categoryRepository = $this->categoryRepositoryInterfaceFactory->create();
        $storeId = $this->storeManagerInterface->getStore()->getId();
        $categoryDetails = $categoryRepository->get($categoryId, $storeId);
        if ($this->getCategoryListingPageStatus()) {
            if ($categoryDetails->getLevel() == self::CATEGORY_LISTING_PAGE_LEVEL
                    && !array_key_exists('brands', $subject->getRequest()->getParams())) {
                $resultForward = $this->resultForwardFactory->create();
                $resultForward->setModule('category');
                $resultForward->setController('index');
                $resultForward->setParams(['id' => $categoryId]);
                $resultForward->forward('index');
                return $resultForward;
            }
        }
        $result = $proceed();
        return $result;
    }
    
    /**
     * 
     * @return bool
     */
    protected function getCategoryListingPageStatus()
    {
        return $this->scopeConfig->getValue(self::XML_PATH_CLP, ScopeInterface::SCOPE_STORE);
    }
}
