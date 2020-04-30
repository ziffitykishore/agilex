<?php

namespace Earthlite\SuggestiveSearch\Helper;

use Magento\Catalog\Model\Config;
use Magento\Catalog\Model\Product\Visibility;
use Magento\Catalog\Model\ResourceModel\Product\CollectionFactory as ProductCollectionFactory;
use Magento\Customer\Model\ResourceModel\Group\CollectionFactory;
use Magento\Customer\Model\Session;
use Magento\Store\Model\StoreManagerInterface;
use Magento\Framework\App\Helper\AbstractHelper;
use Magento\Catalog\Helper\ImageFactory;
use Magento\Framework\UrlInterface;

class Data extends AbstractHelper
{
    const CONFIG_MODULE_PATH = 'auto_search';

    protected $catalogProductVisibility;

    protected $catalogConfig;

    protected $_customerGroupFactory;

    protected $imageFactory;

    protected $customerSession;

    protected $_productCollectionFactory;

    protected $storeManager;

    protected $customerGroupCollectionFactory;

    public function __construct(        
        StoreManagerInterface $storeManager,        
        CollectionFactory $customerGroupCollectionFactory,        
        Visibility $catalogProductVisibility,
        Config $catalogConfig,
        Session $customerSession,
        ImageFactory $imageFactory,
        ProductCollectionFactory $_productCollectionFactory,
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig        
    )
    {        
        $this->storeManager = $storeManager;        
        $this->_customerGroupFactory = $customerGroupCollectionFactory;        
        $this->catalogProductVisibility = $catalogProductVisibility;
        $this->catalogConfig = $catalogConfig;                
        $this->imageFactory = $imageFactory;
        $this->_productCollectionFactory = $_productCollectionFactory;
        $this->scopeConfig = $scopeConfig;
        $this->_customerSession = $customerSession;
    }

    public function isEnabled($storeId = null)
    {        
        return $this->getConfigGeneral('enabled', $storeId);
    }

    public function getConfigGeneral($code = '', $storeId = null)
    {
        $code = ($code !== '') ? '/' . $code : '';

        return $this->getConfigValue(static::CONFIG_MODULE_PATH . '/general' . $code, $storeId);
    }


    public function getConfigValue($fullPath, $storeId)
    {        
        return $this->scopeConfig->getValue($fullPath, \Magento\Store\Model\ScopeInterface::SCOPE_STORE);
    }    

    public function createJsonData()
    {
        $errors = [];        
        try 
        {
            $store = $this->storeManager->getStore();                          
            return $this->createJsonFileForStore($store);
        }
        catch (\Exception $e) 
        {
            $errors[] = __('Cannot generate data for store %1 and error %2', $store->getCode(), $e->getMessage());
        }
        return $errors;
    }

    public function createJsonFileForStore($store)
    {
        if(!$this->isEnabled($store->getId())){
            return $this;
        }
        
        $productList = [];
        $collection = $this->_productCollectionFactory->create();
        $collection->addAttributeToSelect($this->catalogConfig->getProductAttributes())
            ->setStore($store)                        
            ->addStoreFilter()
            ->addUrlRewrite()
            ->setVisibility($this->catalogProductVisibility->getVisibleInSearchIds());

        foreach ($collection as $product) 
        {
            $productList[] = [
                'name' => $product->getName(),                
                'value' => $product->getSku(),
                'image'     => $this->getProductImage($product),
                'url'     => $this->getProductUrl($product)
            ];
        }

        return json_encode($productList);                
    }

    protected function getProductUrl($product)
    {
        $productUrl  = $product->getProductUrl();
        $requestPath = $product->getRequestPath();
        if (!$requestPath) {
            $pos = strpos($productUrl, 'catalog/product/view');
            if ($pos !== false) {
                $productUrl = substr($productUrl, $pos + 20);
            }
        } else {
            $productUrl = $requestPath;
        }

        return $productUrl;
    }
      
    public function getProductImage($product, $imageId = 'autosearch_image')
    {        
        $imageUrl = $this->imageFactory->create()
            ->init($product, $imageId)
            ->getUrl();

        $baseMediaUrl = $this->getSearchMediaUrl();
        if (strpos($imageUrl, $baseMediaUrl) === 0) {
            $imageUrl = substr($imageUrl, strlen($baseMediaUrl));
        }

        return $imageUrl;
    }

    public function getSearchMediaUrl()
    {
        return $this->getBaseMediaUrl() . '/catalog/product/';
    }

     public function getBaseMediaUrl()
    {
        return rtrim($this->storeManager->getStore()->getBaseUrl(UrlInterface::URL_TYPE_MEDIA), '/');
    }
}