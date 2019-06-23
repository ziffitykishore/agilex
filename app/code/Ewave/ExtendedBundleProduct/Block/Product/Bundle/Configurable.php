<?php
namespace Ewave\ExtendedBundleProduct\Block\Product\Bundle;

use Magento\ConfigurableProduct\Model\Product\Type\Configurable as ConfigurableProduct;
use Magento\ConfigurableProduct\Block\Product\View\Type\Configurable as ConfigurableBlock;

class Configurable extends ConfigurableBlock
{
    /**
     * @var array
     */
    private $allowProductsCache = [];

    /**
     * Retrieve selection product
     *
     * @return \Magento\Catalog\Model\Product
     */
    public function getProduct()
    {
        $this->unsAllowProducts();
        return $this->getSelection();
    }

    /**
     * {@inheritdoc}
     */
    public function getAllowProducts()
    {
        $product = $this->getProduct();
        if (!array_key_exists($product->getId(), $this->allowProductsCache)) {
            $this->allowProductsCache[$product->getId()] = [];
            $skipSaleableCheck = $this->catalogProduct->getSkipSaleableCheck();
            $allowedProducts = parent::getAllowProducts();
            foreach ($allowedProducts as $subProduct) {
                if ($subProduct->isDisabled()) {
                    continue;
                }

                if ($skipSaleableCheck || $this->stockRegistry->getStockItem($subProduct->getId())->getIsInStock()) {
                    $this->allowProductsCache[$product->getId()][] = $subProduct;
                }
            }
        }
        return $this->allowProductsCache[$product->getId()];
    }

    /**
     * @param array $array
     * @return string
     */
    public function getJson(array $array)
    {
        return $this->jsonEncoder->encode($array);
    }

    /**
     * @return string
     */
    protected function _toHtml()
    {
        if ($this->getProduct()->getTypeId() == ConfigurableProduct::TYPE_CODE) {
            return parent::_toHtml();
        }
        return '';
    }
}
