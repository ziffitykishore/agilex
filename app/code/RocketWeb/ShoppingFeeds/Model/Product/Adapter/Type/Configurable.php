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
use \Magento\Framework\Filesystem;

/**
 * Configurable Adapter, holds business logic between Product, Config and Mapper
 *
 * Class Configurable
 * @package RocketWeb\ShoppingFeeds\Model\Product\Adapter\Type
 */
class Configurable extends Composite implements AdapterInterface
{
    protected $productMetadata;

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
        \Magento\Framework\App\ProductMetadataInterface $productMetadata,
        array $data = [])
    {
        $this->productMetadata = $productMetadata;
        parent::__construct($filesystem, $feed, $product, $feedTypesConfig, $mapperFactory, $helper, $weeeData, $taxData, $catalogHelper, $catalogRuleCollectionFactory, $productTypePrice, $stockState, $filter, $localeResolver, $timezone, $date, $optionFactory, $processFactory, $processCollectionFactory, $logger, $cache, $adapterFactory, $formatterFactory, $data);
    }

    /**
     * Creates an array of current configurable attributes/values
     *
     * @param \Magento\Catalog\Model\Product $product
     * @return array
     */
    public function getUrlOptions(\Magento\Catalog\Model\Product $product)
    {
        $params = [];
        if ($this->getFeed()->getConfig('configurable_associated_products_link_add_unique')) {
            /** @var \Magento\ConfigurableProduct\Model\Product\Type\Configurable $configurableProductType */
            $configurableProductType = $this->getProduct()->getTypeInstance();
            $codes = $configurableProductType->getConfigurableAttributes($this->getProduct());

            /** @var \Magento\ConfigurableProduct\Model\Product\Type\Configurable\Attribute $attribute */
            foreach ($codes as $attribute) {
                $eavAttribute = $attribute->getProductAttribute();
                $code = $eavAttribute->getAttributeCode();
                if (!$product->hasData($code)) {
                    continue;
                }
                $id = $attribute->getAttributeId();
                $value = $product->getData($code);

                if ($this->useAttributeCodeForUrl($attribute)) {
                    $params[$code] = $value;
                } else {
                    $params[$id] = $value;
                }

            }
        }

        return $params;
    }

    /**
     * @inheritdoc
     */
    public function beforeMap()
    {
        if (!$this->hasData('associated_product_adapters') || !is_array($this->getData('associated_product_adapters'))) {
            // Get associated products with this one
            $configurableProduct = $this->getProduct();
            /** @var \Magento\ConfigurableProduct\Model\ResourceModel\Product\Type\Configurable\Product\Collection $associatedProductCollection */
            $associatedProductCollection = $configurableProduct->getTypeInstance()->getUsedProductCollection($configurableProduct)
                ->addAttributeToSelect('*');

            $associatedProductAdapters = $this->prepareAssociatedProductAdapters($associatedProductCollection);

            $this->setData('associated_product_adapters', $associatedProductAdapters);
        }

        return parent::beforeMap();
    }

    /**
     * @inheritdoc
     */
    public function getAssociatedProductsMode()
    {
        return $this->getFeed()->getConfig('configurable_associated_products_mode');
    }

    /**
     * @inheritdoc
     */
    public function getAssociatedMapInheritance()
    {
        return $this->getFeed()->getConfig('configurable_map_inherit', []);
    }

    public function useAttributeCodeForUrl($attribute)
    {
        /** @var  \Magento\ConfigurableProduct\Model\Product\Type\Configurable\Attribute $attribute */
        $swatch = $attribute->getProductAttribute()->hasData('swatch_input_type');
        $mageVersion = $this->productMetadata->getVersion();

        return ($swatch && version_compare($mageVersion, '2.0.13', '>='));
    }
}