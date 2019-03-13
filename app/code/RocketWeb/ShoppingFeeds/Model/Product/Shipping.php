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

namespace RocketWeb\ShoppingFeeds\Model\Product;

class Shipping
{
    /**
     * @var Adapter\AdapterAbstract
     */
    protected $adapter;

    /**
     * @var \Magento\Framework\App\Config\ScopeConfigInterface
     */
    protected $config;

    /**
     * @var \Magento\Shipping\Model\Shipping
     */
    protected $shipping;

    /**
     * @var \Magento\Tax\Helper\Data
     */
    protected $taxHelper;

    /**
     * @var \Magento\Customer\Model\AddressFactory
     */
    protected $addressFactory;

    /**
     * @var array
     */
    protected $rates = [];

    /**
     * @var \Magento\Directory\Model\Region
     */
    protected $region;

    /**
     * @var \RocketWeb\ShoppingFeeds\Model\Product\Helper\Catalog
     */
    protected $catalogHelper;

    /**
     * @var \RocketWeb\ShoppingFeeds\Model\Generator\Cache
     */
    protected $cache;

    /**
     * @var null|array
     */
    protected $shippingTerritory = null;

    /**
     * Shipping constructor.
     * @param \Magento\Framework\App\Config\ScopeConfigInterface $config
     * @param \Magento\Shipping\Model\Shipping $shipping
     * @param \Magento\Tax\Helper\Data $taxHelper
     * @param \Magento\Customer\Model\AddressFactory $addressFactory
     * @param \Magento\Directory\Model\Region $region
     * @param \RocketWeb\ShoppingFeeds\Model\Generator\Cache $cache
     * @param Adapter\AdapterAbstract $adapter
     * @param Helper\Catalog $catalogHelper
     */
    public function __construct(
        \Magento\Framework\App\Config\ScopeConfigInterface $config,
        \Magento\Shipping\Model\Shipping $shipping,
        \Magento\Tax\Helper\Data $taxHelper,
        \Magento\Customer\Model\AddressFactory $addressFactory,
        \Magento\Directory\Model\Region $region,
        \RocketWeb\ShoppingFeeds\Model\Generator\Cache $cache,
        Adapter\AdapterAbstract $adapter,
        \RocketWeb\ShoppingFeeds\Model\Product\Helper\Catalog $catalogHelper
    )
    {
        $this->config = $config;
        $this->adapter = $adapter;
        $this->shipping = $shipping;
        $this->taxHelper = $taxHelper;
        $this->addressFactory = $addressFactory;
        $this->region = $region;
        $this->cache = $cache;
        $this->catalogHelper = $catalogHelper;
    }

    public function setRequest(\Magento\Catalog\Model\Product $product)
    {
        /** cache this */
        if ($this->cache->getCache('shipping/origin/country_id', false) === false) {
            $this->cache->setCache('shipping/origin/country_id', $this->config->getValue('shipping/origin/country_id', \Magento\Store\Model\ScopeInterface::SCOPE_STORE));
            $this->cache->setCache('shipping/origin/region_id', $this->config->getValue('shipping/origin/region_id', \Magento\Store\Model\ScopeInterface::SCOPE_STORE));
            $this->cache->setCache('shipping/origin/postcode', $this->config->getValue('shipping/origin/postcode', \Magento\Store\Model\ScopeInterface::SCOPE_STORE));
        }
        $shippingOriginCountryId = $this->cache->getCache('shipping/origin/country_id');
        $shippingOriginRegionId = $this->cache->getCache('shipping/origin/region_id');
        $shippingOriginPostal = $this->cache->getCache('shipping/origin/postcode');

        $rateRequest = new \Magento\Quote\Model\Quote\Address\RateRequest();
        $rateRequest->setBaseCurrency($this->adapter->getStore()->getBaseCurrency());
        $rateRequest->setWebsiteId($this->adapter->getStore()->getWebsiteId());
        $rateRequest->setStoreId($this->adapter->getStore()->getStoreId());

        $rateRequest->setOrigCountryId($shippingOriginCountryId)
            ->setOrigRegionId($shippingOriginRegionId)
            ->setOrigPostcode($shippingOriginPostal);

        $price = $product->getPrice();
        $weight = $this->getProductWeight($product);

        $rateRequest->setPackageValue($price)
            ->setPackageWeight($weight)
            ->setFreeMethodWeight($weight)
            ->setPackageQty(1);

        $rateRequest->setOrderTotalQty(1)
            ->setOrderSubtotal($price);

        return $rateRequest;
    }

