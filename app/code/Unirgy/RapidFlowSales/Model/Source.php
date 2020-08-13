<?php
/**
 * Unirgy LLC
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the EULA
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://www.unirgy.com/LICENSE-M1.txt
 *
 * @category   Unirgy
 * @package    Unirgy_RapidFlow
 * @copyright  Copyright (c) 2008-2009 Unirgy LLC (http://www.unirgy.com)
 * @license    http:///www.unirgy.com/LICENSE-M1.txt
 */

/**
 * Class ModelSource
 *
 * @method string getPath()
 * @method $this setPath(string $path)
 */

namespace Unirgy\RapidFlowSales\Model;

use Magento\Eav\Model\Config as ModelConfig;
use Magento\Eav\Model\Entity\Attribute\Set;
use Magento\Framework\Exception\LocalizedException;
use Magento\Store\Model\StoreManagerInterface;
use Unirgy\RapidFlow\Helper\Data as HelperData;
use Unirgy\RapidFlow\Model\Config;
use Unirgy\RapidFlow\Model\Source as RapidFlowModelSource;
use Unirgy\RapidFlowSales\Helper\Data as RapidFlowSalesHelperData;

/**
 * Class Source
 *
 * @method string getPath()
 * @package Unirgy\RapidFlowSales\Model
 */
class Source extends RapidFlowModelSource
{
    const SALES_ROW_TYPE      = 'sales_row_type';
    const DATE_FILTERED_TYPES = 'date_filtered_types';
    const SALES_PREFIX        = 'sales_';
    const MISSING_STORE       = 'missing_store';
    const MISSING_CUSTOMER    = 'missing_customer';
    const ACTION_IGNORE       = 'ignore';
    const ACTION_ERROR        = 'error';
    const ACTION_CREATE       = 'create';
    const FAILED_CUSTOMER     = 'failed_customer';
    /**
     * @var RapidFlowSalesHelperData
     */
    protected $_helperData;
    /**
     * @var RapidFlowSalesHelperData
     */
    protected $_helper;
    /**
     * @var \Unirgy\RapidFlowSales\Model\Profile\Sales
     */
    protected $_profile;

    public function __construct(
        StoreManagerInterface $storeManager,
        Set $entityAttributeSet,
        HelperData $rapidFlowHelperData,
        Config $rapidFlowModelConfig,
        ModelConfig $eavModelConfig,
        RapidFlowSalesHelperData $helperData,
        array $data = []
    )
    {
        $this->_helperData = $helperData;

        parent::__construct($storeManager,
            $entityAttributeSet,
            $rapidFlowHelperData,
            $rapidFlowModelConfig,
            $eavModelConfig,
            $data);
    }

    public function toOptionHash($selector = false)
    {
        try {
            $options = parent::toOptionHash($selector);
        } catch(LocalizedException $e) {
            // parent class has no appropriate option
            $options = $this->_toOptionsHash($selector);
        }

        return $options;
    }

    /**
     * @param bool $selector
     * @return array
     */
    protected function _toOptionsHash($selector)
    {
        $options = [];
        $path    = $this->getPath();
        if ($path === self::SALES_ROW_TYPE) {
            $options = $this->helper()->getSalesRowTypes();
        } else if ($path === self::DATE_FILTERED_TYPES) {
            $options = $this->helper()->getDateRowTypes();
        } else if (strpos($path, self::SALES_PREFIX) === 0) {
            $options = $this->_getSalesEntityOptions($path);
        } else if ($path === self::MISSING_STORE) {
            $options = [
                self::ACTION_IGNORE => 'Warning: Ignore missing store and use admin',
                self::ACTION_ERROR  => 'Error: Skip row import',
            ];
        } else if ($path === self::MISSING_CUSTOMER) {
            $options = [
                self::ACTION_CREATE => 'Warning: Attempt to add customer and skip import if failed',
                self::ACTION_IGNORE => 'Warning: Ignore missing customer and set to null',
                self::ACTION_ERROR  => 'Error: Skip row import',
            ];
        } else if ($path === self::FAILED_CUSTOMER) {
            $options = [
                self::ACTION_IGNORE => 'Warning: Ignore error and set customer to null',
                self::ACTION_ERROR  => 'Error: Skip row import',
            ];
        } else {
            $this->throwInvalidSourcePathException();
        }

        if ($selector) {
            array_unshift($options, ['' => __('* Please select')]);
        }

        return $options;
    }

    /**
     * @return RapidFlowSalesHelperData
     */
    protected function helper()
    {
        return $this->_helperData;
    }

    protected function _getSalesEntityOptions($path)
    {
        if (null === $this->_profile) {
            throw new \RuntimeException(__('Profile not set.'));
        }
        $entityOptions = [];
        $type          = explode('_', $path, 2);
        $type          = count($type) === 2? $type[1]: $type[0];
        $tempOptions   = $this->_profile->getEntityColumns($type);
        foreach ($tempOptions as $option) {
            $entityOptions[$option] = __(ucwords(str_replace('_', ' ', $option)));
        }

        return $entityOptions;
    }

    protected function throwInvalidSourcePathException()
    {
        throw new LocalizedException(__('Invalid request for source options: %1', $this->getPath()));
    }

    public function getSalesRowTypesTree(array $selected = [])
    {
        $selected = $this->parseTreeRowTypesStructure($selected);
        $selAll   = isset($selected['*']) && $selected['*'] === true;
        $result   = [
            'id'         => '*',
            'text'       => 'Select all',
            'sort_order' => 10,
            'checked'    => $selAll,
            'children'   => []
        ];

        $initialSortOrder = 10;// all elements start with sort order of 10, next element order is +10
        $rtSort           = $initialSortOrder;
        foreach ($this->helper()->getRowTypes() as $typeId => $rowType) {
            $item = [
                'id'         => $typeId,
                'text'       => $rowType['title'],
                'checked'    => $selAll,
                'sort_order' => $rtSort
            ];

            $salesEntityOptions = $this->_getSalesEntityOptions($typeId);
            $checked            = true;
            if ($salesEntityOptions) {
                $item['children'] = [];
                $chSort           = $initialSortOrder;
                foreach ($salesEntityOptions as $option => $label) {
                    $isChecked          = !empty($selected['*'][$typeId]) && in_array($option, $selected['*'][$typeId]);
                    $item['children'][] = [
                        'id'         => $option,
                        'text'       => $label,
                        'checked'    => $selAll || $isChecked,
                        'sort_order' => $chSort
                    ];

                    if (!$isChecked) {
                        $checked = false;
                    }

                    $chSort += 10;
                }
                $item['checked'] = $selAll || $checked;
            }
            $rtSort               += 10;
            $result['children'][] = $item;
        }

        return [$result];
    }

    public function parseTreeRowTypesStructure($selected)
    {
        if (isset($selected[0]['__root__'])) {
            $selected = $selected[0]['__root__'][0];
        }
        $selected = $this->_transformSelection($selected);

        return $selected;
    }

    protected function _transformSelection($selected)
    {
        $result = [];
        foreach ($selected as $index => $item) {
            if ($index === '*' && !is_array($item)) {
                return ['*' => $item];
            }

            if ($index === '*' && is_array($item)) {
                $result['*'] = $this->_transformSelection($item);
            }

            if (is_numeric($index) && is_array($item)) {
                foreach ($item as $key => $value) {
                    if (!is_array($value)) {
                        $result[] = $key;
                    } else {
                        $result[$key] = $this->_transformSelection($value);
                    }
                }
            }

        }

        return $result;
    }

    public function setProfile($profile)
    {
        $this->_profile = $profile;

        return $this;
    }
}
