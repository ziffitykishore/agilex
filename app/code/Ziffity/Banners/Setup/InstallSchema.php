<?php
/*
 * Ziffity_Banners
 */
namespace Ziffity\Banners\Setup;

use Magento\Framework\DB\Ddl\Table;
use Magento\Framework\Setup\InstallSchemaInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\SchemaSetupInterface;

class InstallSchema implements InstallSchemaInterface
{

    /**
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     * @param SchemaSetupInterface $setup
     * @param ModuleContextInterface $context
     */
    public function install(SchemaSetupInterface $setup, ModuleContextInterface $context)
    {
        $installer = $setup;
        $installer->startSetup();
        $tableName = $installer->getTable('ziffity_banners');

        if (!$installer->tableExists('ziffity_banners')) {
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
                );
            $installer->getConnection()->createTable($table);
        }
        $installer->endSetup();
    }
}