    public function collectRates(\Magento\Quote\Model\Quote\Address\RateRequest $rateRequest)
    {
        $this->rates = array();
        $allowedCarriers = $this->getAllowedCarriers();

        $ter = $this->getShippingTerritory();
        if (empty($ter)) {
            return $this;
        }

        $onlyMinimumPrice = $this->adapter->getFeed()->getConfig('shipping_only_minimum');
        $onlyFreeShipping = $this->adapter->getFeed()->getConfig('shipping_only_free_shipping');

        if (!empty($allowedCarriers)) {
            foreach ($ter as $countryId => $regions) {
                if (!is_array($regions) || count($regions) == 0) {
                    $regions = [null];
                }

                foreach ($regions as $regionId => $regionCode) {
                    if (is_null($regionCode)) {
                        $regionId = null;
                        $regionCode = '*';
                    }

                    $rateRequest->setDestCountryId($countryId)
                        ->setDestRegionId($regionId);

                    foreach ($allowedCarriers as $carrierCode) {
                        $this->shipping->collectCarrierRates($carrierCode, $rateRequest);
                    }
                    $result = $this->shipping->getResult();
                    if ($onlyMinimumPrice) {
                        $result->sortRatesByPrice();
                    }
                    $result = $result->asArray();
                    if (empty($result)) {
                        continue;
                    }
                    if ($onlyMinimumPrice && is_array($result)) {
                        reset($result);
                        $result = array(key($result) => current($result));
                    }
                    if ($onlyFreeShipping) {
                        $result = $this->filterFreeShipping($result);
                    }
                    $this->rates[$countryId][$regionCode] = $result;
                }
            }
        }

        return $this;
    }

    /**
     * @param array $result
     * @return array
     */
    public function filterFreeShipping($result)
    {
        if (is_array($result)) {
            foreach ($result as $carrierCode => $carrier) {
                if (is_array($carrier) && isset($carrier['methods']) && is_array($carrier['methods'])) {
                    foreach ($carrier['methods'] as $methodCode => $method) {
                        if ($method['price'] > 0) {
                            unset($result[$carrierCode]['methods'][$methodCode]);
                        }
                    }
                    if (count($result[$carrierCode]['methods']) == 0) {
                        unset($result[$carrierCode]);
                    }
                }
            }
        }

        return $result;
    }

    /**
     * @return array|string
     */
    public function getFormatedValue()
    {
        if (!is_array($this->rates) || count($this->rates) == 0) {
            return '';
        }

        $this->rates = $this->minimiseData($this->rates);
        $values = array();
        foreach ($this->rates as $countryId => $regions) {
            if (empty($regions)) {
                continue;
            }

            if (is_array($regions)) {
                foreach ($regions as $regionId => $carriers) {
                    if ($regionId == "*") {
                        if (is_array($carriers)) {
                            foreach ($carriers as $carrier) {
                                if (is_array($carrier) && isset($carrier['methods']) && is_array($carrier['methods'])) {
                                    foreach ($carrier['methods'] as $method) {
                                        $price = sprintf(
                                            "%.2F %s",
                                            $this->getPriceTax($method['price'], $countryId),
                                            $this->adapter->getData('store_currency_code')
                                        );
                                        $values[] = sprintf("%s::%s:%s", $countryId, $this->getShippingTitle($carrier, $method), $price);
                                    }
                                }
                            }
                        }
                        break; // Only 1 set of carriers for countries without shipping vary by region.
                    } else {
                        if (is_array($carriers)) {
                            foreach ($carriers as $carrier) {
                                if (is_array($carrier) && isset($carrier['methods']) && is_array($carrier['methods'])) {
                                    foreach ($carrier['methods'] as $methodCode => $method) {
                                        $price = sprintf(
                                            "%.2F %s",
                                            $this->getPriceTax($method['price'], $countryId),
                                            $this->adapter->getData('store_currency_code')
                                        );
                                        $values[] = sprintf("%s:%s:%s:%s", $countryId, $regionId, $this->getShippingTitle($carrier, $method), $price);
                                    }
                                }
                            }
                        }
                    }
                }
            }
        }
        $values = implode(",", $values);
        return $values;
    }

