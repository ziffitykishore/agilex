<?php

namespace PartySupplies\Customer\Setup;

use Magento\Customer\Model\Customer;
use Magento\Customer\Setup\CustomerSetupFactory;
use Magento\Eav\Model\Entity\Attribute\SetFactory as AttributeSetFactory;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\ModuleDataSetupInterface;
use Magento\Framework\Setup\UpgradeDataInterface;

class UpgradeData implements UpgradeDataInterface
{

    /**
     * @var CustomerSetupFactory
     */
    protected $customerSetupFactory;

    /**
     * @var AttributeSetFactory
     */
    protected $attributeSetFactory;
    
    /**
     *
     * @param CustomerSetupFactory $customerSetupFactory
     * @param AttributeSetFactory  $attributeSetFactory
     */
    public function __construct(
        CustomerSetupFactory $customerSetupFactory,
        AttributeSetFactory $attributeSetFactory
    ) {
        $this->customerSetupFactory = $customerSetupFactory;
        $this->attributeSetFactory = $attributeSetFactory;
    }

    /**
     *
     * @param ModuleDataSetupInterface $setup
     * @param ModuleContextInterface   $context
     */
    public function upgrade(ModuleDataSetupInterface $setup, ModuleContextInterface $context)
    {
        if (version_compare($context->getVersion(), '1.0.1', '<')) {
            
            $customerSetup = $this->customerSetupFactory->create(['setup' => $setup]);
            $customerEntity = $customerSetup
                ->getEavConfig()
                ->getEntityType(Customer::ENTITY);
            $attributeSetId = $customerEntity->getDefaultAttributeSetId();

            $attributeSet = $this->attributeSetFactory->create();
            $attributeGroupId = $attributeSet->getDefaultGroupId($attributeSetId);
            
            $customerSetup->updateAttribute(Customer::ENTITY, 'account_type', [
                'is_used_in_grid' => 1,
                'is_visible_in_grid' => 1,
                'is_filterable_in_grid' => 1,
                'is_searchable_in_grid' => 1
            ]);
            
            $customerSetup->updateAttribute(Customer::ENTITY, 'nav_customer_id', [
                'is_used_in_grid' => 1,
                'is_visible_in_grid' => 1,
                'is_filterable_in_grid' => 1,
                'is_searchable_in_grid' => 1
            ]);
            
            $accountType = $customerSetup->getEavConfig()->getAttribute(
                Customer::ENTITY,
                'account_type'
            )->addData([
                'attribute_set_id' => $attributeSetId,
                'attribute_group_id' => $attributeGroupId,
                'used_in_forms' => ['adminhtml_customer', 'customer_account_create', 'customer_account_edit'],
            ]);

            $accountType->save();

            $navCustomerId = $customerSetup->getEavConfig()->getAttribute(
                Customer::ENTITY,
                'nav_customer_id'
            )->addData([
                'attribute_set_id' => $attributeSetId,
                'attribute_group_id' => $attributeGroupId,
                'used_in_forms' => ['adminhtml_customer', 'customer_account_create', 'customer_account_edit'],
            ]);

            $navCustomerId->save();
        }
    }
}
