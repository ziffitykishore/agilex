<?php

namespace Ziffity\Banners\Setup;

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
        $tableName = $installer->getTable('ziffity_images');

        if (!$installer->tableExists('ziffity_images')) {
            $table = $installer->getConnection()
                ->newTable($tableName)
                ->addColumn(
                    'image_id',
                    Table::TYPE_INTEGER,
                    null,
                    [
                        'identity' => true,
                        'unsigned' => true,
                        'nullable' => false,
                        'primary' => true
                    ],
                    'Image ID'
                )
                ->addColumn(
                    'image',
                    Table::TYPE_TEXT,
                    255,
                    array(
                        'nullable'  => false,
                    ),
                    'Image'
                )
                ->addColumn(
                    'image_code',
                    Table::TYPE_TEXT,
                    100,
                    array(
                        'nullable' => false,
                    ),
                    'Image Code'
                )->addColumn(
                    'position',
                    Table::TYPE_INTEGER,
                    255,
                    array(
                        'nullable' => false,
                    ),
                    'Image Postion'
                )->addColumn(
                    'link',
                    Table::TYPE_TEXT,
                    255,
                    array(
                        'nullable' => true,
                    ),
                    'Link'
                );
            $installer->getConnection()->createTable($table);
        }
        $installer->endSetup();
    }
}
