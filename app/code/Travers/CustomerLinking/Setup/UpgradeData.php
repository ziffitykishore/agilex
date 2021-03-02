<?php

namespace Travers\CustomerLinking\Setup;

use Magento\Framework\Setup\UpgradeDataInterface;
use Magento\Framework\Setup\ModuleDataSetupInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Eav\Setup\EavSetup; 
use Magento\Customer\Setup\CustomerSetupFactory;
use Magento\Eav\Model\Entity\Attribute\SetFactory as AttributeSetFactory;
use Magento\Framework\App\ResourceConnection;

class UpgradeData implements UpgradeDataInterface 
{
    public function __construct(
        EavSetup $eavSetupFactory,
        CustomerSetupFactory $customerSetupFactory, 
        AttributeSetFactory $attributeSetFactory,
        ResourceConnection $resourceConnection
        ) 
    { 
        $this->eavSetupFactory = $eavSetupFactory; 
        $this->customerSetupFactory = $customerSetupFactory;
        $this->attributeSetFactory = $attributeSetFactory;
        $this->resourceConnection = $resourceConnection;
    } 

    public function upgrade(ModuleDataSetupInterface $setup, ModuleContextInterface $context) {

        if ($context->getVersion()
            && version_compare($context->getVersion(), '0.0.2') >= 0
        ) {

            $connection= $this->resourceConnection->getConnection();
            $themeTable = $this->resourceConnection->getTableName('customer_eav_attribute');
            $sql = "UPDATE ".  $themeTable . " SET is_used_in_grid = 1,is_visible_in_grid = 1, is_filterable_in_grid = 1 WHERE attribute_id = 961";
            $connection->query($sql); 

        }
        $customerSetup = $this->customerSetupFactory->create(['setup' => $setup]);
        $customerEntity = $customerSetup->getEavConfig()->getEntityType('customer');
        $attributeSetId = $customerEntity->getDefaultAttributeSetId();

        $attributeSet = $this->attributeSetFactory->create();
        $attributeGroupId = $attributeSet->getDefaultGroupId($attributeSetId);
        $attribute = $customerSetup->getEavConfig()->getAttribute(\Magento\Customer\Model\Customer::ENTITY, 'last_account_linking_date')
        ->addData([
            'attribute_set_id' => $attributeSetId,
            'attribute_group_id' => $attributeGroupId
        ]);
        $attribute->save();
        $attribute = $customerSetup->getEavConfig()->getAttribute(\Magento\Customer\Model\Customer::ENTITY, 'last_account_linking_message')
        ->addData([
            'attribute_set_id' => $attributeSetId,
            'attribute_group_id' => $attributeGroupId
        ]);
        $attribute->save();
    } 

}