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

use Magento\Framework\Setup\UpgradeSchemaInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\SchemaSetupInterface;
use Magento\Framework\DB\Ddl\Table;

/**
 * Upgrade the module DB scheme
 */
class UpgradeSchema implements UpgradeSchemaInterface
{
    /**
     * {@inheritdoc}
     */
    public function upgrade(SchemaSetupInterface $setup, ModuleContextInterface $context)
    {
        $setup->startSetup();

        if (version_compare($context->getVersion(), '2.1.0') < 0) {
            $this->addDefaultTimeToSchedule($setup);
        }
        if (version_compare($context->getVersion(), '2.1.2') < 0) {
            $this->saveLargeCategoryMap($setup);
        }

        $setup->endSetup();
    }

    /**
     * @param SchemaSetupInterface $setup
     * @return void
     */
    private function addDefaultTimeToSchedule(SchemaSetupInterface $setup)
    {
        $setup->getConnection()->changeColumn($setup->getTable('rw_shoppingfeeds_feed_schedule'), 'processed_at', 'processed_at', [
            'type' => Table::TYPE_TIMESTAMP,
            'length' => null,
            'NULLABLE' => 0,
            'default' => 'CURRENT_TIMESTAMP',
            'comment' => 'Keeps track of last time schedule was processed.'
        ]);
    }

    /**
     * @param SchemaSetupInterface $setup
     */
    private function saveLargeCategoryMap(SchemaSetupInterface $setup)
    {
        $setup->run('ALTER TABLE `'. $setup->getTable('rw_shoppingfeeds_feed_config'). '` MODIFY `value` MEDIUMTEXT');
    }
}