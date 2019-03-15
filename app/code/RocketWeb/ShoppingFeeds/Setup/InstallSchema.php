<?php
/**
 * RocketWeb
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 *
 * @category  RocketWeb
 * @package   RocketWeb_ShoppingFeeds
 * @copyright Copyright (c) 2016 RocketWeb (http://rocketweb.com)
 * @license   http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 * @author    Rocket Web Inc.
 */


/**
 * @category   RocketWeb
 * @package    RocketWeb_ShoppingFeeds
 * @author     RocketWeb
 */
namespace RocketWeb\ShoppingFeeds\Setup;

use Magento\Framework\Setup\InstallSchemaInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\SchemaSetupInterface;
use Magento\Framework\DB\Ddl\Table as Table;

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

        /**
         * Create table rw_shoppingfeeds_feed
         */
        $tableNameFeed = $installer->getTable('rw_shoppingfeeds_feed');
        if (!$installer->getConnection()->isTableExists($tableNameFeed)) {
            $tableFeed = $installer->getConnection()->newTable($tableNameFeed)
                ->addColumn('id',
                    Table::TYPE_INTEGER,
                    null,
                    array('identity' => true, 'unsigned' => true, 'nullable' => false, 'primary' => true),
                    'Feed Id'
                )->addColumn('store_id',
                    Table::TYPE_INTEGER,
                    null,
                    ['nullable' => false, 'unsigned' => true],
                    'Store Id'
                )->addColumn('name',
                    Table::TYPE_TEXT,
                    255,
                    ['nullable' => false, 'default' => ''],
                    'Name'
                )->addColumn('type',
                    Table::TYPE_TEXT,
                    100,
                    ['nullable' => false, 'default' => 'generic'],
                    'Type'
                )->addColumn('status',
                    Table::TYPE_INTEGER,
                    null,
                    ['nullable' => false, 'default' => 1, 'unsigned' => true],
                    'Status'
                )->addColumn('messages',
                    Table::TYPE_TEXT,
                    1500,
                    ['nullable' => false],
                    'Messages'
                )->addColumn('created_at',
                    Table::TYPE_TIMESTAMP,
                    null,
                    ['nullable' => false, 'default' => 'CURRENT_TIMESTAMP'],
                    'Created At Timestamp'
                )->addColumn('updated_at',
                    Table::TYPE_TIMESTAMP,
                    null,
                    ['nullable' => false, 'default' => 'CURRENT_TIMESTAMP'],
                    'Updated At Timestamp'
                )->addIndex(
                    $installer->getIdxName(
                        $tableNameFeed,
                        ['name','type'],
                        \Magento\Framework\DB\Adapter\AdapterInterface::INDEX_TYPE_FULLTEXT
                    ),
                    ['name','type'],
                    ['type' => \Magento\Framework\DB\Adapter\AdapterInterface::INDEX_TYPE_FULLTEXT]
                )->setComment('Feeds table')
                ->setOption('type', 'InnoDB')
                ->setOption('charset', 'utf8');
            $installer->getConnection()->createTable($tableFeed);
        }

        /**
         * Create table rw_shoppingfeeds_feed_config
         */
        $tableNameFeedConfig = $installer->getTable('rw_shoppingfeeds_feed_config');
        if (!$installer->getConnection()->isTableExists($tableNameFeedConfig)) {
            $tableFeedConfig = $installer->getConnection()->newTable($tableNameFeedConfig)
                ->addColumn('id',
                    Table::TYPE_INTEGER,
                    null,
                    array('identity' => true, 'unsigned' => true, 'nullable' => false, 'primary' => true),
                    'Config Id'
                )->addColumn('feed_id',
                    Table::TYPE_INTEGER,
                    null,
                    ['nullable' => false, 'unsigned' => true],
                    'Feed Id'
                )->addColumn('path',
                    Table::TYPE_TEXT,
                    255,
                    ['nullable' => false, 'default' => ''],
                    'Configuration path'
                )->addColumn('value',
                    Table::TYPE_TEXT,
                    null,
                    ['nullable' => false, 'default' => ''],
                    'Value'
                )->addForeignKey(
                    $installer->getFkName(
                        $tableNameFeedConfig,
                        'feed_id',
                        $tableNameFeed,
                        'id'
                    ),
                    'feed_id',
                    $tableNameFeed,
                    'id',
                    Table::ACTION_CASCADE
                )->addIndex(
                    $installer->getIdxName($tableNameFeedConfig, 'path'),
                    ['path']
                )->addIndex(
                    $installer->getIdxName($tableNameFeedConfig, 'feed_id'),
                    ['feed_id']
//                )->addIndex(
//                    $installer->getIdxName(
//                        $tableNameFeedConfig,
//                        ['feed_id', 'path'],
//                        \Magento\Framework\DB\Adapter\AdapterInterface::INDEX_TYPE_UNIQUE
//                    ),
//                    ['feed_id', 'path'],
//                    ['type' => \Magento\Framework\DB\Adapter\AdapterInterface::INDEX_TYPE_UNIQUE]
                )->setComment('Feed Configs table')
                ->setOption('type', 'InnoDB')
                ->setOption('charset', 'utf8');
            $installer->getConnection()->createTable($tableFeedConfig);
        }

        /**
         * Create table rw_shoppingfeeds_feed_schedule
         */
        $tableNameFeedSchedule = $installer->getTable('rw_shoppingfeeds_feed_schedule');
        if (!$installer->getConnection()->isTableExists($tableNameFeedSchedule)) {
            $tableFeedSchedule = $installer->getConnection()->newTable($tableNameFeedSchedule)
                ->addColumn('id',
                    Table::TYPE_INTEGER,
                    null,
                    array('identity' => true, 'unsigned' => true, 'nullable' => false, 'primary' => true),
                    'Schedule Id'
                )->addColumn('feed_id',
                    Table::TYPE_INTEGER,
                    null,
                    ['nullable' => false, 'unsigned' => true],
                    'Feed Id'
                )->addColumn('start_at',
                    Table::TYPE_SMALLINT,
                    null,
                    ['nullable' => false],
                    'Schedule hour'
                )->addColumn('processed_at',
                    Table::TYPE_TIMESTAMP,
                    null,
                    ['nullable' => true],
                    'Processed At Timestamp'
                )->addColumn('batch_mode',
                    Table::TYPE_SMALLINT,
                    null,
                    ['nullable' => false, 'default' => 0, 'unsigned' => true],
                    'Batch Mode Status'
                )->addColumn('batch_limit',
                    Table::TYPE_SMALLINT,
                    null,
                    ['nullable' => false, 'unsigned' => true],
                    'Number of processed products on batch run'
                )->addForeignKey(
                    $installer->getFkName(
                        $tableNameFeedSchedule,
                        'feed_id',
                        $tableNameFeed,
                        'id'
                    ),
                    'feed_id',
                    $tableNameFeed,
                    'id',
                    Table::ACTION_CASCADE
                )->addIndex(
                    $installer->getIdxName($tableNameFeedSchedule, 'feed_id'),
                    ['feed_id']
                )->addIndex(
                    $installer->getIdxName(
                        $tableNameFeedSchedule,
                        ['feed_id', 'start_at'],
                        \Magento\Framework\DB\Adapter\AdapterInterface::INDEX_TYPE_UNIQUE
                    ),
                    ['feed_id', 'start_at'],
                    ['type' => \Magento\Framework\DB\Adapter\AdapterInterface::INDEX_TYPE_UNIQUE]
                )->setComment('Feed Schedule table')
                ->setOption('type', 'InnoDB')
                ->setOption('charset', 'utf8');
            $installer->getConnection()->createTable($tableFeedSchedule);
        }

        /**
         * Create table rw_shoppingfeeds_feed_queue
         */
        $tableNameFeedQueue = $installer->getTable('rw_shoppingfeeds_feed_queue');
        if (!$installer->getConnection()->isTableExists($tableNameFeedQueue)) {
            $tableFeedQueue = $installer->getConnection()->newTable($tableNameFeedQueue)
                ->addColumn('id',
                    Table::TYPE_INTEGER,
                    null,
                    array('identity' => true, 'unsigned' => true, 'nullable' => false, 'primary' => true),
                    'Queue Id'
                )->addColumn('feed_id',
                    Table::TYPE_INTEGER,
                    null,
                    ['nullable' => false, 'unsigned' => true],
                    'Feed Id'
                )->addColumn('schedule_id',
                    Table::TYPE_INTEGER,
                    null,
                    ['nullable' => false, 'unsigned' => true],
                    'Schedule Id'
                )->addColumn('message',
                    Table::TYPE_TEXT,
                    1500,
                    ['nullable' => false],
                    'Message'
                )->addColumn('is_read',
                    Table::TYPE_SMALLINT,
                    null,
                    ['nullable' => false, 'default' => 0],
                    'Batch Mode Status'
                )->addColumn('created_at',
                    Table::TYPE_TIMESTAMP,
                    null,
                    ['nullable' => false, 'default' => 'CURRENT_TIMESTAMP'],
                    'Processed At Timestamp'
                )->addForeignKey(
                    $installer->getFkName(
                        $tableNameFeedQueue,
                        'feed_id',
                        $tableNameFeed,
                        'id'
                    ),
                    'feed_id',
                    $tableNameFeed,
                    'id',
                    Table::ACTION_CASCADE
                )->addIndex(
                    $installer->getIdxName($tableNameFeedQueue, 'feed_id'),
                    ['feed_id']
                )->setComment('Feed Queue table')
                ->setOption('type', 'InnoDB')
                ->setOption('charset', 'utf8');
            $installer->getConnection()->createTable($tableFeedQueue);
        }

        /**
         * Create table rw_shoppingfeeds_feed_upload
         */
        $tableNameFeedUpload = $installer->getTable('rw_shoppingfeeds_feed_upload');
        if (!$installer->getConnection()->isTableExists($tableNameFeedUpload)) {
            $tableFeedUpload = $installer->getConnection()->newTable($tableNameFeedUpload)
                ->addColumn('id',
                    Table::TYPE_INTEGER,
                    null,
                    array('identity' => true, 'unsigned' => true, 'nullable' => false, 'primary' => true),
                    'Upload Id'
                )->addColumn('feed_id',
                    Table::TYPE_INTEGER,
                    null,
                    ['nullable' => false, 'unsigned' => true],
                    'Feed Id'
                )->addColumn('username',
                    Table::TYPE_TEXT,
                    255,
                    ['nullable' => false],
                    'Upload Username'
                )->addColumn('password',
                    Table::TYPE_TEXT,
                    255,
                    ['nullable' => false],
                    'Upload Password'
                )->addColumn('host',
                    Table::TYPE_TEXT,
                    255,
                    ['nullable' => false],
                    'Upload Host'
                )->addColumn('port',
                    Table::TYPE_INTEGER,
                    null,
                    ['nullable' => false, 'unsigned' => true],
                    'Upload Port'
                )->addColumn('path',
                    Table::TYPE_TEXT,
                    255,
                    ['nullable' => false],
                    'Upload Path'
                )->addColumn('mode',
                    Table::TYPE_TEXT,
                    255,
                    ['nullable' => true],
                    'Mode - FTP or SFTP'
                )->addColumn('gzip',
                    Table::TYPE_SMALLINT,
                    null,
                    ['nullable' => false, 'unsigned' => true],
                    'Gzip file before sending or not'
                )->addForeignKey(
                    $installer->getFkName(
                        $tableNameFeedUpload,
                        'feed_id',
                        $tableNameFeed,
                        'id'
                    ),
                    'feed_id',
                    $tableNameFeed,
                    'id',
                    Table::ACTION_CASCADE
                )->addIndex(
                    $installer->getIdxName($tableNameFeedUpload, 'feed_id'),
                    ['feed_id']
                )->setComment('Feed Uploads table')
                ->setOption('type', 'InnoDB')
                ->setOption('charset', 'utf8');
            $installer->getConnection()->createTable($tableFeedUpload);
        }

        /**
         * Create table rw_shoppingfeeds_process
         */
        $tableNameProcess = $installer->getTable('rw_shoppingfeeds_process');
        if (!$installer->getConnection()->isTableExists($tableNameProcess)) {
            $tableProcess = $installer->getConnection()->newTable($tableNameProcess)
                ->addColumn('id',
                    Table::TYPE_INTEGER,
                    null,
                    array('identity' => true, 'unsigned' => true, 'nullable' => false, 'primary' => true),
                    'Process Id'
                )->addColumn('item_id',
                    Table::TYPE_INTEGER,
                    null,
                    ['nullable' => false, 'unsigned' => true],
                    'Item Id'
                )->addColumn('parent_item_id',
                    Table::TYPE_INTEGER,
                    null,
                    ['nullable' => false, 'unsigned' => true],
                    'Parent Item Id'
                )->addColumn('feed_id',
                    Table::TYPE_INTEGER,
                    null,
                    ['nullable' => false, 'unsigned' => true],
                    'Feed Id'
                )->addColumn('status',
                    Table::TYPE_INTEGER,
                    null,
                    ['nullable' => false, 'unsigned' => true],
                    'Status'
                )->addColumn('updated_at',
                    Table::TYPE_TIMESTAMP,
                    null,
                    ['nullable' => false, 'default' => 'CURRENT_TIMESTAMP on update CURRENT_TIMESTAMP'],
                    'Updated At Timestamp'
                )->addIndex(
                    $installer->getIdxName($tableNameProcess, ['item_id', 'feed_id']),
                    ['item_id', 'feed_id']
                )->addIndex(
                    $installer->getIdxName($tableNameProcess, ['parent_item_id']),
                    ['parent_item_id']
                )->addIndex(
                    $installer->getIdxName($tableNameProcess, ['status']),
                    ['status']
                )->addIndex(
                    $installer->getIdxName($tableNameProcess, ['updated_at']),
                    ['updated_at']
                )->addIndex(
                    $installer->getIdxName($tableNameProcess, 'feed_id'),
                    ['feed_id']
                )->setComment('Process table')
                ->setOption('type', 'memory')
                ->setOption('charset', 'utf8');
            $installer->getConnection()->createTable($tableProcess);
        }

        /**
         * Create table rw_shoppingfeeds_shipping
         */
        $tableNameShipping = $installer->getTable('rw_shoppingfeeds_shipping');
        if (!$installer->getConnection()->isTableExists($tableNameShipping)) {
            $tableShipping = $installer->getConnection()->newTable($tableNameShipping)
                ->addColumn('id',
                    Table::TYPE_INTEGER,
                    null,
                    array('identity' => true, 'unsigned' => true, 'nullable' => false, 'primary' => true),
                    'Shipping Id'
                )->addColumn('product_id',
                    Table::TYPE_INTEGER,
                    null,
                    ['nullable' => false, 'unsigned' => true],
                    'Product Id'
                )->addColumn('store_id',
                    Table::TYPE_INTEGER,
                    null,
                    ['nullable' => false, 'unsigned' => true],
                    'Store Id'
                )->addColumn('feed_id',
                    Table::TYPE_INTEGER,
                    null,
                    ['nullable' => false, 'unsigned' => true],
                    'Feed Id'
                )->addColumn('currency_code',
                    Table::TYPE_TEXT,
                    3,
                    ['nullable' => false],
                    'Currency code'
                )->addColumn('updated_at',
                    Table::TYPE_TIMESTAMP,
                    null,
                    ['nullable' => false, 'default' => 'CURRENT_TIMESTAMP on update CURRENT_TIMESTAMP'],
                    'Updated At Timestamp'
                )->addColumn('value',
                    Table::TYPE_TEXT,
                    255,
                    ['nullable' => false, 'default' => ''],
                    'Value'
                )->addIndex(
                    $installer->getIdxName($tableNameShipping, ['product_id', 'store_id', 'feed_id', 'currency_code']),
                    ['product_id', 'store_id', 'feed_id', 'currency_code']
                )->addIndex(
                    $installer->getIdxName($tableNameShipping, ['updated_at']),
                    ['updated_at']
                )->addIndex(
                    $installer->getIdxName($tableNameShipping, 'feed_id'),
                    ['feed_id']
                )->setComment('Shipping table')
                ->setOption('type', 'InnoDB')
                ->setOption('charset', 'utf8');
            $installer->getConnection()->createTable($tableShipping);
        }

        $installer->endSetup();
    }
}