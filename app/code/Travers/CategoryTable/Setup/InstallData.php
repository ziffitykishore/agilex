<?php
namespace Travers\CategoryTable\Setup;

use Magento\Eav\Setup\EavSetup;
use Magento\Eav\Setup\EavSetupFactory;
use Magento\Framework\Setup\InstallDataInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\ModuleDataSetupInterface;

class InstallData implements InstallDataInterface
{
    private $eavSetupFactory;

    /**
     * Init
     *
     * @param EavSetupFactory $eavSetupFactory
     */
    public function __construct(EavSetupFactory $eavSetupFactory)
    {
        $this->eavSetupFactory = $eavSetupFactory;
    }

    public function install(ModuleDataSetupInterface $setup, ModuleContextInterface $context)
    {
        /** @var EavSetup $eavSetup */
        $eavSetup = $this->eavSetupFactory->create(['setup' => $setup]);

        /**
         * Add attributes to the eav/attribute
        **/

        $eavSetup->addAttribute(
			\Magento\Catalog\Model\Category::ENTITY,
			'enable_table_view',
			[
				'type' => 'varchar',
				'label' => 'Enable Table View',
                'input' => 'select',
                'source' => \Magento\Eav\Model\Entity\Attribute\Source\Boolean::class,
                'default' => '1',
				'sort_order' => 100,
				'global' => 1,
				'visible' => true,
				'required' => false,
				'user_defined' => false,
				'backend' => ''
			]
		);

    }
}