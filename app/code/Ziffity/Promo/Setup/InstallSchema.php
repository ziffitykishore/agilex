<?php

namespace Ziffity\Promo\Setup;

use Magento\Framework\Setup\InstallSchemaInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\SchemaSetupInterface;

class InstallSchema implements InstallSchemaInterface {

    public function install(SchemaSetupInterface $setup, ModuleContextInterface $context) {
        $installer = $setup;
        $installer->startSetup();
        if (version_compare($context->getVersion(), '1.0.0') < 0) {
            $fieldsSql = 'SHOW COLUMNS FROM ' . $installer->getTable('salesrule');
            $cols = $installer->getConnection()->fetchCol($fieldsSql);
            if (!in_array('promo_sku', $cols)) {
                $installer->run("ALTER TABLE `{$installer->getTable('salesrule')}` ADD `promo_sku` TEXT");
            }
        }
        $installer->endSetup();
    }
}
