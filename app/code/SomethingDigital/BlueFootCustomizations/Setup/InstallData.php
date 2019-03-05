<?php

namespace SomethingDigital\BlueFootCustomizations\Setup;

use Magento\Framework\Setup\InstallDataInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\ModuleDataSetupInterface;
use SomethingDigital\Bluefoot\Helper\Installer as Bluefoot;

class InstallData implements InstallDataInterface
{
    protected $bluefoot;
    
    /**
     * Init
     *
     * @param \SomethingDigital\Bluefoot\Helper\Installer $bluefoot
     */
    public function __construct(BlueFoot $bluefoot)
    {
        $this->bluefoot = $bluefoot;
    }
    
    public function install(ModuleDataSetupInterface $setup, ModuleContextInterface $context)
    {
        $this->bluefoot->installBlueFootBlocks([
            'homepage_categories'
        ], 'SomethingDigital_BlueFootCustomizations');
    }
}
