<?php

namespace SomethingDigital\CustomPdp\ViewModel;

use Magento\Framework\View\Element\Block\ArgumentInterface;
use Magento\Framework\Registry;
use Magento\Framework\Pricing\Helper\Data as PricingHelper;
use Magento\Store\Model\StoreManagerInterface; 
use Magento\Framework\Serialize\Serializer\Json as JsonEncoder;
use SomethingDigital\CustomPdp\Helper\BasePrice;

class CustomProductDetails implements ArgumentInterface
{
    /**
     * @var Registry
     */
    private $coreRegistry;

    /**
     * @var PricingHelper
     */
    private $pricingHelper;

    /**
     * @var StoreManagerInterface
     */
    private $storeManager;

   /**
    * @var JsonEncoder
    */
    private $jsonEncoder;

   /**
    * @var BasePrice
    */
    private $basePrice;

    public function __construct(
        Registry $registry,
        PricingHelper $pricingHelper,
        StoreManagerInterface $storeManager,
        JsonEncoder $jsonEncoder,
        BasePrice $basePrice
    ) {
        $this->coreRegistry = $registry;
        $this->pricingHelper = $pricingHelper;
        $this->storeManager = $storeManager;
        $this->jsonEncoder = $jsonEncoder;
        $this->basePrice = $basePrice;
    }

    /**
     * @return \Magento\Catalog\Api\Data\ProductInterface
     */
    public function getProduct()
    {
        return $this->coreRegistry->registry('product');
    }

    /** 
     * Returns formatted price
     *
     * @return string|null
     */
    public function getCustomMsrpPrice()
    {
		/** @var \Magento\Catalog\Api\Data\ProductInterface $product */
        $product = $this->getProduct();
        /** @var \Magento\Framework\Api\AttributeInterface|null $attribute */
        $attribute = $product->getCustomAttribute('manufacturer_price');
        if (!$attribute) {
            return null;
        }
        return $this->pricingHelper->currencyByStore(
            $attribute->getValue(),
            $this->storeManager->getStore()
        );
    }

    /**
     * Returns a JSON object with price data
     * 
     * @return string JSON
     */
    public function getJsConfig()
    {
        /** @var \Magento\Catalog\Api\Data\ProductInterface $product */
        $product = $this->getProduct();
        /** @var string[] $config */
        $config = [
            'type' => $product->getTypeId(),
            'basePrice' => $this->getBasePrice($product),
            'finalPrice' => $product->getSpecialPrice(),
        ];
        return $this->jsonEncoder->serialize($config);
    }

    private function getBasePrice($typeId)
    {
        return $this->basePrice->getPrice($typeId);
    }
}
