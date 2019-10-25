<?php

namespace PartySupplies\Order\Setup;

use Magento\Framework\Setup\InstallSchemaInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\SchemaSetupInterface;
use Magento\Framework\DB\Ddl\Table;

class InstallSchema implements InstallSchemaInterface
{
    /**
     * To create new column to sales_order table
     *
     * @param SchemaSetupInterface $setup
     * @param ModuleContextInterface $context
     * @return void
     */
    public function install(SchemaSetupInterface $setup, ModuleContextInterface $context)
    {
        
        $installer = $setup;

        $installer->startSetup();

        $eavTable = $installer->getTable('sales_order');

        $columns = [
            'nav_customer_id' => [
                'type' => Table::TYPE_TEXT,
                'nullable' => true,
                'comment' => 'Navision Customer ID',
            ],
            'nav_order_id' => [
                'type' => Table::TYPE_TEXT,
                'nullable' => true,
                'comment' => 'Navision Order ID',
            ]
        ];

        $connection = $installer->getConnection();
        foreach ($columns as $name => $definition) {
            $connection->addColumn($eavTable, $name, $definition);
        }

        $installer->endSetup();
    }
}
