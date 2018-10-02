<?php

namespace Ziffity\Webforms\Setup;

use Magento\Framework\Setup\InstallSchemaInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\SchemaSetupInterface;
use Magento\Framework\DB\Adapter\AdapterInterface;
use Magento\Framework\DB\Ddl\Table;

class InstallSchema implements InstallSchemaInterface
{
    public function install(SchemaSetupInterface $setup, ModuleContextInterface $context)
    {
        $installer = $setup;
        $installer->startSetup();
        $tableName = $installer->getTable('webforms_customer_data');
        if (!$installer->tableExists('webforms_customer_data')) {
            $table = $installer->getConnection()
                ->newTable($tableName)
                ->addColumn(
                    'cust_id',
                    Table::TYPE_INTEGER,
                    null,
                    [
                        'identity' => true,
                        'unsigned' => true,
                        'nullable' => false,
                        'primary' => true
                    ],
                    'Cust ID'
                )
                ->addColumn(
                    'cust_name',
                    Table::TYPE_TEXT,
                    100,
                    ['nullable' => false, 'default' => 'Nil'],
                    'Name'
                )
                ->addColumn(
                    'cust_email',
                    Table::TYPE_TEXT,
                    100,
                    ['nullable' => false, 'default' => 'Nil'],
                    'Email'
                )
                ->addColumn(
                    'cust_phone',
                    Table::TYPE_TEXT,
                    100,
                    ['nullable' => false, 'default' => 'Nil'],
                    'Phone'
                )      
                ->addColumn(
                    'cust_comments',
                    Table::TYPE_TEXT,
                    255,
                    ['nullable' => false, 'default' => 'Nil'],
                    'Comments'
                )
                ->addColumn(
                    'cust_fn',
                    Table::TYPE_TEXT,
                    100,
                    ['nullable' => false, 'default' => 'Nil'],
                    'First Name'
                )     
                ->addColumn(
                    'cust_ln',
                    Table::TYPE_TEXT,
                    100,
                    ['nullable' => false, 'default' => 'Nil'],
                    'Last Name'
                )
                ->addColumn(
                    'cust_find',
                    Table::TYPE_TEXT,
                    255,
                    ['nullable' => false, 'default' => 'Nil'],
                    'I am Looking For'
                )                    
                ->addColumn(
                    'cust_addr_one',
                    Table::TYPE_TEXT,
                    100,
                    ['nullable' => false, 'default' => 'Nil'],
                    'Address 1'
                )
                ->addColumn(
                    'cust_addr_two',
                    Table::TYPE_TEXT,
                    100,
                    ['nullable' => false, 'default' => 'Nil'],
                    'Address 2'
                )             
                ->addColumn(
                    'cust_city',
                    Table::TYPE_TEXT,
                    100,
                    ['nullable' => false, 'default' => 'Nil'],
                    'City'
                )
                ->addColumn(
                    'cust_state',
                    Table::TYPE_TEXT,
                    100,
                    ['nullable' => false, 'default' => 'Nil'],
                    'State'
                )
                ->addColumn(
                    'cust_zip',
                    Table::TYPE_TEXT,
                    100,
                    ['nullable' => false, 'default' => 'Nil'],
                    'Zipcode'
                )
                ->addColumn(
                    'form_type',
                    Table::TYPE_TEXT,
                    100,
                    ['nullable' => false, 'default' => ''],
                    'Form Type'
                )                    
                ->addColumn(
                    'is_active',
                    Table::TYPE_SMALLINT,
                    null,
                    ['unsigned' => true, 'nullable' => false, 'default' => '0'],
                    'Status'
                )
                ->addColumn(
                    'created_at',
                    Table::TYPE_TIMESTAMP,
                    null,
                    ['nullable' => false, 'default' => Table::TIMESTAMP_INIT],
                    'Created At'
                )
                ->addColumn(
                    'updated_at',
                    Table::TYPE_TIMESTAMP,
                    null,
                    ['nullable' => false, 'default' => Table::TIMESTAMP_INIT],
                    'Updated At'
                )
		->addIndex(
		    $setup->getIdxName(
		    	$installer->getTable('webforms_customer_data'),
		    	['cust_name','cust_email','cust_phone','cust_comments','cust_fn','cust_ln','cust_find','cust_addr_one','cust_addr_two','cust_city','cust_state','cust_zip','form_type'],
		    	AdapterInterface::INDEX_TYPE_FULLTEXT
		    ),
		    ['cust_name','cust_email','cust_phone','cust_comments','cust_fn','cust_ln','cust_find','cust_addr_one','cust_addr_two','cust_city','cust_state','cust_zip','form_type'],
		    ['type' => AdapterInterface::INDEX_TYPE_FULLTEXT]
		);
            $installer->getConnection()->createTable($table);
        }
        $installer->endSetup();
    }
}