    /**
     * Get title slot from shipping format ::{title}:
     *
     * @param  $carrier
     * @param  $method
     * @return string
     */
    protected function getShippingTitle($carrier, $method)
    {
        $title = '';

        if ($method['title'] != "*" && (!empty($carrier['title']) || !empty($method['title']))) {
            if (!empty($carrier['title'])) {
                $title .= $carrier['title'];
            }
            if (!empty($method['title'])) {
                $title .= empty($title) ? $method['title'] : ' - ' . $method['title'];
            }
        }
        return $title;
    }

    /**
     * @param $price
     * @param null $countryId
     * @param null $regionId
     * @return float
     */
    protected function getPriceTax($price, $countryId = null, $regionId = null)
    {
        $ret = $price;

        // Apply if there is a tax for shipping
        if ($this->taxHelper->getShippingTaxClass($this->adapter->getStore()->getStoreId())) {
            $includingTax = ($this->adapter->getFeed()->getConfig('shipping_add_tax_to_price') ? true : false);

            // Mock quote object, needed only for getBillingAddress()
            $quote = new \Magento\Framework\DataObject();
            /** @var \Magento\Customer\Model\Address $address */
            $address = $this->addressFactory->create();

            if (!is_null($countryId)) {
                $address->setCountryId($countryId);
                if (!is_null($regionId) && $regionId != "*") {
                    $address->setRegionId($regionId);
                }
            }

            $billingAddress = clone $address;
            $shippingAddress = clone $address;
            $quote->setData('billing_address', $billingAddress);
            $shippingAddress->setQuote($quote);

            $ret = $this->getShippingTaxPrice(
                $price, $includingTax, $shippingAddress, null, $this->adapter->getStore()->getStoreId());
        }
        $ret = $this->adapter->convertPrice($ret);

        return $ret;
    }

