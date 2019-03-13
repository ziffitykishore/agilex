<?php
/**
 * RocketWeb
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 *
 * @category  RocketWeb
 * @package   RocketWeb_ShoppingFeeds
 * @copyright Copyright (c) 2016 RocketWeb (http://rocketweb.com)
 * @license   http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 * @author    Rocket Web Inc.
 */

namespace RocketWeb\ShoppingFeeds\Model\Product\Adapter\Type;

use \RocketWeb\ShoppingFeeds\Model\Product\Adapter\AdapterInterface;

/**
 * Bundle Adapter, holds business logic between Product, Config and Mapper
 *
 * Class Bundle
 * @package RocketWeb\ShoppingFeeds\Model\Product\Adapter\Type
 */
class Bundle extends Composite implements AdapterInterface
{
    /**
     * @inheritdoc
     */
    public function beforeMap()
    {
        if (!$this->hasData('associated_product_adapters') || !is_array($this->getData('associated_product_adapters'))) {
            // Get associated products with this one
            $bundleProduct = $this->getProduct();
            /** @var \Magento\Bundle\Model\Product\Type $bundleTypeInstance */
            $bundleTypeInstance = $bundleProduct->getTypeInstance();
            $associatedProductAdapters = [];

            $optionIds = $bundleTypeInstance->getOptionsIds($bundleProduct);
            if ($optionIds) {
                $bundleSelections = $bundleTypeInstance->getSelectionsCollection($optionIds, $bundleProduct);
                $bundleSelections = $bundleSelections->addAttributeToSelect('weight');

                $associatedProductAdapters = $this->prepareAssociatedProductAdapters($bundleSelections);
            }

            $this->setData('associated_product_adapters', $associatedProductAdapters);
        }

        return parent::beforeMap();
    }

    /**
     * @inheritdoc
     */
    public function getAssociatedProductsMode()
    {
        return $this->getFeed()->getConfig('bundle_associated_products_mode');
    }

    /**
     * @inheritdoc
     */
    protected function getProductPrices(\Magento\Catalog\Model\Product $product)
    {
        /** @var \RocketWeb\ShoppingFeeds\Model\Product\Helper\Catalog $catalogHelper */
        $catalogHelper = $this->catalogHelper;

        $prices = array();
        $price = $this->getMinimalPrice();
        $catalogRulesPrice = $this->getPriceByCatalogRules($price);
        $price = $catalogRulesPrice ? min($catalogRulesPrice, $price) : $price;
        $convertedPrice = $this->convertPrice($price);
        $prices['p_excl_tax'] = $catalogHelper->getTaxPrice($product, $convertedPrice);
        $prices['p_incl_tax'] = $catalogHelper->getTaxPrice($product, $convertedPrice, true);

        $finalPrice = $this->getMinimalPrice(true, true);
        $catalogRulesPrice = $this->getPriceByCatalogRules($finalPrice);
        $finalPrice = $catalogRulesPrice ? min($catalogRulesPrice, $finalPrice) : $finalPrice;
        $convertedFinalPrice = $this->convertPrice($finalPrice);
        $prices['sp_excl_tax'] = $catalogHelper->getTaxPrice($product, $convertedFinalPrice);
        $prices['sp_incl_tax'] = $catalogHelper->getTaxPrice($product, $convertedFinalPrice, true);

        return $prices;
    }

    /**
     * Finds the minimal price for the bundle product
     *
     * @param bool $includeTax
     * @return float
     */
    protected function getMinimalPrice($includeTax = false, $special = false)
    {
        $product = $this->getProduct();
        /** @var \Magento\Bundle\Model\Product\Price $productPriceModel */
        $productPriceModel = $product->getPriceModel();

        $specialPrice = $product->getSpecialPrice();
        if (!empty($specialPrice) && !$special) {
            $product->setSpecialPrice('0');
        }

        //force re-calculation
        $product->setData('min_price', '');
        $product->setData('max_price', '');
        $product->setFinalPrice(null);

        $prices = $productPriceModel->getTotalPrices($product, 'min', $includeTax);

        if (is_array($prices)) {
            $price = min($prices);
        } else {
            $price = $prices;
        }
        //put special price back
        $product->setSpecialPrice($specialPrice);

        return $price;
    }

    /**
     * @param bool|true $processRules
     * @param null $product
     * @return bool
     */
    public function hasSpecialPrice($processRules = true, $product = null)
    {
        $has = false;
        if (is_null($product)) {
            $product = $this->product;
        }

        if ($processRules && $this->hasPriceByCatalogRules()) {
            $has = true;
        } elseif ($this->helper->hasMsrp($product)) {
            $has = true;
        } else {
            $specialPrice = $product->getSpecialPrice();
            $price = $product->getPrice();
            $locale = $this->localeResolver->getLocale();

            if ($specialPrice > 0) {
                $cDate = $this->timezone->date(null, $locale);
                $dates = $this->getSpecialPriceEffectiveDates($product, false);
                /**
                 * @var \DateTime $start
                 * @var \DateTime $end
                 */
                extract($dates);

                if ($start <= $cDate && $end >= $cDate && ($specialPrice < $price || $price == 0)) {
                    $has = true;
                }
            }
        }

        return $has;
    }

    /**
     * Creates an array of current configurable attributes/values
     *
     * @param \Magento\Catalog\Model\Product $product
     * @return array
     */
    public function getUrlOptions(\Magento\Catalog\Model\Product $product)
    {
        return array();
    }
}