<?php
declare(strict_types=1);
namespace Earthlite\Category\ViewModel;

use Magento\Catalog\Api\ProductAttributeOptionManagementInterface;
use Magento\Framework\Registry;
use Magento\Catalog\Helper\Output;
use Magento\Catalog\Model\Category;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Swatches\Model\SwatchFactory;
use Magento\Swatches\Model\ResourceModel\SwatchFactory as SwatchResourceFactory;
use Magento\Swatches\Helper\Media;

/**
 * class CategoryData
 */
class CategoryData implements \Magento\Framework\View\Element\Block\ArgumentInterface
{
    const ATTRIBUTE_CODE = 'brands';
    
    /**
     * Recipient email config path
     */
    const XML_PATH_SUBCATEGORY_CONFIG = 'subcategory/general/enable';
    
    /**
     * Core registry
     *
     * @var \Magento\Framework\Registry
     */
    protected $_coreRegistry = null;

    /**
     * Catalog Helper
     *
     * @var \Magento\Framework\Registry
     */
    protected $_catalogHelper;

    /**
     * Category Model
     *
     * @var \Magento\Catalog\Model\Category
     */
    protected $_categoryModel;

    /**
     * @var \Magento\Framework\App\Config\ScopeConfigInterface
     */
    protected $scopeConfig;
    
    /**
     *
     * @var ProductAttributeOptionManagementInterface 
     */
    protected $productAttributeOptions;
    
    /**
     *
     * @var SwatchFactory
     */
    protected $swatchFactory;
    
    /**
     *
     * @var SwatchResourceFactory
     */
    protected $swatchResourceFactory;

    /**
     * CategoryData Constructor
     * 
     * @param Registry $registry
     * @param Output $catalogHelper
     * @param Category $categoryModel
     * @param ScopeConfigInterface $scopeConfig
     * @param ProductAttributeOptionManagementInterface $productAttributeOptions
     * @param SwatchFactory $swatchFactory
     * @param SwatchResourceFactory $swatchResourceFactory
     * @param Media
     */
    public function __construct(
        Registry $registry,
        Output $catalogHelper,
        Category $categoryModel,
        ScopeConfigInterface $scopeConfig,
        ProductAttributeOptionManagementInterface $productAttributeOptions,
        SwatchFactory $swatchFactory,
        SwatchResourceFactory $swatchResourceFactory,
        Media $media
    ) {
        $this->_coreRegistry = $registry;
        $this->_catalogHelper = $catalogHelper;
        $this->_categoryModel = $categoryModel;
        $this->scopeConfig = $scopeConfig;
        $this->productAttributeOptions = $productAttributeOptions;
        $this->swatchFactory = $swatchFactory;
        $this->swatchResourceFactory = $swatchResourceFactory;
        $this->media = $media;
    }

    /**
     * 
     * @return \Magento\Catalog\Model\Category
     */
    public function getCurrentCategory()
    {
        $category = $this->_coreRegistry->registry('current_category');
        return $category;
    }

    /**
     * 
     * @return Output
     */
    public function getCatalogHelper()
    {
        return $this->_catalogHelper;
    }

    /**
     * 
     * @param type $id
     * @return Category
     */
    public function loadCategoryById($id)
    {
        $category = $this->_categoryModel->load($id);
        return $category;
    }

    /**
     * 
     * @return string
     */
    public function getSubcategoryConfig()
    {
        $storeScope = \Magento\Store\Model\ScopeInterface::SCOPE_STORE;
        return $this->scopeConfig->getValue(self::XML_PATH_SUBCATEGORY_CONFIG, $storeScope);
    }
    
    /**
     * @return []
     */
    public function getBrands()
    {
        $options = $this->productAttributeOptions->getItems(self::ATTRIBUTE_CODE);
        $brands = [];
        foreach ($options as $option) {
            if ($option->getValue()) {
                $swatchModel = $this->swatchFactory->create();
                $swatchResourceModel = $this->swatchResourceFactory->create();
                $swatchResourceModel->load($swatchModel, $option->getValue(), 'option_id');
                $brandImageUrl = $this->media->getSwatchMediaUrl() . $swatchModel->getValue();
                $brands[] = [
                    'brandLabel' => $option->getLabel(),
                    'brandValue' => $option->getValue(),
                    'brandImage' => $brandImageUrl
                ];
            }
        }
        return $brands;
    }
}
