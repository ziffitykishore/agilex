<?php

namespace SomethingDigital\Sx\Setup;

use Magento\Eav\Setup\EavSetup;
use Magento\Eav\Setup\EavSetupFactory;
use Magento\Framework\Setup\InstallDataInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\ModuleDataSetupInterface;
use Magento\Customer\Setup\CustomerSetupFactory;
use Magento\Eav\Model\Entity\Attribute\SetFactory as AttributeSetFactory;

class InstallData implements InstallDataInterface
{
    private $eavSetupFactory;

    /**
     * Init
     *
     * @param EavSetupFactory $eavSetupFactory
     */
    public function __construct(
        EavSetupFactory $eavSetupFactory, 
        CustomerSetupFactory $customerSetupFactory, 
        AttributeSetFactory $attributeSetFactory
    ) {
        $this->eavSetupFactory = $eavSetupFactory;
        $this->customerSetupFactory = $customerSetupFactory;
        $this->attributeSetFactory = $attributeSetFactory;
    }

    public function install(ModuleDataSetupInterface $setup, ModuleContextInterface $context)
    {
        $eavSetup = $this->eavSetupFactory->create(['setup' => $setup]);
        $eavSetup->addAttribute(
            \Magento\Customer\Model\Customer::ENTITY,
            'travers_account_id',
            [
                'type'         => 'int',
                'label'        => 'Travers Account Id',
                'input'        => 'text',
                'required'     => false,
                'visible'      => true,
                'user_defined' => false,
                'position'     => 998,
                'system'       => 0,
            ]
        );
        $eavSetup->addAttribute(
            \Magento\Customer\Model\Customer::ENTITY,
            'travers_contact_id',
            [
                'type'         => 'int',
                'label'        => 'Travers Contact Id',
                'input'        => 'text',
                'required'     => false,
                'visible'      => true,
                'user_defined' => false,
                'position'     => 999,
                'system'       => 0,
            ]
        );

        $customerSetup = $this->customerSetupFactory->create(['setup' => $setup]);
        $customerEntity = $customerSetup->getEavConfig()->getEntityType('customer_address');
        $attributeSetId = $customerEntity->getDefaultAttributeSetId();

        $attributeSet = $this->attributeSetFactory->create();
        $attributeGroupId = $attributeSet->getDefaultGroupId($attributeSetId);
         
        $customerSetup->addAttribute('customer_address', 'sx_address_id', [
            'type'          => 'int',
            'label'         => 'SX Address ID',
            'input'         => 'text',
            'required'      =>  false,
            'visible'       =>  true,
            'user_defined'  =>  false,
            'sort_order'    =>  13,
            'position'      =>  200,
            'system'        =>  0,
        ]);

        $attribute = $customerSetup->getEavConfig()->getAttribute('customer_address', 'sx_address_id')
            ->addData([
                'attribute_set_id' => $attributeSetId,
                'attribute_group_id' => $attributeGroupId,
                'used_in_forms' => [
                  'adminhtml_checkout',
                  'adminhtml_customer',
                  'adminhtml_customer_address',
                  'customer_address_edit'
                ],
            ]);

        $attribute->save();
    }
}