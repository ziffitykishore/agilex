<?php

namespace Ziffity\Pickupdate\Setup\Operation;

use Magento\Framework\Setup\SchemaSetupInterface;
use Magento\Framework\DB\Ddl\Table;
use Ziffity\Pickupdate\Model\ResourceModel\Pickupdate;
use Ziffity\Pickupdate\Api\Data\PickupdateInterface;

/**
 * Class UpgradeTo1611
 */
class UpgradeTo1611
{
    /**
     * @param SchemaSetupInterface $setup
     */
    public function execute(SchemaSetupInterface $setup)
    {
        $this->changeCommentColumn($setup);
    }

    /**
     * @param SchemaSetupInterface $setup
     */
    private  function changeCommentColumn(SchemaSetupInterface $setup)
    {
        $tableName = $setup->getTable(Pickupdate::MAIN_TABLE);

        $setup->getConnection()->changeColumn(
            $tableName,
            PickupdateInterface::COMMENT,
            PickupdateInterface::COMMENT,
            [
                'type' => Table::TYPE_TEXT,
                'length' => '64k',
                'nullable' => false
            ]
        );
    }
}
