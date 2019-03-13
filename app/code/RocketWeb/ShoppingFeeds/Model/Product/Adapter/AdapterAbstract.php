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

namespace RocketWeb\ShoppingFeeds\Model\Product\Adapter;

use Magento\Framework\Filesystem;
use Magento\Framework\App\Filesystem\DirectoryList;
use RocketWeb\ShoppingFeeds\Model\Exception as FeedException;

/**
 *
 * Class AdapterAbstract
 * @package RocketWeb\ShoppingFeeds\Model\Product\Adapter
 *
 * @method  boolean hasParentAdapter()
 * @method  $this   setParentAdapter($adapter)
 * @method  $this   getParentAdapter()
 */
class AdapterAbstract extends \Magento\Framework\DataObject
{
    const DEFAULT_CHILDREN_COUNT = 1;

    /**
     * Store object
     *
     * @var null|\Magento\Store\Model\Store
     */
    protected $storeObject = null;

    /**
     * Feed object
     *
     * @var null|\RocketWeb\ShoppingFeeds\Model\Feed
     */
    protected $feed = null;

    /**
     * Product object
     *
     * @var null|\Magento\Catalog\Model\Product
     */
    protected $product;

    /**
     * @var \RocketWeb\ShoppingFeeds\Model\FeedTypes\Config
     */
    protected $feedTypesConfig;

    /**
     * @var \RocketWeb\ShoppingFeeds\Model\Product\Mapper\MapperFactory
     */
    protected $mapperFactory;

    /**
     * @var \RocketWeb\ShoppingFeeds\Model\Product\Formatter\FormatterFactory
     */
    protected $formatterFactory;

    /**
     * @var \RocketWeb\ShoppingFeeds\Model\Product\Helper
     */
    protected $helper;

    /**
     * @var \Magento\Weee\Helper\Data
     */
    protected $weeeHelper;

    /**
     * @var \Magento\Tax\Helper\Data
     */
    protected $taxHelper;

    /**
     * @var \RocketWeb\ShoppingFeeds\Model\Product\Helper\Catalog
     */
    protected $catalogHelper;

    /**
     * @var \Magento\CatalogRule\Model\ResourceModel\Rule\Product\Price\CollectionFactory
     */
    protected $catalogRuleCollectionFactory;

    /**
     * @var \Magento\Catalog\Model\Product\Type\Price
     */
    protected $productTypePrice;


    protected $logger;

    /**
     * @var \RocketWeb\ShoppingFeeds\Model\Product\Filter
     */
    protected $filter;

    /**
     * @var \Magento\Framework\Locale\Resolver
     */
    protected $localeResolver;

    /**
     * DateTime
     *
     * @var \Magento\Framework\Stdlib\DateTime\Timezone
     */
    protected $timezone;

    /**
     * @var boolean
     */
    protected $isTestMode = false;

    /**
     * DateTime
     *
     * @var \Magento\Framework\Stdlib\DateTime
     */
    protected $date;

    /**
     * @var \RocketWeb\ShoppingFeeds\Model\Product\OptionFactory
     */
    protected $optionFactory;

    /**
     * @var \RocketWeb\ShoppingFeeds\Model\Product\Option
     */
    protected $option;

    /**
     * @var Filesystem
     */
    protected $filesystem;

    /**
     * @var \RocketWeb\ShoppingFeeds\Model\Generator\Cache
     */
    protected $cache;

    /**
     * @var \RocketWeb\ShoppingFeeds\Model\Generator\ProcessFactory
     */
    protected $processFactory;

    /**
     * @var \RocketWeb\ShoppingFeeds\Model\ResourceModel\Generator\Process\CollectionFactory
     */
    protected $processCollectionFactory;

    /**
     * @var \Magento\CatalogInventory\Model\StockState
     */
    protected $stockState;

    /**
     * @var AdapterFactory
     */
    protected $adapterFactory;

