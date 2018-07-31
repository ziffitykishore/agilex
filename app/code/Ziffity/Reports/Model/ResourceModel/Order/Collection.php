<?php
namespace Ziffity\Reports\Model\ResourceModel\Order;
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
class Collection extends \Magento\Reports\Model\ResourceModel\Order\Collection
{
     protected function _getSalesAmountExpression()
    {
        
        if (null === $this->_salesAmountExpression) {
            $connection = $this->getConnection();
            $expressionTransferObject = new \Magento\Framework\DataObject(
                [
                    'expression' => '%s - %s - %s - (%s - %s - %s)',
                    'arguments' => [
                        $connection->getIfNullSql('main_table.base_grand_total', 0),
                        $connection->getIfNullSql('main_table.base_tax_amount', 0),
                        $connection->getIfNullSql('main_table.base_shipping_amount', 0),
                        $connection->getIfNullSql('main_table.base_total_refunded', 0),
                        $connection->getIfNullSql('main_table.base_tax_refunded', 0),
                        $connection->getIfNullSql('main_table.base_shipping_refunded', 0),
                    ],
                ]
            );

            $this->_eventManager->dispatch(
                'sales_prepare_amount_expression',
                ['collection' => $this, 'expression_object' => $expressionTransferObject]
            );
            $this->_salesAmountExpression = vsprintf(
                $expressionTransferObject->getExpression(),
                $expressionTransferObject->getArguments()
            );
        }

        return $this->_salesAmountExpression;
    }
    
    protected function _prepareSummaryLive($range, $customStart, $customEnd, $isFilter = 0)
    {
        $this->setMainTable('sales_order');
        $connection = $this->getConnection();

        /**
         * Reset all columns, because result will group only by 'created_at' field
         */
        $this->getSelect()->reset(\Magento\Framework\DB\Select::COLUMNS);

        $expression = $this->_getSalesAmountExpression();
        if ($isFilter == 0) {
            $this->getSelect()->columns(
                [
                    'revenue' => new \Zend_Db_Expr(
                        sprintf(
                            'SUM((%s) * %s)',
                            $expression,
                            $connection->getIfNullSql('main_table.base_to_global_rate', 0)
                        )
                    ),
                ]
            );
        } else {
            $this->getSelect()->columns(['revenue' => new \Zend_Db_Expr(sprintf('SUM(%s)', $expression))]);
        }

        $dateRange = $this->getDateRange($range, $customStart, $customEnd);

        $tzRangeOffsetExpression = $this->_getTZRangeOffsetExpression(
            $range,
            'created_at',
            $dateRange['from'],
            $dateRange['to']
        );

        $this->getSelect()->columns(
            ['quantity' => 'COUNT(main_table.entity_id)', 'range' => $tzRangeOffsetExpression]
        )->where(
            'main_table.state NOT IN (?)',
            [\Magento\Sales\Model\Order::STATE_PENDING_PAYMENT]
        )->order(
            'range',
            \Magento\Framework\DB\Select::SQL_ASC
        )->group(
            $tzRangeOffsetExpression
        );

        $this->addFieldToFilter('created_at', $dateRange);

        return $this;
    }
    
     protected function _calculateTotalsLive($isFilter = 0)
    {
         
        $this->setMainTable('sales_order');
        $this->removeAllFieldsFromSelect();

        $connection = $this->getConnection();

        $baseTaxInvoiced = $connection->getIfNullSql('main_table.base_tax_amount', 0);
        $baseTaxRefunded = $connection->getIfNullSql('main_table.base_tax_refunded', 0);
        $baseShippingInvoiced = $connection->getIfNullSql('main_table.base_shipping_amount', 0);
        $baseShippingRefunded = $connection->getIfNullSql('main_table.base_shipping_refunded', 0);

        $revenueExp = $this->_getSalesAmountExpression();
        $taxExp = sprintf('%s - %s', $baseTaxInvoiced, $baseTaxRefunded);
        $shippingExp = sprintf('%s - %s', $baseShippingInvoiced, $baseShippingRefunded);

        if ($isFilter == 0) {
            $rateExp = $connection->getIfNullSql('main_table.base_to_global_rate', 0);
            $this->getSelect()->columns(
                [
                    'revenue' => new \Zend_Db_Expr(sprintf('SUM((%s) * %s)', $revenueExp, $rateExp)),
                    'tax' => new \Zend_Db_Expr(sprintf('SUM((%s) * %s)', $taxExp, $rateExp)),
                    'shipping' => new \Zend_Db_Expr(sprintf('SUM((%s) * %s)', $shippingExp, $rateExp)),
                ]
            );
        } else {
            $this->getSelect()->columns(
                [
                    'revenue' => new \Zend_Db_Expr(sprintf('SUM(%s)', $revenueExp)),
                    'tax' => new \Zend_Db_Expr(sprintf('SUM(%s)', $taxExp)),
                    'shipping' => new \Zend_Db_Expr(sprintf('SUM(%s)', $shippingExp)),
                ]
            );
        }

        $this->getSelect()->columns(
            ['quantity' => 'COUNT(main_table.entity_id)']
        );

        return $this;
    }
    
    public function calculateSales($isFilter = 0)
    {
        $statuses = $this->_orderConfig->getStateStatuses(\Magento\Sales\Model\Order::STATE_CANCELED);

        if (empty($statuses)) {
            $statuses = [0];
        }
        $connection = $this->getConnection();

        if ($this->_scopeConfig->getValue(
            'sales/dashboard/use_aggregated_data',
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE
        )
        ) {
            $this->setMainTable('sales_order_aggregated_created');
            $this->removeAllFieldsFromSelect();
            $averageExpr = $connection->getCheckSql(
                'SUM(main_table.orders_count) > 0',
                'SUM(main_table.total_revenue_amount)/SUM(main_table.orders_count)',
                0
            );
            $this->getSelect()->columns(
                ['lifetime' => 'SUM(main_table.total_revenue_amount)', 'average' => $averageExpr]
            );

            if (!$isFilter) {
                $this->addFieldToFilter(
                    'store_id',
                    ['eq' => $this->_storeManager->getStore(\Magento\Store\Model\Store::ADMIN_CODE)->getId()]
                );
            }
            $this->getSelect()->where('main_table.order_status NOT IN(?)', $statuses);
        } else {
            $this->setMainTable('sales_order');
            $this->removeAllFieldsFromSelect();

            $expr = $this->_getSalesAmountExpression();

            if ($isFilter == 0) {
                $expr = '(' . $expr . ') * main_table.base_to_global_rate';
            }

            $this->getSelect()->columns(
                ['lifetime' => "SUM({$expr})", 'average' => "AVG({$expr})"]
            )->where(
                'main_table.status NOT IN(?)',
                $statuses
            )->where(
                'main_table.state NOT IN(?)',
                [\Magento\Sales\Model\Order::STATE_PENDING_PAYMENT]
            );
        }
        return $this;
    }
    
}

