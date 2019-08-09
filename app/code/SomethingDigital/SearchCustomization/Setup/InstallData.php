<?php
namespace SomethingDigital\SearchCustomization\Setup;
 
use Magento\Framework\DB\Ddl\Table;
use Magento\Framework\Setup\InstallDataInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\ModuleDataSetupInterface;
 
/**
 * @codeCoverageIgnore
 */
class InstallData implements InstallDataInterface
{
    public function install(ModuleDataSetupInterface $setup, ModuleContextInterface $context)
    { 
        $setup->startSetup();
        $connection = $setup->getConnection();

        $connection->addColumn(
            $setup->getTable('quote'),
            'suffix',
            [
                'type' => \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                'length' => 10,
                'nullable' => true,
                'comment' => 'Suffix'
            ]
        );
        $setup->endSetup();
    }
}