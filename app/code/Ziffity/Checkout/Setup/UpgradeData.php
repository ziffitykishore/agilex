<?php

namespace Ziffity\Checkout\Setup;

use Magento\Framework\Setup\UpgradeDataInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\ModuleDataSetupInterface;
use Magento\Framework\DB\Ddl\Table;
use Magento\Customer\Setup\CustomerSetupFactory;
use Magento\Eav\Setup\EavSetup;
use Magento\Eav\Setup\EavSetupFactory;

class UpgradeData implements UpgradeDataInterface
{
	private $eavSetupFactory;

	public function __construct(
		EavSetupFactory $eavSetup
	) {
		$this->eavSetupFactory = $eavSetup;
	}

	public function upgrade(
		ModuleDataSetupInterface $setup,
		ModuleContextInterface $context
	) {
		$setup->startSetup();

		if(version_compare($context->getVersion(), '1.0.0', '<')){
			$eavSetup = $this->eavSetupFactory->create(['setup' => $setup]);
			$eavSetup->updateAttribute('customer_address', 'postcode', 'frontend_input', 'select');
			$eavSetup->updateAttribute('customer_address', 'postcode', 'source_model', 'Ziffity\Checkout\Model\ResourceModel\Address\Attribute\Source\Postcode');
		}

		$setup->endSetup();
	}
}