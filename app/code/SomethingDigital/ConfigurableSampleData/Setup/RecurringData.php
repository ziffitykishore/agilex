<?php

namespace SomethingDigital\ConfigurableSampleData\Setup;

use Magento\Framework\App\State;
use Magento\Framework\Setup\InstallDataInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\ModuleDataSetupInterface;
use Magento\Indexer\Model\Indexer\CollectionFactory;

/**
 * Class RecurringData
 *
 * @package SomethingDigital\ConfigurableSampleData\Setup
 */
class RecurringData implements InstallDataInterface
{
    /**
     * @var State
     */
    private $state;

    /**
     * @var CollectionFactory
     */
    private $indexerCollectionFactory;

    /**
     * RecurringData constructor
     *
     * @param State             $state
     * @param CollectionFactory $indexerCollectionFactory
     */
    public function __construct(
        State $state,
        CollectionFactory $indexerCollectionFactory
    ) {
        $this->state = $state;
        $this->indexerCollectionFactory = $indexerCollectionFactory;
    }//end __construct()

    /**
     * {@inheritdoc}
     */
    public function install(ModuleDataSetupInterface $setup, ModuleContextInterface $context)
    {
        $this->state->emulateAreaCode(
            \Magento\Framework\App\Area::AREA_CRONTAB,
            [$this, 'reindex']
        );
    }//end install()

    /**
     * Add patch out so every deploy doesn't do a full reindex
     */
    public function reindex()
    {
        return; // TODO: Do we required to reindex only product related data?
    }//end reindex()
}