<?php

namespace Ziffity\Blockcustomers\Setup;

use Magento\Framework\Setup\UpgradeSchemaInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\SchemaSetupInterface;
use Magento\Framework\DB\Adapter\AdapterInterface;

/**
 * Upgrade the Ziffity_Blockcustomers module DB scheme
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
        
        
        $setup->startSetup();
        $connection = $setup->getConnection();
        if (version_compare($context->getVersion(), '1.0.1', '<')){
           
            $connection->addIndex(
                    $setup->getTable('blocked_customers'),
                $setup->getIdxName(
                    'blocked_customers',
                    ['name','email','reason'],
                    AdapterInterface::INDEX_TYPE_FULLTEXT
                ),
                ['name','email','reason'],
                AdapterInterface::INDEX_TYPE_FULLTEXT                    
            );
        }

        $setup->endSetup();
        
    }

    protected function addColumnsInTable(SchemaSetupInterface $setup)
    {
        $setup->startSetup();
        /*Get module table*/
        $tableName = $setup->getTable('blocked_customers');
        /* Check if the table already exists */
        if ($setup->getConnection()->isTableExists($tableName) == true) {
            $columns = [
                'reason' => [
                    'type' => \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                    'nullable' => false,
                    'comment' => 'Reason For Block'
                ],
                'is_active' => [
                    'type' => \Magento\Framework\DB\Ddl\Table::TYPE_SMALLINT,
                    null,
                    'nullable' => false,
                    'unsigned' => true,
                    'default' => '0',
                    'comment' => 'Is Active'
                ],
                'created_at' => [
                    'type' => \Magento\Framework\DB\Ddl\Table::TYPE_TIMESTAMP,
                    null,
                    'nullable' => false,
                    'default' => \Magento\Framework\DB\Ddl\Table::TIMESTAMP_INIT,
                    'comment' => 'Created At'
                ],
                'updated_at' => [
                    'type' => \Magento\Framework\DB\Ddl\Table::TYPE_TIMESTAMP,
                    null,
                    'nullable' => false,
                    'default' => \Magento\Framework\DB\Ddl\Table::TIMESTAMP_INIT_UPDATE,
                    'comment' => 'Updated At'
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