    /**
     * AdapterAbstract constructor.
     * @param Filesystem $filesystem
     * @param \RocketWeb\ShoppingFeeds\Model\Feed $feed
     * @param \Magento\Catalog\Model\Product $product
     * @param \RocketWeb\ShoppingFeeds\Model\FeedTypes\Config $feedTypesConfig
     * @param \RocketWeb\ShoppingFeeds\Model\Product\Mapper\MapperFactory $mapperFactory
     * @param \RocketWeb\ShoppingFeeds\Model\Product\Helper $helper
     * @param \Magento\Weee\Helper\Data $weeeData
     * @param \Magento\Tax\Helper\Data $taxData
     * @param \RocketWeb\ShoppingFeeds\Model\Product\Helper\Catalog $catalogHelper
     * @param \Magento\CatalogRule\Model\ResourceModel\Rule\Product\Price\CollectionFactory $catalogRuleCollectionFactory
     * @param \Magento\Catalog\Model\Product\Type\Price $productTypePrice
     * @param \Magento\CatalogInventory\Model\StockState $stockState
     * @param \RocketWeb\ShoppingFeeds\Model\Product\Filter $filter
     * @param \Magento\Framework\Locale\Resolver $localeResolver
     * @param \Magento\Framework\Stdlib\DateTime\Timezone $timezone
     * @param \Magento\Framework\Stdlib\DateTime $date
     * @param \RocketWeb\ShoppingFeeds\Model\Product\OptionFactory $optionFactory
     * @param \RocketWeb\ShoppingFeeds\Model\Logger $logger
     * @param \RocketWeb\ShoppingFeeds\Model\Generator\Cache $cache
     * @param AdapterFactory $adapterFactory
     * @param array $data
     * @param \RocketWeb\ShoppingFeeds\Model\Product\Formatter\FormatterFactory $formatterFactory
     */
    public function __construct(
        Filesystem $filesystem,
        \RocketWeb\ShoppingFeeds\Model\Feed $feed,
        \Magento\Catalog\Model\Product $product,
        \RocketWeb\ShoppingFeeds\Model\FeedTypes\Config $feedTypesConfig,
        \RocketWeb\ShoppingFeeds\Model\Product\Mapper\MapperFactory $mapperFactory,
        \RocketWeb\ShoppingFeeds\Model\Product\Helper $helper,
        \Magento\Weee\Helper\Data $weeeData,
        \Magento\Tax\Helper\Data $taxData,
        \RocketWeb\ShoppingFeeds\Model\Product\Helper\Catalog $catalogHelper,
        \Magento\CatalogRule\Model\ResourceModel\Rule\Product\Price\CollectionFactory $catalogRuleCollectionFactory,
        \Magento\Catalog\Model\Product\Type\Price $productTypePrice,
        \Magento\CatalogInventory\Model\StockState $stockState,
        \RocketWeb\ShoppingFeeds\Model\Product\Filter $filter,
        \Magento\Framework\Locale\Resolver $localeResolver,
        \Magento\Framework\Stdlib\DateTime\Timezone $timezone,
        \Magento\Framework\Stdlib\DateTime $date,
        \RocketWeb\ShoppingFeeds\Model\Product\OptionFactory $optionFactory,
        \RocketWeb\ShoppingFeeds\Model\Generator\ProcessFactory $processFactory,
        \RocketWeb\ShoppingFeeds\Model\ResourceModel\Generator\Process\CollectionFactory $processCollectionFactory,
        \RocketWeb\ShoppingFeeds\Model\Logger $logger,
        \RocketWeb\ShoppingFeeds\Model\Generator\Cache $cache,
        \RocketWeb\ShoppingFeeds\Model\Product\Adapter\AdapterFactory $adapterFactory,
        \RocketWeb\ShoppingFeeds\Model\Product\Formatter\FormatterFactory $formatterFactory,
        array $data = []
    ) {
        $this->setFeed($feed);
        $this->setProduct($product);
        $this->logger = $logger;
        $this->stockState = $stockState;
        $this->adapterFactory = $adapterFactory;
        $this->filesystem = $filesystem;
        $this->helper = $helper;
        $this->weeeHelper = $weeeData;
        $this->taxHelper = $taxData;
        $this->catalogHelper = $catalogHelper;
        $this->catalogRuleCollectionFactory = $catalogRuleCollectionFactory;
        $this->productTypePrice = $productTypePrice;
        $this->mapperFactory = $mapperFactory;
        $this->feedTypesConfig = $feedTypesConfig;
        $this->filter = $filter;
        $this->filter->setFeed($feed);
        $this->localeResolver = $localeResolver;
        $this->timezone = $timezone;
        $this->date = $date;
        $this->optionFactory = $optionFactory;
        $this->processFactory = $processFactory;
        $this->processCollectionFactory = $processCollectionFactory;
        $this->cache = $cache;
        $this->formatterFactory = $formatterFactory;

        parent::__construct($data);
        
        $this->setAdapterData();
    }

