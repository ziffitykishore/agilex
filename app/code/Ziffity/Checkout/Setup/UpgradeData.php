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

        protected $region;

	public function __construct(
            EavSetupFactory $eavSetup,
            \Magento\Directory\Model\Region $region
	) {
            $this->eavSetupFactory = $eavSetup;
            $this->region = $region;
	}

	public function upgrade(
		ModuleDataSetupInterface $setup,
		ModuleContextInterface $context
	) {
                $regionData = $this->region->loadByCode('TX','US');

		$setup->startSetup();

		if(version_compare($context->getVersion(), '1.0.1', '<')){
			$eavSetup = $this->eavSetupFactory->create(['setup' => $setup]);
			$eavSetup->updateAttribute('customer_address', 'postcode', 'frontend_input', 'select');
			$eavSetup->updateAttribute('customer_address', 'postcode', 'source_model', 'Ziffity\Checkout\Model\ResourceModel\Address\Attribute\Source\Postcode');
                        $eavSetup->updateAttribute('customer_address', 'region_id', 'default_value', $regionData->getData('region_id'));
		}

		$setup->endSetup();
	}
}