    /**
     * Transform rates from:
     *    US:CA:x:99 and US:NY:x:99 to US:*:x:99
     *
     * @param  array $rates
     * @return array
     */
    public function minimiseData($rates)
    {
        $ret = $rates;
        if (empty($ret)) {
            return $ret;
        }

        /* Compress by regions
           US:CA:x:99 and US:NY:x:99 to US::x:99 */
        foreach ($rates as $countryId => $regions) {
            if (empty($regions)) {
                continue;
            }

            // Find all methods in all regions
            $allMethods = array();
            foreach ($regions as $regionId => $carriers) {
                if ($regionId != "*") {
                    if (is_array($carriers)) {
                        foreach ($carriers as $carrierCode => $carrier) {
                            if (is_array($carrier) && isset($carrier['methods']) && is_array($carrier['methods'])) {
                                foreach ($carrier['methods'] as $methodCode => $method) {
                                    $code = $carrierCode . '~' . $methodCode;
                                    if (!isset($allMethods[$code])) {
                                        $allMethods[$code] = $code;
                                    }
                                }
                            }
                        }
                    }
                }
            }

            // Find which methods have same prices in all regions ($same) and transform $ret: for each method in all regions with same price => set method in region_id == *.
            $same = array();
            foreach ($allMethods as $code => $v) {
                $same[$code] = PHP_INT_MAX;
            }

            foreach ($regions as $regionId => $carriers) {
                if ($regionId != "*") {
                    $allRegionMethods = array();
                    if (is_array($carriers)) {
                        foreach ($carriers as $carrierCode => $carrier) {
                            if (is_array($carrier) && isset($carrier['methods']) && is_array($carrier['methods'])) {
                                foreach ($carrier['methods'] as $methodCode => $method) {
                                    $code = $carrierCode . '~' . $methodCode;
                                    $allRegionMethods[$code] = $code;
                                    if ($same[$code] == PHP_INT_MAX) {
                                        $same[$code] = $method['price'];
                                    } elseif ($same[$code] !== false && $same[$code] != $method['price']) {
                                        $same[$code] = false;
                                    }
                                }
                            }
                        }
                    }

                    $missing = array_diff($allMethods, $allRegionMethods);
                    foreach ($missing as $code => $v) {
                        $same[$code] = false;
                    }
                }
            }

            foreach ($same as $code => $v) {
                if ($same[$code] === false) {
                    unset($same[$code]);
                }
            }

            // Move every redundant method to *
            if (count($same) > 0) {
                foreach ($regions as $regionId => $carriers) {
                    if ($regionId != "*") {
                        if (is_array($carriers)) {
                            foreach ($carriers as $carrierCode => $carrier) {
                                if (is_array($carrier) && isset($carrier['methods']) && is_array($carrier['methods'])) {
                                    foreach ($carrier['methods'] as $methodCode => $method) {
                                        $code = $carrierCode . '~' . $methodCode;
                                        if (isset($same[$code])) {
                                            unset($ret[$countryId][$regionId][$carrierCode]['methods'][$methodCode]);

                                            // move once to *
                                            if (!isset($ret[$countryId]["*"])) {
                                                $ret[$countryId]["*"] = array();
                                            }
                                            if (!isset($ret[$countryId]["*"][$carrierCode])) {
                                                $ret[$countryId]["*"][$carrierCode] = array(
                                                    'title' => $carrier['title'],
                                                    'methods' => array()
                                                );
                                            }
                                            if (!isset($ret[$countryId]["*"][$carrierCode]['methods'][$methodCode])) {
                                                $ret[$countryId]["*"][$carrierCode]['methods'][$methodCode] = $method;
                                            }
                                        }
                                    }

                                    if (empty($ret[$countryId][$regionId][$carrierCode]['methods'])) {
                                        unset($ret[$countryId][$regionId][$carrierCode]);
                                    }
                                }
                            }
                        }
                    }

                    if (empty($ret[$countryId][$regionId])) {
                        unset($ret[$countryId][$regionId]);
                    }
                }
            }
        }

        return $ret;
    }

    /**
     * @return array
     */
    public function getAllowedCarriers()
    {
        $realtimeCarriers = $this->adapter->getFeed()->getConfig('shipping_carrier_realtime', []);
        $methods = $this->adapter->getFeed()->getConfig('shipping_methods');
        $allowedCarriers = array();
        foreach ($methods as $m) {
            if (!empty($m)) {
                $allowedCarriers[] = substr($m, 0, strpos($m, "_"));
            }
        }
        $allowedCarriers = array_unique($allowedCarriers);
        $allowedCarriers = array_diff($allowedCarriers, $realtimeCarriers);

        return $allowedCarriers;
    }

