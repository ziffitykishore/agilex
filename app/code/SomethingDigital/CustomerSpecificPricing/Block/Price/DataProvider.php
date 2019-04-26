<?php

namespace SomethingDigital\CustomerSpecificPricing\Block\Price;

use Magento\Framework\View\Element\Template;
use Magento\Catalog\Block\Product\Context;
use Magento\Catalog\Model\Product\Type\Simple;
use Magento\ConfigurableProduct\Model\Product\Type\Configurable;
use Magento\Framework\Registry;
use Magento\Catalog\Api\ProductRepositoryInterface;
use Magento\Framework\Serialize\Serializer\Json as JsonEncoder;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Customer\Model\Session;
use Magento\ConfigurableProduct\Api\LinkManagementInterface;
use SomethingDigital\CustomerSpecificPricing\Model\SkuMap;

class DataProvider extends Template
{
    const CSP_ENDPOINT_URL = 'csp/prices';

   /**
    * @var Registry
    */
    private $coreRegistry;

   /**
    * @var ProductRepositoryInterface
    */
    private $productRepository;

   /**
    * @var JsonEncoder
    */
    private $jsonEncoder;

    /**
    * @var CustomerSession
    */
    private $customerSession;

    /**
     * @var LinkManagementInterface
     */
    private $linkManagement;

    /**
    * @var SkuMap
    */
    private $skuMap;

    public function __construct(
        Context $context,
        Registry $registry,
        ProductRepositoryInterface $productRepository,
        JsonEncoder $jsonEncoder,
        Session $customerSession,
        LinkManagementInterface $linkManagement,
        SkuMap $skuMap
    ) {
        parent::__construct($context);
        $this->coreRegistry = $registry;
        $this->productRepository = $productRepository;
        $this->jsonEncoder = $jsonEncoder;
        $this->customerSession = $customerSession;
        $this->linkManagement = $linkManagement;
        $this->skuMap = $skuMap;
    }
    
    /**
     * Retrieve current product model
     *
     * @return \Magento\Catalog\Model\Product
     */
    public function getProduct()
    {
        return $this->coreRegistry->registry('product');
    }

    /**
     * Returns JSON string with all the neccessary data
     * to initialize the data provider component
     *
     * @return string JSON
     */
    public function getJsConfig()
    {
        /** @var \Magento\Catalog\Model\Product $currentProduct */
        $currentProduct = $this->getProduct();
        /** @var string $productType */
        $productType = $currentProduct->getTypeId();
        try {
            /** @var \Magento\Catalog\Api\Data\ProductInterface $productData */
            $productData = $this->productRepository->getById($currentProduct->getId());
        } catch (NoSuchEntityException $e) {
            return '';
        }

        /** @var string[] $config */
        $config = [];
        if ($productType == 'simple') {
            $config['data'] = $this->getSimpleProductData($productData);
        } else if ($productType == Configurable::TYPE_CODE) {
            $config['data'] = $this->getConfigurableProductData($productData);
        }
        $this->appendConfigurations($config, $productData);
        return $this->jsonEncoder->serialize($config);
    }

    private function getSimpleProductData(\Magento\Catalog\Api\Data\ProductInterface $productData)
    {
        /** @var string[][] $data */
        $data = [];
        $this->appendProductData($data, $productData);
        return $data;
    }

    private function getConfigurableProductData(\Magento\Catalog\Api\Data\ProductInterface $productData) 
    {
        /** @var \Magento\Catalog\Api\Data\ProductInterface[] $childProducts */
        $childProducts = $this->linkManagement->getChildren($productData->getSku());
        /** @var string[][] $data */
        $data = [];

        // lets add our configurable first
        $this->appendProductData($data, $productData);

        /** @var \Magento\Catalog\Api\Data\ProductInterface $child */
        foreach ($childProducts as $child) {
            $this->appendProductData($data, $child);
        }
        return $data;
    }

    private function appendConfigurations(array &$config, \Magento\Catalog\Api\Data\ProductInterface $productData)
    {
        $config['url'] = $this->getBaseUrl() . self::CSP_ENDPOINT_URL;
        $config['type'] = $productData->getTypeId();
        if ($config['type'] === Configurable::TYPE_CODE) {
            $config['parent'] = $productData->getId();
        }
        $config['map'] = $this->skuMap->getMap($productData);
    }

    /**
     * Adds product data to a multidimensional array
     *
     * @param string[][] $data
     * @param \Magento\Catalog\Api\Data\ProductInterface $productData
     * @return void
     */
    private function appendProductData(array &$data, \Magento\Catalog\Api\Data\ProductInterface $productData)
    {
        $data[$productData->getId()] = $productData->getSku();
    }

    public function isCustomerLoggedIn()
    {
        if ($this->customerSession->isLoggedIn()) {
            return true;
        } else {
            return false;
        }
    }
}
