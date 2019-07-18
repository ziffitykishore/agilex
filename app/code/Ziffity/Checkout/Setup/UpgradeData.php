<?php

namespace Ziffity\Checkout\Setup;

use Magento\Framework\Setup\UpgradeDataInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\ModuleDataSetupInterface;
use Magento\Framework\DB\Ddl\Table;
use Magento\Customer\Setup\CustomerSetupFactory;
use Magento\Eav\Setup\EavSetup;
use Magento\Eav\Setup\EavSetupFactory;
use Magento\Sales\Setup\SalesSetupFactory;
use Magento\Quote\Setup\QuoteSetupFactory;

class UpgradeData implements UpgradeDataInterface
{
	private $eavSetupFactory;

	protected $region;
	
	
    protected $salesSetupFactory;
    
    protected $quoteSetupFactory;

	public function __construct(
        EavSetupFactory $eavSetup,
		\Magento\Directory\Model\Region $region,
		SalesSetupFactory $salesSetupFactory,
        QuoteSetupFactory $quoteSetupFactory
	) {
        $this->eavSetupFactory = $eavSetup;
		$this->region = $region;
		$this->salesSetupFactory = $salesSetupFactory;
        $this->quoteSetupFactory = $quoteSetupFactory;
	}

	public function upgrade(
		ModuleDataSetupInterface $setup,
		ModuleContextInterface $context
	) {
        $regionData = $this->region->loadByCode('TX','US');

		$setup->startSetup();

		if (version_compare($context->getVersion(), '1.0.1', '<')){
			$eavSetup = $this->eavSetupFactory->create(['setup' => $setup]);
			$eavSetup->updateAttribute('customer_address', 'postcode', 'frontend_input', 'select');
			$eavSetup->updateAttribute('customer_address', 'postcode', 'source_model', 'Ziffity\Checkout\Model\ResourceModel\Address\Attribute\Source\Postcode');
            $eavSetup->updateAttribute('customer_address', 'region_id', 'default_value', $regionData->getData('region_id'));
		}

		if (version_compare($context->getVersion(),'1.0.2','<')){

			/** @var \Magento\Quote\Setup\QuoteSetup $quoteInstaller */
			$quoteInstaller = $this->quoteSetupFactory->create(
				['resourceName' => 'quote_setup', 'setup' => $setup]
			);
	
			/** @var \Magento\Sales\Setup\SalesSetup $salesInstaller */
			$salesInstaller = $this->salesSetupFactory->create(
				['resourceName' => 'sales_setup', 'setup' => $setup]
			);
			
			$quoteInstaller->addAttribute(
				'quote',
				'store_location',
				[
				  'type' => Table::TYPE_TEXT
				]
			);

			$quoteInstaller->addAttribute(
				'quote',
				'store_address',
				[
				  'type' => Table::TYPE_TEXT,
				  'length' => '64k', 'nullable' => true
				]
			);
	
			$salesInstaller->addAttribute(
				'order',
				'store_location',
				[
				  'type' => Table::TYPE_TEXT,
				  'grid' => true
				]
			);

			$salesInstaller->addAttribute(
				'order',
				'store_address',
				[
				  'type' => Table::TYPE_TEXT,
				  'length' => '64k', 'nullable' => true,
				  'grid' => true
				]
			);
		}

		$setup->endSetup();
	}
}