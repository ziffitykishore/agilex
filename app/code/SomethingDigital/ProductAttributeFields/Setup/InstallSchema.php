<?php
namespace SomethingDigital\ProductAttributeFields\Setup;

use Magento\Framework\Setup\InstallSchemaInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\SchemaSetupInterface;
use Magento\Framework\DB\Ddl\Table;

class InstallSchema implements InstallSchemaInterface
{
   
    public function install(SchemaSetupInterface $setup, ModuleContextInterface $context)
    {
        $setup->startSetup();

        $tableName = 'catalog_eav_attribute';

        $connection = $setup->getConnection();

        $connection->addColumn(
            $setup->getTable($tableName),
            'include_in_table',
            [
                'type' => \Magento\Framework\DB\Ddl\Table::TYPE_SMALLINT,
                'nullable' => false,
                'unsigned' => true,
                'default' => '0',
                'comment' => 'Include In Table'
            ]
        );
        $connection->addColumn(
            $setup->getTable($tableName),
            'table_position',
            [
                'type' => \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
                'nullable' => false,
                'default' => '0',
                'comment' => 'Table Position'
            ]
        );
        $connection->addColumn(
            $setup->getTable($tableName),
            'include_in_flyout',
            [
                'type' => \Magento\Framework\DB\Ddl\Table::TYPE_SMALLINT,
                'nullable' => false,
                'unsigned' => true,
                'default' => '0',
                'comment' => 'Include In Flyout'
            ]
        );
        $connection->addColumn(
            $setup->getTable($tableName),
            'flyout_position',
            [
                'type' => \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
                'nullable' => false,
                'default' => '0',
                'comment' => 'Flyout Position'
            ]
        );
        $connection->addColumn(
            $setup->getTable($tableName),
            'searchable_in_layered_nav',
            [
                'type' => \Magento\Framework\DB\Ddl\Table::TYPE_SMALLINT,
                'nullable' => false,
                'unsigned' => true,
                'default' => '0',
                'comment' => 'Searchable in Layered Nav'
            ]
        );
        $connection->addColumn(
            $setup->getTable($tableName),
            'layered_nav_description',
            [
                'type' => \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                'nullable' => true,
                'comment' => 'Layered Nav Description'
            ]
        );

        $setup->endSetup();
    }
}