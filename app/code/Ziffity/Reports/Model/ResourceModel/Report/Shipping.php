<?php
namespace Ziffity\Reports\Model\ResourceModel\Report;
/**
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * This is the Summary for this element.
 * 
 * @inheritDoc
 */
class Shipping extends \Magento\Sales\Model\ResourceModel\Report\Shipping
{
    /**
     * Aggregate shipping report by order create_at as period
     *
     * @param string|null $from
     * @param string|null $to
     * @return $this
     * @throws \Exception
     */
    protected function _aggregateByOrderCreatedAt($from, $to)
    {
        $table = $this->getTable('sales_shipping_aggregated_order');
        $sourceTable = $this->getTable('sales_order');
        $connection = $this->getConnection();
        $connection->beginTransaction();

        try {
            if ($from !== null || $to !== null) {
                $subSelect = $this->_getTableDateRangeSelect($sourceTable, 'created_at', 'updated_at', $from, $to);
            } else {
                $subSelect = null;
            }

            $this->_clearTableByDateRange($table, $from, $to, $subSelect);
            // convert dates to current admin timezone
            $periodExpr = $connection->getDatePartSql(
                $this->getStoreTZOffsetQuery($sourceTable, 'created_at', $from, $to)
            );
            $shippingCanceled = $connection->getIfNullSql('base_shipping_canceled', 0);
            $shippingRefunded = $connection->getIfNullSql('base_shipping_refunded', 0);
            $columns = [
                'period' => $periodExpr,
                'store_id' => 'store_id',
                'order_status' => 'status',
                'shipping_description' => 'shipping_description',
                'orders_count' => new \Zend_Db_Expr('COUNT(entity_id)'),
                'total_shipping' => new \Zend_Db_Expr(
                    "SUM((base_shipping_amount - {$shippingCanceled}) * base_to_global_rate)"
                ),
                'total_shipping_actual' => new \Zend_Db_Expr(
                    "SUM((base_shipping_invoiced - {$shippingRefunded}) * base_to_global_rate)"
                ),
            ];

            $select = $connection->select();
            $select->from(
                $sourceTable,
                $columns
            )->where(
                'state NOT IN (?)',
                [\Magento\Sales\Model\Order::STATE_PENDING_PAYMENT]
            )->where(
                'is_virtual = 0'
            );

            if ($subSelect !== null) {
                $select->having($this->_makeConditionFromDateRangeSelect($subSelect, 'period'));
            }

            $select->group([$periodExpr, 'store_id', 'status', 'shipping_description']);
            $select->having('orders_count > 0');
            $insertQuery = $select->insertFromSelect($table, array_keys($columns));
            $connection->query($insertQuery);
            $select->reset();

            $columns = [
                'period' => 'period',
                'store_id' => new \Zend_Db_Expr(\Magento\Store\Model\Store::DEFAULT_STORE_ID),
                'order_status' => 'order_status',
                'shipping_description' => 'shipping_description',
                'orders_count' => new \Zend_Db_Expr('SUM(orders_count)'),
                'total_shipping' => new \Zend_Db_Expr('SUM(total_shipping)'),
                'total_shipping_actual' => new \Zend_Db_Expr('SUM(total_shipping_actual)'),
            ];

            $select->from($table, $columns)->where('store_id != ?', \Magento\Store\Model\Store::DEFAULT_STORE_ID);

            if ($subSelect !== null) {
                $select->where($this->_makeConditionFromDateRangeSelect($subSelect, 'period'));
            }

            $select->group(['period', 'order_status', 'shipping_description']);
            $insertQuery = $select->insertFromSelect($table, array_keys($columns));
            $connection->query($insertQuery);
        } catch (\Exception $e) {
            $connection->rollBack();
            throw $e;
        }

        $connection->commit();
        return $this;
    }
}
