<?php
 
namespace Earthlite\TopCategory\Plugin;
 
class HelperPlugin
{
    /**
     * Core registry
     *
     * @var \Magento\Framework\Registry
     */
    protected $_coreRegistry = null;

    /**
     * @var \Magento\Framework\App\Config\ScopeConfigInterface
     */
    protected $scopeConfig;

    /**
     * Subcategory config path
     */
    const XML_PATH_SUBCATEGORY_CONFIG = 'subcategory/general/enable';

    public function __construct(
        \Magento\Framework\Registry $registry,
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig
    ) {
        $this->_coreRegistry = $registry;
        $this->scopeConfig = $scopeConfig;
    }

    public function afterIsAllow(\Magento\Wishlist\Helper\Data $subject, $result)
    {
         $category = $this->_coreRegistry->registry('current_category');

        $storeScope = \Magento\Store\Model\ScopeInterface::SCOPE_STORE;

        $moduleStatus = $this->scopeConfig->getValue(self::XML_PATH_SUBCATEGORY_CONFIG, $storeScope);
         
        if ($moduleStatus && $result) {
            $allowBlock = false;
            
            if ($category == '') {
                $allowBlock = true;
            } elseif ($category->getLevel() > 2 || empty($category->getChildrenCategories()->getSize()) || !$viewModel->getSubcategoryConfig()) {
                $allowBlock = true;
            }
             
            if (!$allowBlock) {
                return $allowBlock;
            }
        }

        return $result;
    }
}
