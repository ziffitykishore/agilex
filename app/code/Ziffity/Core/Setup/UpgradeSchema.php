<?php

namespace Ziffity\Core\Setup;

use Magento\Framework\Setup\UpgradeSchemaInterface;
use Magento\Framework\Setup\SchemaSetupInterface;
use Magento\Framework\Setup\ModuleContextInterface;

class UpgradeSchema implements UpgradeSchemaInterface
{

    const SALES_ORDER_TABLE = 'sales_order';
    const QUOTE_TABLE = 'quote';

    public function upgrade(SchemaSetupInterface $setup, ModuleContextInterface $context)
    {
        $setup->startSetup();
        if (version_compare($context->getVersion(), '1.0.1', '<')) {
            $this->updateQuoteTable($setup);
            $this->updateSalesOrderTable($setup);
        }
        $setup->endSetup();
    }

    private function getDefinitions($length, $comment)
    {
        return  [
                    'type' => \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                    'length' => $length,
                    'nullable' => true,
                    'comment' => $comment
                ];
    }

    public function updateSalesOrderTable($setup)
    {
        if ($setup->getConnection()->isTableExists(self::SALES_ORDER_TABLE) == true) {
            $setup->getConnection()->modifyColumn(
                $setup->getTable(self::SALES_ORDER_TABLE),
                'remote_ip',
                $this->getDefinitions(45, 'Remote IP')
            );
            $setup->getConnection()->modifyColumn(
                $setup->getTable(self::SALES_ORDER_TABLE),
                'x_forwarded_for',
                $this->getDefinitions(250, 'X Forwarded For')
            );
        }
    }

    public function updateQuoteTable($setup)
    {
        if ($setup->getConnection()->isTableExists(self::QUOTE_TABLE) == true) {
            $setup->getConnection()->modifyColumn(
                $setup->getTable(self::QUOTE_TABLE),
                'remote_ip',
                $this->getDefinitions(45, 'Remote IP')
            );
        }
    }
}
