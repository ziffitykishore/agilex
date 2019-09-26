<?php

namespace Creatuity\Nav\Setup;

use Magento\Framework\Setup\UpgradeSchemaInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\SchemaSetupInterface;
use Magento\Framework\DB\Ddl\Table;

class UpgradeSchema implements UpgradeSchemaInterface
{
    /**
     * To upgrade database schema
     * 
     * @param SchemaSetupInterface $setup
     * @param ModuleContextInterface $context
     */
    public function upgrade(SchemaSetupInterface $setup, ModuleContextInterface $context)
    {
        $installer = $setup;
        $installer->startSetup();
        $tableName = $installer->getTable('navision_log');

        if (version_compare($context->getVersion(), '1.0.6', '<')) {
            if (!$installer->tableExists('navision_log')) {
                $table = $installer->getConnection()
                    ->newTable($tableName)
                    ->addColumn(
                        'log_id',
                        Table::TYPE_INTEGER,
                        null,
                        [
                            'identity' => true,
                            'unsigned' => true,
                            'nullable' => false,
                            'primary' => true
                        ],
                        'Log ID'
                    )
                    ->addColumn(
                        'log_type',
                        Table::TYPE_TEXT,
                        255,
                        ['nullable' => false, 'default' => ''],
                        'Log Type'
                    )
                    ->addColumn(
                        'log_status',
                        Table::TYPE_SMALLINT,
                        null,
                        ['unsigned' => true, 'nullable' => false, 'default' => '0'],
                        'Log Status'
                    )
                    ->addColumn(
                        'description',
                        Table::TYPE_TEXT,
                        null,
                        ['nullable' => false, 'default' => ''],
                        'Description'
                    )
                    ->addColumn(
                        'created_at',
                        Table::TYPE_TIMESTAMP,
                        null,
                        ['nullable' => false, 'default' => Table::TIMESTAMP_INIT],
                        'Created At'
                    );
                $installer->getConnection()->createTable($table);
            }
            $installer->endSetup();
        }
    }
}
