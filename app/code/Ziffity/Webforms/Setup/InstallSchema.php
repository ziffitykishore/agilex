<?php
namespace Ziffity\Webforms\Setup;

use Magento\Framework\Setup\InstallSchemaInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\SchemaSetupInterface;
use Magento\Framework\DB\Adapter\AdapterInterface;
class InstallSchema implements InstallSchemaInterface
{
    public function install(SchemaSetupInterface $setup, ModuleContextInterface $context)
    {
        
        /* Customer Comments Form Table */
        
        $installer = $setup;
        $installer->startSetup();
        $table = $installer->getConnection()->newTable(
            $installer->getTable('customer_comments_details')
        )
        ->addColumn(
            'customer_id',
            \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
            null,
            ['identity' => true, 'unsigned' => true, 'nullable' => false, 'primary' => true],
            'Customer Id'
        )
        ->addColumn(
            'customer_name',
            \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
            255,
            ['nullable' => false],
            'Customer Name'
        )
        ->addColumn(
            'customer_email',
            \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
            255,
            ['nullable' => false],
            'Customer Email'
        )
        ->addColumn(
            'customer_phone',
            \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
            255,
            ['nullable' => false],
            'Customer Phone'
        )
        ->addColumn(
            'customer_comments',
            \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
            255,
            ['nullable' => false],
            'Customer Comments'
        )->addColumn(
            'is_active',
            \Magento\Framework\DB\Ddl\Table::TYPE_SMALLINT,
	    null,
            ['unsigned' => true, 'nullable' => false, 'default' => '0'],
            'Status'
        )->addColumn(
            'created_at',
            \Magento\Framework\DB\Ddl\Table::TYPE_TIMESTAMP,
            null,
            ['nullable' => false, 'default' => \Magento\Framework\DB\Ddl\Table::TIMESTAMP_INIT],
            'Created At'
        )->addColumn(
            'updated_at',
            \Magento\Framework\DB\Ddl\Table::TYPE_TIMESTAMP,
            null,
            ['nullable' => false, 'default' => \Magento\Framework\DB\Ddl\Table::TIMESTAMP_INIT_UPDATE],
            'Updated At'
        )->addIndex(
	    $setup->getIdxName(
	    $installer->getTable('customer_comments_details'),
	    ['customer_name','customer_email','customer_comments'],
	    AdapterInterface::INDEX_TYPE_FULLTEXT
	    ),
	    ['customer_name','customer_email','customer_comments'],
	    ['type' => AdapterInterface::INDEX_TYPE_FULLTEXT]
	)->setComment(
	    'Customer Comments Table'
	);

        $installer->getConnection()->createTable($table);
        $installer->endSetup();
        
        /* Customer Find Your Coin Table */
        
        
        $installer->startSetup();
        $table = $installer->getConnection()->newTable(
            $installer->getTable('find_your_coin')
        )
        ->addColumn(
            'customer_id',
            \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
            null,
            ['identity' => true, 'unsigned' => true, 'nullable' => false, 'primary' => true],
            'Customer Id'
        )
        ->addColumn(
            'customer_fn',
            \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
            255,
            ['nullable' => false],
            'FirstName'
        )
        ->addColumn(
            'customer_ln',
            \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
            255,
            ['nullable' => false],
            'LastName'
        )
        ->addColumn(
            'customer_email',
            \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
            255,
            ['nullable' => false],
            'Email'
        )
        ->addColumn(
            'customer_phone',
            \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
            255,
            ['nullable' => false],
            'Phone'
        )->addColumn(
            'customer_find',
            \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
            255,
            ['nullable' => false],
            'I am Looking For'
        )->addColumn(
            'is_active',
            \Magento\Framework\DB\Ddl\Table::TYPE_SMALLINT,
	    null,
            ['unsigned' => true, 'nullable' => false, 'default' => '0'],
            'Status'
        )->addColumn(
            'created_at',
            \Magento\Framework\DB\Ddl\Table::TYPE_TIMESTAMP,
            null,
            ['nullable' => false, 'default' => \Magento\Framework\DB\Ddl\Table::TIMESTAMP_INIT],
            'Created At'
        )->addColumn(
            'updated_at',
            \Magento\Framework\DB\Ddl\Table::TYPE_TIMESTAMP,
            null,
            ['nullable' => false, 'default' => \Magento\Framework\DB\Ddl\Table::TIMESTAMP_INIT_UPDATE],
            'Updated At'
        )->addIndex(
	    $setup->getIdxName(
	    $installer->getTable('find_your_coin'),
	    ['customer_fn','customer_ln','customer_email','customer_phone','customer_find'],
	    AdapterInterface::INDEX_TYPE_FULLTEXT
	    ),
	    ['customer_fn','customer_ln','customer_email','customer_phone','customer_find'],
	    ['type' => AdapterInterface::INDEX_TYPE_FULLTEXT]
	)->setComment(
	    'Find Your Coin Table'
	);

        $installer->getConnection()->createTable($table);
        $installer->endSetup();
        
        
        /* Customer Catalog Request Table */
        
        
        $installer->startSetup();
        $table = $installer->getConnection()->newTable(
            $installer->getTable('catalog_request')
        )
        ->addColumn(
            'customer_id',
            \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
            null,
            ['identity' => true, 'unsigned' => true, 'nullable' => false, 'primary' => true],
            'Customer Id'
        )
        ->addColumn(
            'customer_fn',
            \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
            255,
            ['nullable' => false],
            'FirstName'
        )
        ->addColumn(
            'customer_ln',
            \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
            255,
            ['nullable' => false],
            'LastName'
        )
        ->addColumn(
            'customer_addr_one',
            \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
            255,
            ['nullable' => false],
            'Address 1'
        )
        ->addColumn(
            'customer_addr_two',
            \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
            255,
            ['nullable' => false],
            'Address 2'
        )->addColumn(
            'customer_city',
            \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
            255,
            ['nullable' => false],
            'City'
        )->addColumn(
            'customer_state',
            \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
            255,
            ['nullable' => false],
            'State'
        )->addColumn(
            'customer_zip',
            \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
            255,
            ['nullable' => false],
            'Zipcode'
        )->addColumn(
            'is_active',
            \Magento\Framework\DB\Ddl\Table::TYPE_SMALLINT,
	    null,
            ['unsigned' => true, 'nullable' => false, 'default' => '0'],
            'Status'
        )->addColumn(
            'created_at',
            \Magento\Framework\DB\Ddl\Table::TYPE_TIMESTAMP,
            null,
            ['nullable' => false, 'default' => \Magento\Framework\DB\Ddl\Table::TIMESTAMP_INIT],
            'Created At'
        )->addColumn(
            'updated_at',
            \Magento\Framework\DB\Ddl\Table::TYPE_TIMESTAMP,
            null,
            ['nullable' => false, 'default' => \Magento\Framework\DB\Ddl\Table::TIMESTAMP_INIT_UPDATE],
            'Updated At'
        )->addIndex(
	    $setup->getIdxName(
	    $installer->getTable('catalog_request'),
	    ['customer_fn','customer_ln','customer_addr_one','customer_addr_two','customer_city','customer_state','customer_zip'],
	    AdapterInterface::INDEX_TYPE_FULLTEXT
	    ),
	    ['customer_fn','customer_ln','customer_addr_one','customer_addr_two','customer_city','customer_state','customer_zip'],
	    ['type' => AdapterInterface::INDEX_TYPE_FULLTEXT]
	)->setComment(
	    'Catalog Request Table'
	);

        $installer->getConnection()->createTable($table);
        $installer->endSetup();
        
        
        
    }
}


