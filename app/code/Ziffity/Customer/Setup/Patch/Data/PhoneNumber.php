<?php

namespace Ziffity\Customer\Setup\Patch\Data;

use Magento\Framework\Setup\Patch\DataPatchInterface;
use Magento\Customer\Model\Customer;

class PhoneNumber implements DataPatchInterface
{
	protected $moduleDataSetup;

	protected $customerSetupFactory;

	protected $attributeSetFactory;

	public function __construct(
		\Magento\Framework\Setup\ModuleDataSetupInterface $moduleDataSetup,
		\Magento\Customer\Setup\CustomerSetupFactory $customerSetupFactory,
		\Magento\Eav\Model\Entity\Attribute\SetFactory $setFactory
	) {
		$this->moduleDataSetup = $moduleDataSetup;
		$this->customerSetupFactory = $customerSetupFactory;
		$this->attributeSetFactory = $setFactory;
	}

	public function apply()
	{
		$customerSetup = $this->customerSetupFactory->create(['setup' => $this->moduleDataSetup]);
		$customerEntity = $customerSetup->getEavConfig()->getEntityType(Customer::ENTITY);
		$attributeSetId = $customerEntity->getDefaultAttributeSetId();

		$attributeSet = $this->attributeSetFactory->create();
		$attributeGroupId = $attributeSet->getDefaultGroupId($attributeSetId);

		$customerSetup->addAttribute(
			Customer::ENTITY,
			'phone_number',
			[
				'type' => 'varchar',
				'label' => 'Phone Number',
				'input' => 'text',
				'validate_rules' => '{"max_text_length" : 255, "min_text_length" : 1}',
				'required' => true,
				'sort_order' => 120,
				'position' => 120,
				'visible' => true,
				'user_defined' => true,
				'unique' => false,
				'system' => false
			]
		);

		$attribute = $customerSetup->getEavConfig()->getAttribute(
			Customer::ENTITY,
			'phone_number'
		);

		$attribute->addData(
			[
				'attribute_set_id' => $attributeSetId,
				'attribute_group_id' => $attributeGroupId,
				'used_in_forms' => [
					'adminhtml_customer',
					'customer_account_create',
					'customer_account_edit'
				]
			]
		);

		$attribute->save();


	}

	public static function getDependencies()
	{
		return [];
	}

	public function getAliases()
	{
		return [];
	}

}