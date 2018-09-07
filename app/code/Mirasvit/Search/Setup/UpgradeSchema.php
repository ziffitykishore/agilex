<?php
/**
 * Mirasvit
 *
 * This source file is subject to the Mirasvit Software License, which is available at https://mirasvit.com/license/.
 * Do not edit or add to this file if you wish to upgrade the to newer versions in the future.
 * If you wish to customize this module for your needs.
 * Please refer to http://www.magentocommerce.com for more information.
 *
 * @category  Mirasvit
 * @package   mirasvit/module-search
 * @version   1.0.78
 * @copyright Copyright (C) 2018 Mirasvit (https://mirasvit.com/)
 */


namespace Mirasvit\Search\Setup;

use Magento\Framework\Setup\UpgradeSchemaInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\SchemaSetupInterface;
use Magento\Framework\DB\Ddl\Table;

class UpgradeSchema implements UpgradeSchemaInterface
{
    /**
     * {@inheritdoc}
     * @SuppressWarnings(PHPMD)
     */
    public function upgrade(SchemaSetupInterface $setup, ModuleContextInterface $context)
    {
        $installer = $setup;
        $connection = $installer->getConnection();

        if (version_compare($context->getVersion(), '1.0.1') < 0) {
            $connection->dropTable($installer->getTable('mst_search_synonym'));
            $connection->dropTable($installer->getTable('mst_search_stopword'));

            $table = $installer->getConnection()->newTable(
                $installer->getTable('mst_search_synonym')
            )->addColumn(
                'synonym_id',
                Table::TYPE_INTEGER,
                null,
                ['identity' => true, 'unsigned' => true, 'nullable' => false, 'primary' => true],
                'Synonym Id'
            )->addColumn(
                'term',
                Table::TYPE_TEXT,
                255,
                ['nullable' => false],
                'Term'
            )->addColumn(
                'synonyms',
                Table::TYPE_TEXT,
                255,
                ['nullable' => false],
                'Synonyms'
            )->addColumn(
                'store_id',
                Table::TYPE_INTEGER,
                11,
                ['nullable' => false],
                'Store Id'
            )->addIndex(
                $installer->getIdxName('mst_search_synonym', ['term']),
                ['term']
            )->setComment(
                'Synonyms'
            );

            $installer->getConnection()->createTable($table);

            $table = $installer->getConnection()->newTable(
                $installer->getTable('mst_search_stopword')
            )->addColumn(
                'stopword_id',
                Table::TYPE_INTEGER,
                null,
                ['identity' => true, 'unsigned' => true, 'nullable' => false, 'primary' => true],
                'Stopword Id'
            )->addColumn(
                'term',
                Table::TYPE_TEXT,
                255,
                ['nullable' => false],
                'Stopword'
            )->addColumn(
                'store_id',
                Table::TYPE_INTEGER,
                11,
                ['nullable' => false],
                'Store Id'
            )->addIndex(
                $installer->getIdxName('mst_search_stopword', ['term']),
                ['term']
            )->setComment(
                'Stopwords'
            );

            $installer->getConnection()->createTable($table);
        }

        if (version_compare($context->getVersion(), '1.0.3') < 0) {
            $setup->getConnection()->addColumn(
                $setup->getTable($installer->getTable('catalog_product_entity')),
                'search_weight',
                [
                    'type'     => Table::TYPE_INTEGER,
                    'length'   => 11,
                    'nullable' => false,
                    'default'  => 0,
                    'comment'  => 'Search Weight',
                ]
            );
        }

        if (version_compare($context->getVersion(), '1.0.5') < 0) {
            try {
                $setup->getConnection()->changeColumn(
                    $installer->getTable('mst_search_index'),
                    'code',
                    'identifier',
                    [
                        'type'     => Table::TYPE_TEXT,
                        'length'   => 255,
                        'nullable' => false,
                    ]
                );
            } catch (\Exception $e) {
            }
        }
    }
}
