<?php

namespace Ziffity\Pickupdate\Setup;

use Magento\Framework\DB\Ddl\Table;
use Magento\Framework\Setup\InstallSchemaInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\SchemaSetupInterface;

class InstallSchema implements InstallSchemaInterface
{
    public function install(SchemaSetupInterface $setup, ModuleContextInterface $context)
    {
        $installer = $setup;

        $installer->startSetup();

        /**
         * Create table 'ziffity_pickupdate_pickupdate'
         */

        $table = $installer->getConnection()->newTable(
            $installer->getTable('ziffity_pickupdate_pickupdate')
        )->addColumn(
            'pickupdate_id',
            Table::TYPE_INTEGER,
            8,
            ['unsigned' => true, 'identity' => true, 'nullable' => false, 'primary' => true]
        )->addColumn(
            'order_id',
            Table::TYPE_INTEGER,
            10,
            ['unsigned' => true, 'nullable' => false]
        )->addColumn(
            'increment_id',
            Table::TYPE_TEXT,
            50,
            ['nullable' => false]
        )->addColumn(
            'date',
            Table::TYPE_DATE,
            null,
            ['nullable' => false, 'default' => '0000-00-00']
        )->addColumn(
            'time',
            Table::TYPE_TEXT,
            255,
            ['nullable' => false]
        )->addColumn(
            'comment',
            Table::TYPE_TEXT,
            255,
            ['nullable' => false]
        )->addColumn(
            'reminder',
            Table::TYPE_BOOLEAN,
            1,
            ['unsigned' => true, 'nullable' => false]
        )->addColumn(
            'tinterval_id',
            Table::TYPE_INTEGER,
            10,
            ['unsigned' => true, 'nullable' => true,  'default' => null]
        )->addColumn(
            'active',
            Table::TYPE_SMALLINT,
            6,
            ['nullable' => false, 'default' => '1']
        )->addIndex(
            $installer->getIdxName('ziffity_pickupdate_pickupdate', ['order_id']),
            ['order_id']
        )->addIndex(
            $installer->getIdxName('ziffity_pickupdate_pickupdate', ['tinterval_id']),
            ['tinterval_id']
        )->addForeignKey(
            $installer->getFkName('ziffity_pickupdate_pickupdate', 'order_id', 'sales_order', 'entity_id'),
            'order_id',
            $installer->getTable('sales_order'),
            'entity_id',
            Table::ACTION_CASCADE
        );
        $installer->getConnection()->createTable($table);

        /**
         * Create table 'ziffity_pickupdate_dinterval'
         */

        $table = $installer->getConnection()->newTable(
            $installer->getTable('ziffity_pickupdate_dinterval')
        )->addColumn(
            'dinterval_id',
            Table::TYPE_INTEGER,
            8,
            ['unsigned' => true, 'identity' => true, 'nullable' => false, 'primary' => true]
        )->addColumn(
            'store_ids',
            Table::TYPE_TEXT,
            255,
            ['nullable' => false]
        )->addColumn(
            'from_year',
            Table::TYPE_SMALLINT,
            4,
            ['nullable' => false]
        )->addColumn(
            'from_month',
            Table::TYPE_SMALLINT,
            2,
            ['nullable' => false]
        )->addColumn(
            'from_day',
            Table::TYPE_SMALLINT,
            2,
            ['nullable' => false]
        )->addColumn(
            'to_year',
            Table::TYPE_SMALLINT,
            4,
            ['nullable' => false]
        )->addColumn(
            'to_month',
            Table::TYPE_SMALLINT,
            2,
            ['nullable' => false]
        )->addColumn(
            'to_day',
            Table::TYPE_SMALLINT,
            2,
            ['nullable' => false]
        )->addColumn(
            'description',
            Table::TYPE_TEXT,
            255,
            ['nullable' => true]
        );
        $installer->getConnection()->createTable($table);

        /**
         * Create table 'ziffity_pickupdate_holidays'
         */

        $table = $installer->getConnection()->newTable(
            $installer->getTable('ziffity_pickupdate_holidays')
        )->addColumn(
            'holiday_id',
            Table::TYPE_INTEGER,
            8,
            ['unsigned' => true, 'identity' => true, 'nullable' => false, 'primary' => true]
        )->addColumn(
            'store_ids',
            Table::TYPE_TEXT,
            255,
            ['nullable' => false]
        )->addColumn(
            'year',
            Table::TYPE_SMALLINT,
            4,
            ['nullable' => false]
        )->addColumn(
            'month',
            Table::TYPE_SMALLINT,
            2,
            ['nullable' => false]
        )->addColumn(
            'day',
            Table::TYPE_SMALLINT,
            2,
            ['nullable' => false]
        )->addColumn(
            'description',
            Table::TYPE_TEXT,
            255,
            ['nullable' => true]
        );
        $installer->getConnection()->createTable($table);

        /**
         * Create table 'ziffity_pickupdate_tinterval'
         */

        $table = $installer->getConnection()->newTable(
            $installer->getTable('ziffity_pickupdate_tinterval')
        )->addColumn(
            'tinterval_id',
            Table::TYPE_INTEGER,
            8,
            ['unsigned' => true, 'identity' => true, 'nullable' => false, 'primary' => true]
        )->addColumn(
            'store_ids',
            Table::TYPE_TEXT,
            255,
            ['nullable' => false]
        )->addColumn(
            'time_from',
            Table::TYPE_TEXT,
            255,
            ['nullable' => false]
        )->addColumn(
            'time_from_sql',
            Table::TYPE_TEXT,
            255,
            ['nullable' => false]
        )->addColumn(
            'time_to',
            Table::TYPE_TEXT,
            255,
            ['nullable' => false]
        )->addColumn(
            'time_to_sql',
            Table::TYPE_TEXT,
            255,
            ['nullable' => false]
        )->addColumn(
            'sorting_order',
            Table::TYPE_SMALLINT,
            5,
            ['unsigned' => true, 'nullable' => false]
        )->addColumn(
            'quota',
            Table::TYPE_INTEGER,
            10,
            ['unsigned' => true, 'default' => null]
        );
        $installer->getConnection()->createTable($table);

        $installer->endSetup();
    }
}
