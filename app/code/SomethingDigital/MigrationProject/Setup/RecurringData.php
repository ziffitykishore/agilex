<?php

namespace SomethingDigital\MigrationProject\Setup;

use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\ModuleDataSetupInterface;
use Magento\Framework\Setup\InstallDataInterface;
use SomethingDigital\Migration\Api\MigratorInterface;

class RecurringData implements InstallDataInterface
{
    protected $migrator;

    public function __construct(MigratorInterface $migrator)
    {
        $this->migrator = $migrator;
    }

    public function install(ModuleDataSetupInterface $setup, ModuleContextInterface $context)
    {
        $this->migrator->execute($setup, 'SomethingDigital_MigrationProject', 'data');
    }
}
