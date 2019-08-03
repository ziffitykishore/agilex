<?php

namespace Wyomind\AdvancedInventory\Model\ResourceModel\Order\Invoice\Grid;


use Magento\Framework\Data\Collection\Db\FetchStrategyInterface as FetchStrategy;
use Magento\Framework\Data\Collection\EntityFactoryInterface as EntityFactory;
use Magento\Framework\Event\ManagerInterface as EventManager;
use Psr\Log\LoggerInterface as Logger;

class Collection extends \Magento\Sales\Model\ResourceModel\Order\Invoice\Grid\Collection
{

    public function __construct(
        EntityFactory $entityFactory,
        Logger $logger,
        FetchStrategy $fetchStrategy,
        EventManager $eventManager,
        string $mainTable = 'sales_invoice_grid',
        string $resourceModel = \Magento\Sales\Model\ResourceModel\Order\Invoice::class)
    {
        parent::__construct($entityFactory, $logger, $fetchStrategy, $eventManager, $mainTable, $resourceModel);

    }


    protected function _beforeLoad()
    {
        $sog = $this->getTable("sales_order_grid");
        $this->getSelect()->joinLeft(
            $sog,
            $sog . ".entity_id = main_table.order_id",
            [
                "assigned_to" => "assigned_to",
                "order_status" => $sog . ".status"
            ]
        );



        parent::_beforeLoad();
    }

}