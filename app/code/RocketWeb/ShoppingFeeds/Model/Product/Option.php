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

class Option
{
    protected $_items = array(), $_prices = array();
    protected $_item = array(), $_row = array();

    /**
     * @var Adapter\AdapterAbstract
     */
    protected $adapter;

    /**
     * @var \Magento\Framework\Pricing\Helper\Data
     */
    protected $pricingHelper;

    public function __construct(
        Adapter\AdapterAbstract $adapter,
        \Magento\Framework\Pricing\Helper\Data $pricingHelper
    )
    {
        $this->adapter = $adapter;
        $this->pricingHelper = $pricingHelper;
    }

    /**
     * @return mixed
     */
    public function getItems()
    {
        return $this->_items;
    }


    /**
     * Process current product rows and append or update them according to options usage
     *
     * @param $rows
     * @return array
     */
    public function process($rows)
    {
        if (empty($rows)){
            return array();
        }

        foreach ($rows as $row) {
            $this->_item = $this->_row = $row;
            $options = $this->getOptions();
            $this->processItems($options, count($options));
        }

        $items = $this->getItems();
        return !empty($items) ? $items : $rows;
    }

    /**
     * Returns an array of product options used in the column map across all columns.
     * If $names provided, it will only add those that are within those names.
     *
     * @param array $names
     * @return array
     */
    public function getOptions($names = array())
    {
        $return = array();

        $options = $this->adapter->getProduct()->getOptions();
        if (count($options)) {
            if (empty($names)) {
                // Process the options used in the columns map
                foreach ($this->adapter->getFeed()->getColumnsMap() as $column) {
                    if ($column['attribute'] == 'directive_product_option' && isset($column['param'])) {
                        $names[$column['column']] = is_array($column['param']) ? 
                            $column['param'] : array($column['param']);
                    }
                }
            }

            foreach ($options as $option) {
                foreach ($names as $column => $name) {
                    if (in_array($option->getTitle(), $name)) {
                        if (!isset($return[$column])) {
                            $return[$column] = array();
                        }
                        foreach ($option->getValues() as $value) {
                            $value->setOptionId($option->getId());
                            $type = $option->getType();
                            if ($type == \Magento\Catalog\Model\Product\Option::OPTION_TYPE_DROP_DOWN) {
                                $type = \Magento\Catalog\Model\Product\Option::OPTION_GROUP_SELECT;
                            }
                            $value->setOptionType($type);
                            $return[$column][] = $value;
                        }
                    }
                }
            }
        }

        return $return;
    }

    /**
     * Recursively extract the generate the option items from options array
     *
     * @param array $options array of options used in the column map, obtain with getOptions
     * @param int $depth the number of option to fill in the row
     * @param array $fill counter for
     */
    protected function processItems($options, $depth, $fill = array())
    {
        $originalLink = isset($this->_item['link']) ? $this->_item['link'] : null;
        $originalPrices = $this->_prices;
        foreach ($options as $column => $values) {
            unset($options[$column]);

            foreach ($values as $value) {
                /** @var \Magento\Catalog\Model\Product\Option\Value $value*/

                $this->_updateConcatenate($this->_item[$column], $value->getTitle());

                $this->_item[$column] = $this->adapter->getFilter()->cleanField($value->getTitle(), ['column' => $column]);

                $price = $value->getPrice(true);
                $this->_prices[$value->getOptionId()] = $this->pricingHelper->currency($price, false, false);
                $this->updateItemPrice();

                $this->updateItemId($value);
                $this->updateItemLink(array($value->getOptionId()  => $value->getId()));

                $fill[$column] = $value->getId();
                if (!count($options) && count($fill) == $depth) {
                    $this->_items[] = $this->_item;
                    // We reset the link value otherwise it sticks thru whole row
                    if (!is_null($originalLink)) {
                        $this->_item['link'] = $originalLink;
                    }
                    $this->_prices = $originalPrices;
                } else {
                    $this->processItems($options, $depth, $fill);
                }
            }

            $fill = array();
        }
    }

    /**
     * @return $this
     */
    protected function updateItemPrice()
    {
        if (isset($this->_row['price'])
            && $this->_row['price'] > 0
            && isset($this->_item['price'])) {

            $this->_item['price'] = $this->_row['price'];
            foreach ($this->_prices as $p) {
                $this->_item['price'] += $p;
            }
        }

        if (isset($this->_item['sale_price'])
            && $this->_row['sale_price'] > 0
            && isset($this->_row['sale_price'])) {

            $this->_item['sale_price'] = $this->_row['sale_price'];
            foreach ($this->_prices as $p) {
                $this->_item['sale_price'] += $p;
            }
        }

        return $this;
    }

    /**
     * Update value for concatenate directive
     * 
     * @param string $oldValue
     * @param string $newValue
     */
    protected function _updateConcatenate($oldValue, $newValue)
    {
        foreach ($this->adapter->getFeed()->getColumnsMap() as $column) {
            if ($column['attribute'] == 'directive_concatenate') {
                $this->_item[$column['column']] = str_replace($oldValue, $newValue, $this->_item[$column['column']]);
            }
        }
    }

    /**
     * Update the ID by appending an increment
     * Update the SKU with the option sku if provided, else append increment
     * Update item group id to be the sku of initial product
     *
     * @param $value \Magento\Catalog\Model\Product\Option\Value
     * @return $this
     */
    protected function updateItemId($value)
    {
        foreach ($this->adapter->getFeed()->getColumnsMap() as $column) {

            switch ($column['attribute']) {
                case 'directive_id':
                    $this->_item[$column['column']] = $value->getProduct()->getId(). '-option'. count($this->_items);
                    break;
                case 'sku':
                    if (!is_null($value->getSku())) {
                        $this->_item[$column['column']] = $value->getSku();
                    } else {
                        $this->_item[$column['column']] = $this->_row[$column['column']]. '-'. (count($this->_items)+1);
                    }
                    break;
                case 'directive_item_group_id':
                    $this->_item[$column['column']] = $value->getProduct()->getSku();
                    break;
            }
        }
        return $this;
    }

    /**
     * @param $params
     * @return $this
     */
    protected function updateItemLink($params)
    {
        if (!array_key_exists('link', $this->_item)) {
            return $this;
        }

        $parts = parse_url($this->_item['link']);

        if (isset($parts['fragment'])) {
            parse_str($parts['fragment'], $old_params);
            foreach ($old_params as $key => $value) {
                if (!array_key_exists($key, $params)) {
                    $params[$key] = $value;
                }
            }
        }

        $parts['fragment'] = http_build_query($params);

        $link = "";
        foreach ($parts as $k => $v) {
            switch ($k) {
                case 'scheme':
                    $link .= $v . '://';
                    break;
                case 'port':
                    $link .= ':' . $v;
                    break;
                case 'query':
                    $link .= '?' . $v;
                    break;
                case 'fragment':
                    $link .= '#' . $v;
                    break;
                default:
                    $link .= $v;
            }
        }

        $this->_item['link'] = $link;
        return $this;
    }
}