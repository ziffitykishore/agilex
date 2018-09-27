<?php

namespace Ziffity\Webforms\Setup;

use Magento\Framework\Setup\UpgradeSchemaInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\SchemaSetupInterface;
/**
 * Upgrade the Ziffity_Webforms module DB scheme
 */
class UpgradeSchema implements UpgradeSchemaInterface
{
    /**
     * {@inheritdoc}
     */
    public function upgrade(SchemaSetupInterface $setup, ModuleContextInterface $context)
    {
        if (version_compare($context->getVersion(), '1.0.1', '<')) {
            $this->addColumnsInTable($setup);
        }
    }
    protected function addColumnsInTable(SchemaSetupInterface $setup)
    {
        $setup->startSetup();
        /*Get module table*/
        $tableName = $setup->getTable('webforms_customer_data');
        /* Check if the table already exists */
        if ($setup->getConnection()->isTableExists($tableName) == true) {
            $columns = [
                'customer_id' => [
                    'type' => \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
                    'length' => 10,
                    'nullable' => false,
                    'default' => 0,
                    'after' => 'is_active',
                    'comment' => 'Customer Id'
                ],
                'customer_ip' => [
                    'type' => \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                    'nullable' => false,
                    'after' => 'customer_id',
                    'comment' => 'Customer Ip'
                ],
                'store_id' => [
                    'type' => \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
                    'length' => 5,
                    'nullable' => false,
                    'default' => 0,             
                    'after' => 'customer_ip',
                    'comment' => 'Store Id'
                ]
            ];
            $connection = $setup->getConnection();
            foreach ($columns as $name => $definition) {
                $connection->addColumn($tableName, $name, $definition);
            }
        }
        $setup->endSetup();
    }
}
