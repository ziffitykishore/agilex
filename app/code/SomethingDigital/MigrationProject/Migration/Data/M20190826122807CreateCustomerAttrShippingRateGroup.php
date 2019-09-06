<?php

namespace SomethingDigital\MigrationProject\Migration\Data;

use Magento\Framework\Setup\SetupInterface;
use SomethingDigital\Migration\Api\MigrationInterface;
use SomethingDigital\Migration\Helper\Cms\Page as PageHelper;
use SomethingDigital\Migration\Helper\Cms\Block as BlockHelper;
use SomethingDigital\Migration\Helper\Email\Template as EmailHelper;
use Magento\Config\Model\ResourceModel\Config as ResourceConfig;
use Magento\Eav\Setup\EavSetupFactory;
use Magento\Eav\Model\Config;
use Magento\Customer\Model\Customer;
use Magento\Eav\Model\Entity\Attribute\SetFactory as AttributeSetFactory;

class M20190826122807CreateCustomerAttrShippingRateGroup implements MigrationInterface
{
    private $eavSetupFactory;
    private $eavConfig;

    public function __construct(
        EavSetupFactory $eavSetupFactory, 
        Config $eavConfig, 
        AttributeSetFactory $attributeSetFactory
    ) {
        $this->eavSetupFactory = $eavSetupFactory;
        $this->eavConfig       = $eavConfig;
        $this->attributeSetFactory = $attributeSetFactory;
    }

    public function execute(SetupInterface $setup)
    {
        $eavSetup = $this->eavSetupFactory->create(['setup' => $setup]);
        $customerEntity = $this->eavConfig->getEntityType('customer');
        $attributeSetId = $customerEntity->getDefaultAttributeSetId();
        /** @var $attributeSet AttributeSet */
        $attributeSet = $this->attributeSetFactory->create();
        $attributeGroupId = $attributeSet->getDefaultGroupId($attributeSetId);
        $eavSetup->addAttribute(\Magento\Customer\Model\Customer::ENTITY, 'shipping_rate_group', [
            'user_defined' => true,
            'position' => 999,
            'type' => 'varchar',
            'label' => 'Shipping Rate Group',
            'input' => 'text',
            'sort_order' => 9999,
            'global' => 1,
            'visible' => true,
            'required' => false,
            'system' => false,
            'group' => 'Account Information',
        ]);
        $attribute = $this->eavConfig->getAttribute(\Magento\Customer\Model\Customer::ENTITY, 'shipping_rate_group')
            ->addData([
                'attribute_set_id' => $attributeSetId,
                'attribute_group_id' => $attributeGroupId,
                'used_in_forms' => ['adminhtml_checkout','adminhtml_customer','customer_account_edit', 'customer_account_create'],
            ]);
        $attribute->save();
    }
}