    /**
     * @return array|null
     */
    public function getShippingTerritory()
    {
        if (is_null($this->shippingTerritory)) {
            $this->shippingTerritory = array();
            $allowedCountries = $this->adapter->getFeed()->getConfig('shipping_country');
            if (empty($allowedCountries)) {
                return $this->shippingTerritory;
            }
            $shippingByRegion = $this->adapter->getFeed()->getConfig('shipping_by_region');
            $allowedCountryWithRegions = array();
            if ($shippingByRegion) {
                $allowedCountryWithRegions = $this->adapter->getFeed()->getConfig('shipping_country_with_region');
                $allowedCountryWithRegions = empty($allowedCountryWithRegions) ? $allowedCountries : array_intersect($allowedCountryWithRegions, $allowedCountries);

            }

            foreach ($allowedCountries as $countryId) {
                $this->shippingTerritory[$countryId] = [];
            }

            if (count($allowedCountryWithRegions) > 0) {

                /** @var \Magento\Directory\Model\ResourceModel\Region\Collection $regionsCollection */
                $regionsCollection = $this->region->getCollection();
                $regionsCollection->addCountryFilter($allowedCountryWithRegions);

                $countryRegions = array();
                foreach ($regionsCollection as $region) {
                    /** @var \Magento\Directory\Model\Region $region */
                    $countryRegions[$region->getCountryId()][$region->getId()] = $region->getCode();
                }
                unset($regionsCollection);

                foreach ($allowedCountryWithRegions as $countryId) {
                    $this->shippingTerritory[$countryId] = array();
                    if ($shippingByRegion && isset($countryRegions[$countryId]) && count($countryRegions[$countryId]) > 0) {
                        $this->shippingTerritory[$countryId] = $countryRegions[$countryId];
                    }
                }
            }
        }
        return $this->shippingTerritory;
    }

    /**
     * Get shipping price
     * This method copies method getShippingPrice from \Magento\Tax\Helper\Data,
     * but uses another helper to push price calculation
     *
     * @param  float                      $price
     * @param  bool|null                  $includingTax
     * @param  Address|null               $shippingAddress
     * @param  int|null                   $ctc
     * @param  null|string|bool|int|Store $store
     * @return float
     */
    protected function getShippingTaxPrice(
        $price,
        $includingTax = null,
        $shippingAddress = null,
        $ctc = null,
        $store = null
    )
    {
        $pseudoProduct = new \Magento\Framework\DataObject();
        $pseudoProduct->setTaxClassId($this->taxHelper->getShippingTaxClass($store));

        $billingAddress = false;
        if ($shippingAddress && $shippingAddress->getQuote() && $shippingAddress->getQuote()->getBillingAddress()) {
            $billingAddress = $shippingAddress->getQuote()->getBillingAddress();
        }

        $price = $this->catalogHelper->getTaxPrice(
            $pseudoProduct,
            $price,
            $includingTax,
            $shippingAddress,
            $billingAddress,
            $ctc,
            $store,
            $this->taxHelper->shippingPriceIncludesTax($store)
        );

        return $price;
    }

    /**
     * Compute product weight based on mappings
     *
     * @param \Magento\Catalog\Model\Product $product
     * @return int
     */
    public function getProductWeight(\Magento\Catalog\Model\Product $product)
    {
        $cacheKey = ['shipping', 'map', 'weight', 'product', $product->getId()];
        if (($weight = $this->cache->getCache($cacheKey, false)) !== false) {
            return $weight;
        }

        $weight = $product->getWeight();

        $shippingColumn = $this->adapter->getFeed()->getConfig('shipping_weight_column', '');
        if (!empty($shippingColumn)) {

            $columnsMap = $this->adapter->getFeed()->getColumnsMap();
            $shippingColumnMap = null;
            foreach ($columnsMap as $map) {
                if ($map['column'] == $shippingColumn) {
                    $shippingColumnMap = $map;
                    break;
                }
            }
            if (!is_null($shippingColumnMap) && isset($shippingColumnMap['param'])) {
                $unit = $shippingColumnMap['param'];

                try {
                    $weight = $this->adapter->getMapValue($shippingColumnMap);
                } catch (\Exception $e) {
                    $weight = 1;
                }
                if ($unit != '') {
                    $weight = trim(str_replace($unit, '', $weight));
                }
            } else {
                $weight = 1;
            }
        }
        $weight = intval($weight);

        $this->cache->setCache($cacheKey, $weight);
        return $weight;
    }
}
