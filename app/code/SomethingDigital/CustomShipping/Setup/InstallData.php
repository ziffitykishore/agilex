<?php
namespace SomethingDigital\CustomShipping\Setup;

use Magento\Eav\Setup\EavSetup;
use Magento\Eav\Setup\EavSetupFactory;
use Magento\Framework\Setup\InstallDataInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\ModuleDataSetupInterface;
use Magento\Eav\Model\Config;
use Magento\Customer\Model\Customer;
use Magento\Eav\Model\Entity\Attribute\SetFactory as AttributeSetFactory;

class InstallData implements InstallDataInterface
{
    private $eavSetupFactory;
    private $eavConfig;

    public function __construct(EavSetupFactory $eavSetupFactory, Config $eavConfig, AttributeSetFactory $attributeSetFactory)
    {
        $this->eavSetupFactory = $eavSetupFactory;
        $this->eavConfig       = $eavConfig;
        $this->attributeSetFactory = $attributeSetFactory;
    }

    public function install(ModuleDataSetupInterface $setup, ModuleContextInterface $context)
    {
        $eavSetup = $this->eavSetupFactory->create(['setup' => $setup]);

        $customerEntity = $this->eavConfig->getEntityType('customer');
        $attributeSetId = $customerEntity->getDefaultAttributeSetId();

        /** @var $attributeSet AttributeSet */
        $attributeSet = $this->attributeSetFactory->create();
        $attributeGroupId = $attributeSet->getDefaultGroupId($attributeSetId);

        $eavSetup->addAttribute(\Magento\Customer\Model\Customer::ENTITY, 'customer_freight_account', [
            'user_defined' => true,
            'position' => 999,
            'type' => 'varchar',
            'label' => 'Customer Freight Account',
            'input' => 'text',
            'sort_order' => 9999,
            'global' => 1,
            'visible' => true,
            'required' => false,
            'system' => false,
            'group' => 'Account Information',
        ]);

        $attribute = $this->eavConfig->getAttribute(\Magento\Customer\Model\Customer::ENTITY, 'customer_freight_account')
            ->addData([
                'attribute_set_id' => $attributeSetId,
                'attribute_group_id' => $attributeGroupId,
                'used_in_forms' => ['adminhtml_checkout','adminhtml_customer','customer_account_edit', 'customer_account_create'],
            ]);

        $attribute->save();
    }
}