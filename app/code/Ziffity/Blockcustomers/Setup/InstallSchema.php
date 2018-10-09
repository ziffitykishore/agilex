<?php
/**
 * Copyright © 2015 Ziffity. All rights reserved.
 */

namespace Ziffity\Blockcustomers\Setup;

use Magento\Framework\Setup\InstallSchemaInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\SchemaSetupInterface;

/**
 * @codeCoverageIgnore
 */
class InstallSchema implements InstallSchemaInterface
{
    /**
     * {@inheritdoc}
     */
    public function install(SchemaSetupInterface $setup, ModuleContextInterface $context)
    {
	
        $installer = $setup;
        $installer->startSetup();
        $table = $installer->getConnection()->newTable($installer->getTable('blocked_customers'))
        ->addColumn(
        'id',
        \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
        null,
        ['identity' => true, 'unsigned' => true, 'nullable' => false, 'primary' => true],
        'ID'
        )
        ->addColumn(
        'name',
        \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
        '64k',
        [],
        'Name'
        )
        ->addColumn(
        'email',
        \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
        '64k',
        [],
        'Email'
        )
        ->setComment(
            'Ziffity Blockcustomers blocked_customers'
        );
        $installer->getConnection()->createTable($table);
        $installer->endSetup();
    }
}
