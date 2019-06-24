<?php

namespace Ziffity\Pickupdate\Plugin\Grid;

use Magento\Framework\View\Element\UiComponent\DataProvider\SearchResult as GridSearchResult;
use Magento\Sales\Api\Data\OrderInterface;

/**
 * Add Data, Filters, Sorting functional for Order/Shipment/Invoice Adminhtml Grids for Pickup Date fields
 */
class SearchResult
{
    /**
     * key - grid column name
     * value - sql column name
     */
    const PICKUP_COLUMN = [
            'ziffity_pickupdate_date'    => 'pickupdate.date',
            'ziffity_pickupdate_time'    => 'pickupdate.time',
            'ziffity_pickupdate_comment' => 'pickupdate.comment'
        ];

    /**
     * @var \Ziffity\Pickupdate\Model\ResourceModel\Pickupdate
     */
    private $pickupdateResource;

    /**
     * @var \Ziffity\Pickupdate\Helper\Data
     */
    private $helper;

    /**
     * SearchResult constructor.
     *
     * @param \Ziffity\Pickupdate\Model\ResourceModel\Pickupdate $pickupdateResource
     * @param \Ziffity\Pickupdate\Helper\Data                      $helper
     */
    public function __construct(
        \Ziffity\Pickupdate\Model\ResourceModel\Pickupdate $pickupdateResource,
        \Ziffity\Pickupdate\Helper\Data $helper
    ) {
        $this->pickupdateResource = $pickupdateResource;
        $this->helper = $helper;
    }

    /**
     * @param GridSearchResult             $collection
     * @param \Magento\Framework\DB\Select $select
     *
     * @return \Magento\Framework\DB\Select
     */
    public function afterGetSelect(
        GridSearchResult $collection,
        $select
    ) {
        if ((string)$select && !array_key_exists('pickupdate', $select->getPart('from'))) {
            $select->joinLeft(
                ['pickupdate' => $this->pickupdateResource->getMainTable()],
                'main_table.entity_id = pickupdate.order_id',
                self::PICKUP_COLUMN
            );
        }

        return $select;
    }

    /**
     * Prepare items pickup date to format for Grid
     *
     * @param GridSearchResult              $collection
     * @param \Magento\Framework\DataObject $item
     *
     * @return array
     */
    public function beforeAddItem(
        GridSearchResult $collection,
        \Magento\Framework\DataObject $item
    ) {
        $date = $item->getDataByKey('ziffity_pickupdate_date');
        if ($date) {
            if ($date == '0000-00-00') {
                $item->setData('ziffity_pickupdate_date');
                return [$item];
            }
            $date = $this->helper->convertDateOutput($date);
            $item->setData('ziffity_pickupdate_date', $date);
        }
        return [$item];
    }

    /**
     * @param GridSearchResult $collection
     * @param string           $field
     * @param string|null      $condition
     *
     * @return array
     */
    public function beforeAddFieldToFilter(
        GridSearchResult $collection,
        $field,
        $condition = null
    ) {
        if (array_key_exists($field, self::PICKUP_COLUMN)) {
            $field = self::PICKUP_COLUMN[$field];
        }
        if ($field == OrderInterface::INCREMENT_ID) {
            $field = 'main_table.' . OrderInterface::INCREMENT_ID;
        }

        return [$field, $condition];
    }
}
