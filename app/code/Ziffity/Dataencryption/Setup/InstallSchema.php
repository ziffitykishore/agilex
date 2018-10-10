<?php
/**
 * Copyright © 2015 Ziffity. All rights reserved.
 */

namespace Ziffity\Dataencryption\Setup;

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

        $setup->startSetup();
 
        $table = $setup->getConnection()->newTable(
            $setup->getTable('ziffity_dataencryption')
        )->addColumn(
            'id',
            \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
            null,
            ['identity' => true, 'unsigned' => true, 'nullable' => false, 'primary' => true],
            'Id'
        )->addColumn(
            'filename',
            \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
            255,
            [],
            'File Name'
        )->addColumn(
            'mail_sent',
            \Magento\Framework\DB\Ddl\Table::TYPE_BOOLEAN,
            64,
            [],
            'mail_sent'
        ) ->addColumn(
            'mail_sent_date',
            \Magento\Framework\DB\Ddl\Table::TYPE_DATETIME,
            64,
            [],
            'mail_sent_date'
        )->setComment(
            'Ziffity Dataencryption'
        );
        $setup->getConnection()->createTable($table);
 
        $setup->endSetup();


    }
}
