<?php

namespace SomethingDigital\CustomPdp\ViewModel;

use Magento\Framework\View\Element\Block\ArgumentInterface;
use Magento\Framework\Registry;
use Magento\Framework\Pricing\Helper\Data as PricingHelper;
use Magento\Store\Model\StoreManagerInterface; 

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

    public function __construct(
        Registry $registry,
        PricingHelper $pricingHelper,
       StoreManagerInterface $storeManager
    ) {
        $this->coreRegistry = $registry;
        $this->pricingHelper = $pricingHelper;
        $this->storeManager = $storeManager;
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
}
