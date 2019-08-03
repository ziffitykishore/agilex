<?php
/**
 * Copyright © 2019 Wyomind. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace Wyomind\MassStockUpdate\Setup;

use Magento\Framework\Setup\UpgradeSchemaInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\SchemaSetupInterface;
use Magento\Framework\DB\Ddl\Table;

/**
 * Upgrade schema for Simple Google Shopping
 */
class UpgradeSchema implements UpgradeSchemaInterface
{

    /**
     * @param SchemaSetupInterface $setup
     * @param ModuleContextInterface $context
     */
    public function upgrade(SchemaSetupInterface $setup,
                            ModuleContextInterface $context)
    {

        $installer = $setup;
        $installer->startSetup();
        // $context->getVersion() = version du module actuelle
        // 4.0.0 = version en cours d'installation
        if (version_compare($context->getVersion(), '5.0.2') < 0) {

            $tableName = $setup->getTable('massstockupdate_profiles');

            if ($setup->getConnection()->isTableExists($tableName) == true) {


                // webservice
                $setup->getConnection()->addColumn(
                    $tableName, 'webservice_params', ['type' => Table::TYPE_TEXT, 'length' => 900, 'nullable' => true, "comment" => 'Webservice params']
                );
                $setup->getConnection()->addColumn(
                    $tableName, 'webservice_login', ['type' => Table::TYPE_TEXT, 'length' => 300, 'nullable' => true, "comment" => 'Webservice login']
                );
                $setup->getConnection()->addColumn(
                    $tableName, 'webservice_password', ['type' => Table::TYPE_TEXT, 'length' => 300, 'nullable' => true, "comment" => 'Webservice password']
                );

                $setup->getConnection()->addColumn(
                    $tableName, 'default_values', ['type' => Table::TYPE_TEXT, 'length' => 900, 'nullable' => true, "comment" => 'Default Values']
                );
                $setup->getConnection()->dropColumn($tableName, "auto_set_total");
            }

            $installer->endSetup();
        }
        if (version_compare($context->getVersion(), '6.0.0') < 0) {


            $installer = $setup;
            $installer->startSetup();


            $tableName = $installer->getTable('massstockupdate_profiles');
            // webservice 
            $setup->getConnection()->addColumn(
                $tableName, 'xml_column_mapping', ['type' => \Magento\Framework\DB\Ddl\Table::TYPE_TEXT, "comment" => 'Xml columns order']
            );
            $setup->getConnection()->addColumn(
                $tableName, 'preserve_xml_column_mapping', ['type' => \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER, 'length' => 1, 'nullable' => false, "default" => 0, "comment" => 'Preserve the xml column order']
            );


            $installer->endSetup();
        }
        if (version_compare($context->getVersion(), '6.1.0.1') < 0) {
            $installer = $setup;
            $installer->startSetup();
            $tableName = $installer->getTable('massstockupdate_profiles');
            $setup->getConnection()->addColumn(
                $tableName, 'line_filter', ['type' => \Magento\Framework\DB\Ddl\Table::TYPE_TEXT, 'length' => 300, 'nullable' => false, "comment" => 'Line filter']
            );
            $setup->getConnection()->addColumn(
                $tableName, 'has_header', ['type' => \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER, 'length' => 1, 'nullable' => false, "default" => 0, "comment" => 'Has header']
            );
            $setup->getConnection()->addColumn(
                $tableName, 'profile_method', ['type' => \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER, 'length' => 1, 'nullable' => false, "default" => 1, "comment" => 'Profile method']
            );
            $installer->endSetup();
        }

        if (version_compare($context->getVersion(), '7.0.0') < 0) {

            $installer = $setup;
            $installer->startSetup();

            $tableName = $installer->getTable('massstockupdate_profiles');
            $setup->getConnection()->addColumn(
                $tableName, 'dropbox_token', ['type' => \Magento\Framework\DB\Ddl\Table::TYPE_TEXT, 'length' => 300, 'nullable' => false, "comment" => 'Dropbox token']
            );

            $setup->getConnection()->addColumn(
                $tableName, 'post_process_action', ['type' => \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER, 'length' => 1, 'nullable' => false, 'default' => 0, "comment" => 'Post process action']
            );
            $setup->getConnection()->addColumn(
                $tableName, 'post_process_move_folder', ['type' => \Magento\Framework\DB\Ddl\Table::TYPE_TEXT, 'length' => 300, 'nullable' => true, "comment" => 'Post process: move folder']
            );
            $setup->getConnection()->addColumn(
                $tableName, 'identifier_script', ['type' => \Magento\Framework\DB\Ddl\Table::TYPE_TEXT, 'length' => 900, 'nullable' => true, "comment" => 'Script for the identifier']
            );


            $installer->endSetup();
        }

        if (version_compare($context->getVersion(), '7.4.1') < 0) {

            $installer = $setup;
            $installer->startSetup();

            $tableName = $installer->getTable('massstockupdate_profiles');

            $setup->getConnection()->addColumn(
                $tableName, 'post_process_indexers', ['type' => \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER, 'length' => 1, 'nullable' => false, 'default' => 1, "comment" => 'Run indexes after import']
            );


            $installer->endSetup();
        }
        if (version_compare($context->getVersion(), '8.0.2') < 0) {
            $installer = $setup;
            $installer->startSetup();
            $tableName = $installer->getTable('massstockupdate_profiles');

            $setup->getConnection()->dropColumn(
                $tableName, 'auto_set_total'
            );

            $installer->endSetup();
        }

        if (version_compare($context->getVersion(), '8.4.0') < 0) {
            $installer = $setup;
            $installer->startSetup();
            $tableName = $installer->getTable('massstockupdate_profiles');


            $setup->getConnection()->addColumn(
                $tableName, 'is_magento_export', ['type' => \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER, 'length' => 1, 'nullable' => false, 'default' => 2, "comment" => 'Magento export file']
            );

            $installer->endSetup();
        }

        if (version_compare($context->getVersion(), '9.3.0') < 0) {

            $installer = $setup;
            $installer->startSetup();

            $tableName = $installer->getTable('massstockupdate_profiles');

            $setup->getConnection()->addColumn(
                $tableName, 'post_process_indexers_selection', ['type' => \Magento\Framework\DB\Ddl\Table::TYPE_TEXT, 'length' => 900, 'nullable' => false,  "comment" => 'List of  indexes to run after import']
            );


            $installer->endSetup();
        }
    }

}
