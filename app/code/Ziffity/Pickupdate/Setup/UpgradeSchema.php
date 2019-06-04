<?php

namespace Ziffity\Pickupdate\Setup;

use Magento\Framework\Setup\UpgradeSchemaInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\SchemaSetupInterface;
use Magento\Framework\DB\Ddl\Table;

class UpgradeSchema implements UpgradeSchemaInterface
{
    /**
     * @var Operation\UpgradeTo1611
     */
    private $upgradeTo1611;

    public function __construct(
        Operation\UpgradeTo1611 $upgradeTo1611
    ) {
        $this->upgradeTo1611 = $upgradeTo1611;
    }

    /**
     * @param SchemaSetupInterface $setup
     * @param ModuleContextInterface $context
     */
    public function upgrade(SchemaSetupInterface $setup, ModuleContextInterface $context)
    {
        $setup->startSetup();
        
        if (version_compare($context->getVersion(), '1.0.0', '<')) {
            $this->_addTypeDayColumn($setup);
        }

        if (version_compare($context->getVersion(), '1.0.0', '<')) {
            $this->upgradeTo1611->execute($setup);
        }

        $setup->endSetup();
    }

    /**
     * @param SchemaSetupInterface $setup
     */
    protected function _addTypeDayColumn(SchemaSetupInterface $setup) {
        $table = $setup->getTable('ziffity_pickupdate_holidays');
        $setup->getConnection()
              ->addColumn(
                    $table,
                    'type_day',
                    [
                        'type'      => \Magento\Framework\DB\Ddl\Table::TYPE_SMALLINT,
                        'nullable'  => false,
                        'default'   => '0',
                        'comment'   => 'Day type'
                    ]);
    }
}
