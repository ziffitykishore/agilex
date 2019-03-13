<?php
namespace SomethingDigital\ProductAttributeFields\Setup;

use Magento\Framework\Setup\UpgradeSchemaInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\SchemaSetupInterface;
use Magento\Framework\DB\Ddl\Table;

class UpgradeSchema implements UpgradeSchemaInterface
{
   
    public function upgrade(SchemaSetupInterface $setup, ModuleContextInterface $context)
    {
        $setup->startSetup();

        if(version_compare($context->getVersion(), '0.0.2', '<')) {

            $tableName = 'catalog_eav_attribute';

            $connection = $setup->getConnection();

            $connection->addColumn(
                $setup->getTable($tableName),
                'include_in_list',
                [
                    'type' => \Magento\Framework\DB\Ddl\Table::TYPE_BOOLEAN,
                    'nullable' => false,
                    'unsigned' => true,
                    'default' => '0',
                    'comment' => 'Include In List'
                ]
            );
            $connection->addColumn(
                $setup->getTable($tableName),
                'list_position',
                [
                    'type' => \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
                    'nullable' => false,
                    'default' => '0',
                    'comment' => 'List Position'
                ]
            );
        }

        $setup->endSetup();
    }
}