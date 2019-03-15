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

use \RocketWeb\ShoppingFeeds\Model\Product\Adapter\AdapterAbstract;
use \RocketWeb\ShoppingFeeds\Model\Feed\Source\Product\Inheritance;

/**
 * Composite Adapter, holds business logic between Product, Config and Mapper
 *
 * Class Composite
 * @package RocketWeb\ShoppingFeeds\Model\Product\Adapter\Type
 */
class Composite extends AdapterAbstract
{
    const ALLOWED_PARENT = [
        \RocketWeb\ShoppingFeeds\Model\Feed\Source\Product\AssociatedMode::ONLY_PARENT,
        \RocketWeb\ShoppingFeeds\Model\Feed\Source\Product\AssociatedMode::BOTH_PARENT_ASSOCIATED
    ];

    const ALLOWED_ASSOC = [
        \RocketWeb\ShoppingFeeds\Model\Feed\Source\Product\AssociatedMode::ONLY_ASSOCIATED,
        \RocketWeb\ShoppingFeeds\Model\Feed\Source\Product\AssociatedMode::BOTH_PARENT_ASSOCIATED
    ];

    /**
     * @param \Magento\Catalog\Model\ResourceModel\Product\Collection $associatedProductCollection
     * @return array
     */
    protected function prepareAssociatedProductAdapters($associatedProductCollection)
    {
        $associatedProductAdapters = [];

        foreach ($associatedProductCollection as $associatedProduct) {
            /** @var \Magento\Catalog\Model\Product $associatedProduct */
            if ($associatedProduct->isDisabled()) {
                continue;
            }

            $associatedProductAdapter = $this->adapterFactory->create($associatedProduct, $this->getFeed(), false);
            if ($associatedProductAdapter !== false) {
                $associatedProductAdapter->setParentAdapter($this);
                if ($this->isTestMode()) {
                    $associatedProductAdapter->setTestMode();
                }
                $associatedProductAdapters[] = $associatedProductAdapter;
            }
        }

        return $associatedProductAdapters;
    }

    /**
     * Internal method to pull feed config for specific product type
     * This needs to be overwritten in child class
     *
     * @return int
     */
    public function getAssociatedProductsMode()
    {
        return \RocketWeb\ShoppingFeeds\Model\Feed\Source\Product\AssociatedMode::BOTH_PARENT_ASSOCIATED;
    }

    /**
     * Internal method to pull feed config for specific product type
     * This needs to be overwritten in child class
     *
     * @return array
     */
    public function getAssociatedMapInheritance()
    {
        return [];
    }

    /**
     * @inheritdoc
     */
    protected function internalMap()
    {
        $associatedMode = $this->getAssociatedProductsMode();
        $rows = [];

        if (in_array($associatedMode, self::ALLOWED_PARENT)) {
            $this->setData('map_parent', true);
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
        }

        if (in_array($associatedMode, self::ALLOWED_ASSOC)) {
            $rows = array_merge($rows, $this->mapAssociatedProducts());
        }

        return $rows;
    }

    /**
     * Get rows for associated products
     *
     * @return array
     */
    protected function mapAssociatedProducts()
    {
        $rows = [];
        $associatedProductAdapters = $this->getData('associated_product_adapters');
        $feedType = $this->feed->getType();
        $generator = $this->getData('generator');

        /** @var \RocketWeb\ShoppingFeeds\Model\Product\Adapter\AdapterAbstract $associatedProductAdapter */
        foreach ($associatedProductAdapters as $associatedProductAdapter) {

            if ($associatedProductAdapter->isSkipped()) {
                if ($generator) {
                    $generator->getLogger()->info(sprintf('Product skipped: %s', $associatedProductAdapter->getSkipMessage()));
                    $generator->updateCountSkip(1);
                }
                continue;
            }

            if ($associatedProductAdapter->isDuplicate()) {
                $this->logger->info(sprintf(
                        'Associated product skipped: %s', $associatedProductAdapter->getSkipMessage())
                );
                continue;
            }

            $fields = [];
            foreach ($this->feed->getColumnsMap() as $arr) {

                $cell = $this->mapByInheritance($associatedProductAdapter, $arr);
                // Grab from associated by default if no inheritance rule defined
                if ($cell === false) {
                    $cell = $associatedProductAdapter->getMapValue($arr);
                }

                $column = $arr['column'];
                $directive = $this->feedTypesConfig->getDirective($feedType, $arr['attribute']);
                if (!empty($directive)) {
                    $mapperData = $this->mapperFactory->getMapperData($directive, $feedType);
                    $mapper = $this->mapperFactory->create($directive, $associatedProductAdapter);
                    if (isset($mapperData['filter']) && $mapperData['filter']) {
                        $skip = $mapper->filter($cell);
                        if ($skip) {
                            $this->logger->info(sprintf('Skipped product #%s filtered by column "%s"', $associatedProductAdapter->getProduct()->getSku(), $column));
                            if ($this->hasData('generator')) {
                                $this->getData('generator')->updateCountSkip(1);
                            }
                            continue 2;
                        }
                    }
                }

                if (isset($fields[$column])) {
                    if (is_array($fields[$column])) {
                        $fields[$column][] = $cell;
                    } else {
                        $fields[$column] = [$fields[$column], $cell];
                    }
                } else {
                    $fields[$column] = $cell;
                }
            }

            $associatedProductAdapter->checkEmptyColumns($fields);
            if (!$associatedProductAdapter->isSkipped()) {
                array_push($rows, $fields);
            } else {
                if ($generator) {
                    $generator->getLogger()->info(sprintf('Product skipped: %s', $associatedProductAdapter->getSkipMessage()));
                    $generator->updateCountSkip(1);
                }
            }
        }

        return $rows;
    }

    /**
     * @inheritdoc
     */
    public function getChildrenCount()
    {
        return self::DEFAULT_CHILDREN_COUNT + count($this->getData('associated_product_adapters'));
    }


    /**
     * @inheritdoc
     */
    public function hasSpecialPrice($processRules = true, $product = null)
    {
        $associatedProductAdapters = $this->getData('associated_product_adapters');

        $has = false;
        /** @var \RocketWeb\ShoppingFeeds\Model\Product\Adapter\AdapterAbstract $associatedProductAdapter */
        foreach ($associatedProductAdapters as $associatedProductAdapter) {
            if ($associatedProductAdapter->hasSpecialPrice($processRules, $product)) {
                $has = true;
                break;
            }
        }

        return $has;
    }

    protected function mapByInheritance(\RocketWeb\ShoppingFeeds\Model\Product\Adapter\AdapterAbstract $adapter, $column = [])
    {
        $value = false;
        if (!array_key_exists('column', $column)) {
            return $value;
        }
        $column_name = $column['column'];
        $map = $this->getAssociatedMapInheritance();

        foreach ($map as $row) {
            if ($row['column'] == $column_name) {
                switch ($row['from']) {
                    case Inheritance::PARENT_ONLY:
                        $value = $this->getMapValue($column);
                        break;
                    case Inheritance::ASSOCIATED_ONLY:
                        $value = $adapter->getMapValue($column);
                        break;
                    case Inheritance::PARENT_FIRST:
                        $value = $this->getMapValue($column);
                        if (empty($value)) {
                            $value = $adapter->getMapValue($column);
                        }
                        break;
                    case Inheritance::ASSOCIATED_FIRST:
                        $value = $adapter->getMapValue($column);
                        if (empty($value)) {
                            $value = $this->getMapValue($column);
                        }
                        break;
                }
            }
        }

        return $value;
    }
}
