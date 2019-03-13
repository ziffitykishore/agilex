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

namespace RocketWeb\ShoppingFeeds\Model\Product\Mapper\Generic\Simple;

use \RocketWeb\ShoppingFeeds\Model\Product\Mapper\MapperAbstract;

class Concatenate extends MapperAbstract
{
    public function map(array $params = array())
    {
        $expr = $params['param'];
        preg_match_all('/\{\{(.*?)\}\}/is', $expr, $attributes);

        if (!isset($attributes[1]) || empty($attributes[1])) {
            $this->logger->warning('Invalid expression in Concatenate directive. Could not find product attributes', $params);
            return $expr;
        }

        // Get value for each identified attribute
        $values = array();
        foreach ($attributes[1] as $k => $attributeColumn) {
            $column = $this->getColumn($attributeColumn);
            $newParams = ['column' => $column, 'attribute' => $column];

            $columnsMap = $this->getAdapter()->getFeed()->getColumnsMap();
            foreach ($columnsMap as $arr) {
                if ($newParams['column'] == $arr['column']) {
                    $newParams['attribute'] = $arr['attribute'];
                    $newParams['param'] = isset($arr['param']) ? $arr['param'] : '';

                    if ($newParams['attribute'] == 'directive_concatenate') {
                        $newParams['attribute'] = $attributeColumn;
                    }
                    break;
                }
            }

            try {
                $values = $this->getAttributeValue($k, $attributeColumn, $values, $newParams);
            } catch (\Exception $e) {
                $this->logger->warning(sprintf('Invalid attribute name in Concatenate directive. Could not find product attribute matching {{%s}}', $attributeColumn), $params);
                $values[$k] = $attributeColumn;
            }
        }

        $implodedValues = implode('', $values);
        if (!empty($implodedValues)) {
            foreach ($values as $k => $val) {
                $expr = str_replace($attributes[0][$k], $val, $expr);
            }
        } else {
            $expr = '';
        }

        return $this->getAdapter()->getFilter()->cleanField($expr, $params);
    }

    protected function getColumn($attributeColumn)
    {
        return $attributeColumn;
    }

    protected function getAttributeValue($key, $attributeColumn, $values = [], $params = [])
    {
        $values[$key] = $this->getAdapter()->getMapValue($params);

        if (empty($values[$key])) {
            $values[$key] = $this->getAdapter()->mapEmptyValues($params);
        }

        return $values;
    }
}