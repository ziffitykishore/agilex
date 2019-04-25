<?php

namespace SomethingDigital\CustomerSpecificPricing\Block\Price;

use Magento\Framework\View\Element\Template;
use Magento\Catalog\Block\Product\Context;
use Magento\Catalog\Model\Product\Type\Simple;
use Magento\Framework\Registry;
use Magento\Catalog\Api\ProductRepositoryInterface;
use Magento\Framework\Serialize\Serializer\Json as JsonEncoder;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Customer\Model\Session;

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

    public function __construct(
        Context $context,
        Registry $registry,
        ProductRepositoryInterface $productRepository,
        JsonEncoder $jsonEncoder,
        Session $customerSession
    ) {
        parent::__construct($context);
        $this->coreRegistry = $registry;
        $this->productRepository = $productRepository;
        $this->jsonEncoder = $jsonEncoder;
        $this->customerSession = $customerSession;
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

    private function appendConfigurations(array &$config, \Magento\Catalog\Api\Data\ProductInterface $productData)
    {
        $config['url'] = $this->getBaseUrl() . self::CSP_ENDPOINT_URL;
        $config['type'] = $productData->getTypeId();
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
        $data[] = [
            "sku" => $productData->getSku()
        ];
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
