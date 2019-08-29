<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace PartySupplies\Search\Helper;

/**
 * Description of Data.
 *
 * @author linux
 */
class Data extends \Magento\Search\Helper\Data
{
    /**
     * @var \Magento\Catalog\Model\CategoryFactory
     */
    protected $categoryFactory;
    /**
     * Url.
     *
     * @var \Magento\Framework\UrlInterface
     */
    protected $_url;

    /**
     * @param \Magento\Framework\App\Helper\Context      $context
     * @param \Magento\Framework\Stdlib\StringUtils      $string
     * @param \Magento\Framework\Escaper                 $escaper
     * @param \Magento\Store\Model\StoreManagerInterface $storeManager
     * @param \Magento\Catalog\Model\CategoryFactory     $categoryFactory
     */
    public function __construct(
        \Magento\Framework\App\Helper\Context $context,
        \Magento\Framework\Stdlib\StringUtils $string,
        \Magento\Framework\Escaper $escaper,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Magento\Catalog\Model\CategoryFactory $categoryFactory,
        \Magento\Framework\UrlInterface $_url
    ) {
        parent::__construct($context, $string, $escaper, $storeManager);
        $this->categoryFactory = $categoryFactory;
        $this->_url = $_url;
    }

    /**
     * Used to return all the subcategory's in a category.
     *
     * @return string $categoryOptions
     */
    public function getCategoriesOption($categoryId = 8)
    {
        $category = $this->categoryFactory->create()->load($categoryId);

        // Categories
        $categories = $category->getChildrenCategories();

        // Categories as options
        $categoryOptions = '';

        $selectedUri = explode('?', $this->_getRequest()->getUri())[0];

        foreach ($categories as $category) {
            if (trim($selectedUri) == $category->getUrl()) {
                $query = ['q' => null];
                $params['_current'] = true;
                $params['_use_rewrite'] = true;
                $params['_query'] = $query;
                $params['_escape'] = true;

                $categoryOptions .= (
                        "<option selected value='" .
                        $this->_url->getUrl('*/*/*', $params) . "'>" .
                        $category->getName() . '</option>');
            } else {
                $categoryOptions .= ("<option value='".$category->getUrl()."'>".$category->getName().'</option>');
            }

            if ($category->hasChildren()) {
                $categoryOptions .= $this->getCategoriesOption($category->getEntityId());
            }
        }

        return $categoryOptions;
    }
}