    protected function setAdapterData()
    {
        $this->setData('store_currency_code', $this->getStore()->getCurrentCurrency()->getCode());
        $this->setData('images_url_prefix', $this->getStore()->getBaseUrl(\Magento\Framework\UrlInterface::URL_TYPE_MEDIA, false) . 'catalog/product');
        $this->setData('images_path_prefix', $this->filesystem->getDirectoryRead(DirectoryList::MEDIA)->getAbsolutePath());
    }

    /**
     * Gets value either from directive method or attribute method.
     *
     * @param  array $column
     * @return mixed
     */
    public function getMapValue(array $column = [])
    {
        $cacheKey = ['row', 'map', 'product', $this->getProduct()->getId(), 'column', $column['column']];
        if ($this->cache->getCache($cacheKey, false) === false) {
            $feedType = $this->feed->getData('type');

            if ($this->feedTypesConfig->isAllowedDirective($feedType, $column['attribute'])
                && !isset($column['skip_directive'])
            ) {
                $directive = $this->feedTypesConfig->getDirective($feedType, $column['attribute']);
                $mapper = $this->mapperFactory->create($directive, $this);
                $value = $mapper->map($column);
                $mapper->popAdapter();
            } else {
                $attribute = $this->getMapAttribute($column);
                $value = $this->getAttributeValue($this->product, $attribute);
                $value = $this->getFilter()->cleanField($value, $column);
            }

            if ($value == '') {
                $value = $this->mapEmptyValues($column);
            }

            $this->cache->setCache($cacheKey, $value);
        } else {
            $value = $this->cache->getCache($cacheKey);
        }

        return $value;
    }

    /**
     * Pull price & special price with & without tax
     *
     * @return array
     */
    public function getPrices()
    {
        if ($this->hasData('price_array')) {
            return $this->getData('price_array');
        }

        /** @var \Magento\Weee\Helper\Data $weeeHelper */
        $weeeHelper = $this->weeeHelper;
        /** @var \Magento\Tax\Helper\Data $taxHelper */
        $taxHelper = $this->taxHelper;
        /** @var \RocketWeb\ShoppingFeeds\Model\Product\Helper\Catalog $catalogHelper */
        $catalogHelper = $this->catalogHelper;
        $helper = $this->helper;

        $store = $this->getStore();
        $algorithm = $taxHelper->getConfig()->getAlgorithm($store);

        /** @var \Magento\Catalog\Model\Product $product */
        $product = $this->product;

        $qtyIncrements = $helper->getQuantityIcrements(
            ($this->hasParentAdapter() ? $this->getParentAdapter()->getProduct() : $product), $this->getFeed()
        );

        $weeeExcludingTax = $weeeHelper->getAmountExclTax($product);
        $weeeIncludingTax = $weeeExcludingTax;
        if ($weeeHelper->isTaxable()) {
            $amount = 0;
            $attributes = $weeeHelper->getProductWeeeAttributesForRenderer($product, null, null, null, true);
            foreach ($attributes as $attribute) {
                $amount += $attribute->getAmount();
            }
            $weeeIncludingTax = $amount;
        }

        $prices = $this->getProductPrices($product);

        if ($algorithm !== \Magento\Tax\Model\Calculation::CALC_UNIT_BASE && $qtyIncrements > 1.0) {
            // We need to multiply base before calculating tax for whole ((itemPrice * qty) + vat = total)
            $prices['p_excl_tax'] *= $qtyIncrements;
            $prices['p_incl_tax'] = $catalogHelper->getTaxPrice($product, $prices['p_excl_tax'], true);

            $prices['sp_excl_tax'] *= $qtyIncrements;
            $prices['sp_incl_tax'] = $catalogHelper->getTaxPrice($product, $prices['sp_excl_tax'], true);
        } else if ($qtyIncrements > 1.0) {
            // We just need to multiply incl_tax/excl_tax prices
            foreach ($prices as $code => $price) {
                $prices[$code] = $price * $qtyIncrements;
            }
        }

        foreach ($prices as $code => $price) {
            if (strpos($code, '_incl_') !== false) {
                $price = $price + $weeeIncludingTax;
            } else {
                $price = $price + $weeeExcludingTax;
            }
            $prices[$code] = $price;
        }

        $this->setData('price_array', $prices);
        return $this->getData('price_array');

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
            $locale = $this->localeResolver->getLocale();
            if ($specialPrice > 0) {
                $cDate = $this->timezone->date(null, $locale);
                $dates = $this->getSpecialPriceEffectiveDates($product, false);
                /**
                 * @var \DateTime $start
                 * @var \DateTime $end
                 */
                extract($dates);

                if ($start <= $cDate && $end >= $cDate && $specialPrice < $product->getPrice()) {
                    $has = true;
                }
            }
        }

        return $has;
    }

    /**
     * @return float|int
     */
    public function getInventoryCount()
    {
        $stockState = $this->stockState;
        $stockQty = $stockState->getStockQty($this->product->getId(), $this->getStore()->getWebsiteId());
        return $stockQty > 0 ? $stockQty : 0;
    }

    /**
     * Get an array of sale price effective dates from catalog rules or product's special price
     *
     * @return false|array(\DateTime, \DateTime)
     */
    public function getSalePriceEffectiveDates()
    {
        $product = $this->product;

        if ($this->hasPriceByCatalogRules($product)) {
            return $this->getCatalogRuleEffectiveDates($product);
        } else if ($this->hasSpecialPrice(false)) {
            return $this->getSpecialPriceEffectiveDates($product);
        }

        return false;
    }

    /**
     * Get the price array (regular/special + incuding/excluding tax)
     *
     * @param \Magento\Catalog\Model\Product $product
     * @return array
     */
    protected function getProductPrices(\Magento\Catalog\Model\Product $product)
    {
        /** @var \RocketWeb\ShoppingFeeds\Model\Product\Helper\Catalog */
        $catalogHelper = $this->catalogHelper;

        $prices = array();
        // Compute equivalent to default/template/catalog/product/price.phtml
        $price = $product->getPrice();
        $convertedPrice = $this->convertPrice($price);
        $prices['p_excl_tax'] = $catalogHelper->getTaxPrice($product, $convertedPrice);
        $prices['p_incl_tax'] = $catalogHelper->getTaxPrice($product, $convertedPrice, true);

        $catalogRulesPrice = $this->getPriceByCatalogRules();
        $finalPrice = $catalogRulesPrice ? min($catalogRulesPrice, $product->getFinalPrice()) : $product->getFinalPrice();
        $convertedFinalPrice = $this->convertPrice($finalPrice);

        $prices['sp_excl_tax'] = $catalogHelper->getTaxPrice($product, $convertedFinalPrice);
        $prices['sp_incl_tax'] = $catalogHelper->getTaxPrice($product, $convertedFinalPrice, true);

        return $prices;
    }


    /**
     * @param null|$product
     * @return bool
     */
    public function hasPriceByCatalogRules($product = null)
    {
        $has = false;
        if (is_null($product)) {
            $product = $this->product;
        }

        if ($this->getFeed()->getConfig('general_apply_catalog_price_rules')) {
            $rulesPrice = $this->getPriceByCatalogRules();

            if (round($product->getPrice(), 2) != round($rulesPrice, 2)) {
                $specialPrice = $product->getSpecialPrice();
                $hasSpecialPrice = $this->hasSpecialPrice(false);

                if ($hasSpecialPrice && $specialPrice > 0 && floatval($specialPrice) <= floatval($rulesPrice)) {
                    $has = false;
                } else {
                    $has = true;
                }
            }
        }

        return $has;
    }

    /**
     * Retrieves the start and end date for the product's special price, if they exist.
     *
     * @see self::hasSpecialPrice() - you should check to see if the product is using a special price
     *
     * @param  \Magento\Catalog\Model\Product $product
     * @return false|array
     */
    protected function getSpecialPriceEffectiveDates($product, $returnEmpty = true)
    {
        $specialFromDate = $product->getSpecialFromDate();
        $specialToDate = $product->getSpecialToDate();

        if (empty($specialFromDate) && empty($specialToDate) && $returnEmpty) {
            return false;
        }

        $locale = $this->localeResolver->getLocale();

        $fromDate = null;
        if (!$this->date->isEmptyDate($specialFromDate)) {
            $fromDate = \DateTime::createFromFormat('Y-m-d H:i:s', $specialFromDate);
        }

        /** @var \DateTime $fromDate */
        $fromDate = $this->timezone->date($fromDate, $locale);

        /** @var \DateTime $toDate */
        if (!$this->date->isEmptyDate($specialToDate)) {
            $toDate = \DateTime::createFromFormat('Y-m-d H:i:s', $specialToDate);
        } else {
            $toDate = clone $fromDate;
            $toDate->add(new \DateInterval('P5Y'));
        }

        return [
            'start' => $fromDate,
            'end' => $toDate
        ];
    }

    /**
     * Retrieves the start and end date for the catalog rule that applies to the product.
     * If there's no rule, or if the rule doesn't have dates, it defaults 365 days
     *
     * @see self::hasPriceByCatalogRules() - you should first check if the product has catalog rules
     *
     * @param  \Magento\Catalog\Model\Product $product
     * @return false|\DateTime[]
     */
    protected function getCatalogRuleEffectiveDates($product)
    {
        /** @var \DateTime $date */
        $date = $this->timezone->date();

        /** @var \Magento\CatalogRule\Model\ResourceModel\Rule\Product\Price\Collection $catalogRuleCollection */
        $catalogRuleCollection = $this->catalogRuleCollectionFactory->create();
        $catalogRuleCollection->addFieldToFilter('rule_date', ['eq' => $this->date->formatDate($date, false)])
            ->addFieldToSelect(['latest_start_date', 'earliest_end_date'])
            ->addFieldToFilter('website_id', ['eq' => $this->getStore()->getWebsiteId()])
            ->addFieldToFilter('product_id', ['eq' => $product->getId()])
            ->addFieldToFilter('rule_price', ['eq' => $this->getPriceByCatalogRules()])
            ->addFieldToFilter('customer_group_id', ['eq' => \Magento\Customer\Model\Group::NOT_LOGGED_IN_ID]);

        /** @var \Magento\CatalogRule\Model\Rule $rule */
        $rule = $catalogRuleCollection->getFirstItem();
        if ($rule->getData('latest_start_date')) {
            $fromDate = \DateTime::createFromFormat('Y-m-d', $rule->getData('latest_start_date'));
            $fromDate->setTime(1, 0);
        } else {
            $fromDate = null;
        }

        if ($rule->getData('earliest_end_date')) {
            $toDate = \DateTime::createFromFormat('Y-m-d', $rule->getData('earliest_end_date'));
            $toDate->setTime(1, 0);
        } else {
            $toDate = null;
        }

        if (is_null($fromDate)) {
            $fromDate = $date;
        }

        if (is_null($toDate)) {
            $toDate = $date;
            $toDate->add(new \DateInterval('P1Y'));
        }

        return [
            'start' => $fromDate,
            'end' => $toDate
        ];
    }

    /**
     * When computing the special price, we send the $price parameter from associated items
     * @return mixed
     */
    protected function getPriceByCatalogRules($price = null)
    {
        if (is_null($price)) {
            $price = $this->product->getPrice();
        }

        return $this->productTypePrice->calculatePrice(
            $price,
            0.0, false, false, false,
            $this->getStore()->getWebsiteId(),
            \Magento\Customer\Model\Group::NOT_LOGGED_IN_ID,
            $this->product->getId()
        );
    }

    /**
     * @param array $column
     * @return false|\Magento\Eav\Model\Entity\Attribute\AbstractAttribute
     * @throws FeedException
     */
    public function getMapAttribute($column = array())
    {
        if (!is_array($column)) {
            $column = ['attribute' => $column];
        }

        $attributeCode = $column['attribute'];
        $resource = $this->product->getResource();
        $attribute = $resource->getAttribute($attributeCode);

        if ($attribute === false) {
            throw new FeedException(
                new \Magento\Framework\Phrase(sprintf('Couldn\'t find attribute \'%s\'.', $column['attribute']))
            );
        }
        return $attribute;
    }

    /**
     * @param \Magento\Catalog\Model\Product $product
     * @param \Magento\Eav\Model\Entity\Attribute $attribute
     * @throws FeedException
     * @return string
     */
    public function getAttributeValue($product, $attribute)
    {
        if ($attribute->getSourceModel() == 'Magento\Eav\Model\Entity\Attribute\Source\Boolean') {
            $attributeCode = $attribute->getAttributeCode();
            $value = $product->getData($attributeCode) ? 'Yes' : 'No';
        }
        elseif ($attribute->getFrontendInput() == "select" || $attribute->getFrontendInput() == "multiselect") {
            $value = $this->getAttributeSelectValue($product, $attribute, $this->getStore()->getId());
        } else {
            $value = $product->getData($attribute->getAttributeCode());
        }

        if (is_array($value)) {
            $value = implode(',', $value);
        } else {
            $value = (string) $value;
        }

        if (!is_string($value)) {
            throw new FeedException(
                new \Magento\Framework\Phrase(
                    sprintf('Attribute %s returned non-string value for product SKU #%s',
                    $attribute->getAttributeCode(), $product->getSku())
                )
            );
        }

        return $value;
    }

    /**
     * Gets option text value from product for attributes with frontend_type select.
     * Multiselect values are by default imploded with comma.
     * By default gets option text from admin store (recommended - english values in feed).
     *
     * @param  \Magento\Catalog\Model\Product $product
     * @param \Magento\Eav\Model\Entity\Attribute $attribute
     * @param int|null $store_id
     *
     * @return string
     */
    protected function getAttributeSelectValue($product, $attribute, $storeId = null)
    {
        $options = $attribute->getOptions();
        $productAttributeData = $product->getData($attribute->getAttributeCode());

        if (is_array($productAttributeData) && count($productAttributeData) > 1) {
            $attributeOptions = array();
            foreach ($productAttributeData as $attributeValue) {
                foreach ($options as $option) {
                    if ($attributeValue == $option->getValue()) {
                        $labels = $option->getStoreLabels();
                        if (isset($labels[$storeId])) {
                            $label = $labels[$storeId];
                        } else {
                            $label = $option->getLabel();
                        }
                        $attributeOptions[] = $label;
                    }
                }
            }
            return implode(', ', $attributeOptions);
        }
        if (is_array($productAttributeData)) {
            $productAttributeData = $productAttributeData[0];
        }
        foreach ($options as $option) {
            if ($productAttributeData == $option->getValue()) {
                return $option->getLabel();
            }
        }

        return '';
    }

    /**
     * @param $args
     * @return mixed|string
     */
    public function mapEmptyValues($args)
    {
        $value = '';
        $column = $args['column'];
        $columnsMapReplaced = $this->hasData('columns_map_replaced') ? $this->getData('columns_map_replaced') : [];

        // Avoid infinite loop, and not process if already replaced
        if ( isset($columnsMapReplaced[$column]) && array_key_exists('empty_replaced', $columnsMapReplaced[$column])) {
            return $value;
        }

        $emptyColumnsReplaceMaps = $this->feed->getConfig('filters_map_replace_empty_columns', []);
        if (is_array($emptyColumnsReplaceMaps) && count ($emptyColumnsReplaceMaps) > 0) {

            // Go through replacement rules and pick the one matching current column.
            foreach ($emptyColumnsReplaceMaps as $arr) {
                if ($column == $arr['column']) {

                    $columnsMapReplaced[$column]['empty_replaced'] = true;
                    $this->setData('columns_map_replaced', $columnsMapReplaced);

                    if (!empty($arr['static']) && (!$arr['attribute'] || $arr['attribute'] == 'directive_static_value')) {
                        $value = $arr['static'];
                    } else {
                        $value = $this->getMapValue($arr);
                    }
                }
            }
        }


        return $value;
    }

    /**
     * @param $rows
     * @return $this
     */
    protected function checkEmptyColumns($row)
    {
        $skipEmptyColumn = $this->feed->getConfig('filters_skip_column_empty');

        if (is_array($skipEmptyColumn)) {
            foreach ($skipEmptyColumn as $column) {
                if (isset($row[$column]) && $row[$column] == "") {
                    $this->setSkipProduct(sprintf(
                        "product id %d product sku %s, skipped - by product skip rule, has %s empty.",
                        $this->getProduct()->getId(),
                        $this->getProduct()->getSku(),
                        $column
                    ));
                    break;
                }
            }
        }

        return $this;
    }

    /**
     * Generates feed row(s) based on the given product
     * This is called for each Enabled & Visible (Catalog & Search) product
     *
     * @return array
     */
    public function map()
    {
        $this->beforeMap();
        $rows = $this->internalMap();
        $rows = $this->afterMap($rows);
        return $rows;
    }

    /**
     * Implement product options on top of complex product variants.
     *
     * @return $this
     */
    public function beforeMap()
    {
        $this->setData('skip_message', '');
        $this->setData('skip_product', false);

        return $this;
    }

    /**
     * Forms product's data row. [column] => [value]
     * @return array
     */
    protected function internalMap()
    {
        $rows = [];
        if ($this->isDuplicate()) {
            return $rows;
        }

        // Map current product
        $fields = [];
        foreach ($this->feed->getColumnsMap() as $arr) {
            $column = $arr['column'];
            $row = $this->getMapValue($arr);
            if (isset($fields[$column])) {
                if (is_array($fields[$column])) {
                    $fields[$column][] = $row;
                } else {
                    $fields[$column] = [$fields[$column], $row];
                }
            } else {
                $fields[$column] = $row;
            }
        }
        $rows[] = $fields;

        return $rows;
    }

    /**
     * Checks if option is enabled and if categories match with the product
     *
     * @return bool
     */
    protected function canAddProductOptions()
    {
        $multipleRowsEnabled =
            $this->getFeed()->getConfig('options_mode') == \RocketWeb\ShoppingFeeds\Model\Feed\Source\Product\OptionHandling::MULTIPLE_ROWS;
        if ($multipleRowsEnabled) {
            $categories = $this->getFeed()->getConfig('options_vary_categories', []);
            $matchingCategories = array_intersect($categories, $this->getProduct()->getCategoryIds());
            if (empty($categories) || (count($categories) > 0 && count($matchingCategories) > 0)) {
                return true;
            }
        }
        return false;
    }

    protected function filterAndFormatRows(array $originalRows)
    {
        $feedType = $this->feed->getData('type');
        $rows = $originalRows;

        foreach ($this->feed->getColumnsMap() as $arr) {
            $isAllowed = $this->feedTypesConfig->isAllowedDirective($feedType, $arr['attribute']);
            if (!$isAllowed) {
                continue;
            }

            $column = $arr['column'];
            $directive = $this->feedTypesConfig->getDirective($feedType, $arr['attribute']);
            $mapperData = $this->mapperFactory->getMapperData($directive, $feedType);
            $hasFilter = isset($mapperData['filter']) && $mapperData['filter'];

            if ($hasFilter) {
                $mapper = $this->mapperFactory->create($directive, $this);

                // Is first row parent row?
                $firstRow = $this->hasData('map_parent') && $this->getData('map_parent');
                foreach ($rows as $key => &$row) {
                    $skip = $mapper->filter($row[$column]);
                    if ($skip) {
                        if ($firstRow) {
                            $rows = [];
                            break 2;
                        } else {
                            unset($rows[$key]);
                        }
                    }

                    $firstRow = false;
                }

                if (count($rows) == 0) {
                    $this->setSkipProduct(sprintf('Row skipped - product #%s filtered by column "%s"', $this->getProduct()->getSku(), $column));
                    break;
                }
            }

            $hasFormat = !is_null($this->formatterFactory->getFormatterData($directive, $feedType));

            if ($hasFormat) {
                $formatter = $this->formatterFactory->create($directive, $this);
                foreach ($rows as &$row) {
                    $row[$column] = $formatter->run($row[$column]);
                }
            }
        }

        if (count($rows) == 0) {
            $rows = $originalRows;
        }

        return $rows;
    }

    /**
     * @param $rows
     * @return array
     */
    protected function afterMap(array $rows)
    {
        // Add Product Options if needed
        if ($this->canAddProductOptions()) {
            $rows = $this->getOptionProcessor()->process($rows);
        }

        // If we don't have any rows, they might be skipped, so don't process filters/formats/empty cells
        if (count($rows)) {
            // Format rows (add currency to prices, ...)
            $rows = $this->filterAndFormatRows($rows);

            foreach ($rows as $row) {
                $this->checkEmptyColumns($row);
            }
        }


        // Reset the row cache, leave the feed cache
        $this->cache->setCache('row', []);
        $this->unsetData('mode_parent');
        return $rows;
    }

    /**
     * Catch any undefined currency rate
     *
     * @param $price
     * @return float
     */
    public function convertPrice($price)
    {
        try {
            return $this->getStore()
                ->getBaseCurrency()
                ->convert($price, $this->getStore()->getCurrentCurrency());
        } catch (\Exception $e) {
            return $price;
        }
    }

    public function setSkipProduct($message)
    {
        $this->setData('skip_message', $message);
        $this->setData('skip_product', true);

        return $this;
    }

    /**
     * Check if product is set to be skipped in the Product Edit page
     *
     * @return bool
     */
    public function isSkipped()
    {
        $product = $this->getProduct();
        if ($product->getData('rw_shoppingfeeds_skip_submit') == 1
            || $product->getData('rw_google_base_skip_submi') == 1) {
            $this->setSkipProduct(
                sprintf(
                    "Product ID %d SKU %s, skipped - product has 'Skip from Being Submitted' = 'Yes'.",
                     $product->getId(), $product->getSku()
                )
            );
            return true;
        }

        return (bool)$this->getData('skip_product');
    }

    /**
     * Check if product was already processed (either as a standalone product or associated product of some other)
     *
     * @return bool
     */
    public function isDuplicate()
    {
        // We don't check for duplicates if this is a test mode
        if ($this->isTestMode()) {
            return false;
        }

        /** @var \RocketWeb\ShoppingFeeds\Model\ResourceModel\Generator\Process\Collection $processLookup */
        $processLookup = $this->processCollectionFactory->create()
            ->setFeedFilter($this->getFeed())
            ->setProductFilter($this->getProduct());
        /** @var \RocketWeb\ShoppingFeeds\Model\Generator\Process $process */
        $process = ($processLookup->count()) ? $processLookup->getFirstItem() : $this->processFactory->create();;

        if ($this->hasParentAdapter()) {
            $process->setParentItemId($this->getParentAdapter()->getProduct()->getId());
        }

        if ($process->getId()) {
            if ($process->getStatus() == \RocketWeb\ShoppingFeeds\Model\Generator\Process::STATUS_PROCESSED) {
                $this->setSkipProduct(
                    sprintf('Product SKU %s, ID %d is been omitted because it has been already processed%s',
                        $this->getProduct()->getSku(), $this->getProduct()->getId(),
                        ($process->getParentItemId() > 0
                            ? sprintf(' as part of product ID %s', $process->getParentItemId())
                            : ' as a standalone product'
                        )
                    )
                );
                return true;
            }
        } else {
            $process->setFeedId($this->getFeed()->getId())
                ->setItemId($this->getProduct()->getId());
        }
        $process->setStatus(\RocketWeb\ShoppingFeeds\Model\Generator\Process::STATUS_PROCESSED)
            ->save();

        return false;
    }

    /**
     * Get number of total rows for this adapter
     *
     * @return int
     */
    public function getChildrenCount()
    {
        return self::DEFAULT_CHILDREN_COUNT;
    }

    /**
     * @return \Magento\Store\Model\Store
     * @throws FeedException
     */
    public function getStore()
    {
        if (!$this->storeObject instanceof \Magento\Store\Model\Store) {
            throw new FeedException(
                new \Magento\Framework\Phrase('Adapter failed, feed was not set!')
            );
        }
        return $this->storeObject;
    }

    /**
     * Returns filter instance
     *
     * @return \RocketWeb\ShoppingFeeds\Model\Product\Filter
     */
    public function getFilter()
    {
        return $this->filter;
    }

    /**
     * @return \RocketWeb\ShoppingFeeds\Model\Feed
     */
    public function getFeed()
    {
        return $this->feed;
    }

    /**
     * @return \Magento\Framework\Stdlib\DateTime\Timezone
     */
    public function getTimezone()
    {
        return $this->timezone;
    }


    /**
     * @param \RocketWeb\ShoppingFeeds\Model\Feed $feed
     * @return $this
     */
    public function setFeed(\RocketWeb\ShoppingFeeds\Model\Feed $feed)
    {
        $this->storeObject = $feed->getStore();
        $this->feed = $feed;
        return $this;
    }

    /**
     * @return $this
     */
    public function setTestMode()
    {
        $this->isTestMode = true;
        return $this;
    }

    /**
     * @return mixed
     */
    public function isTestMode()
    {
        return (boolean)$this->isTestMode;
    }

    /**
     * Sets the product for this adapter
     *
     * @param \Magento\Catalog\Model\Product $product
     * @return $this
     * @throws FeedException
     */
    public function setProduct(\Magento\Catalog\Model\Product $product)
    {
        if (!$product->getId()) {
            throw new FeedException(
                new \Magento\Framework\Phrase('Adapter can\'t be created, product is not loaded')
            );
        }
        $this->product = $product;
        return $this;
    }

    /**
     * @return \Magento\Catalog\Model\Product|null
     */
    public function getProduct()
    {
        return $this->product;
    }

    /**
     * @return \RocketWeb\ShoppingFeeds\Model\Product\Option
     */
    public function getOptionProcessor()
    {
        if (is_null($this->option)) {
            $this->option = $this->optionFactory->create(['adapter' => $this]);
        }
        return $this->option;
    }
}