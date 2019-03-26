<?php

namespace SomethingDigital\MigrationProject\Migration\Data;

use Magento\Framework\Setup\SetupInterface;
use SomethingDigital\Migration\Api\MigrationInterface;
use SomethingDigital\Migration\Helper\Cms\Page as PageHelper;
use SomethingDigital\Migration\Helper\Cms\Block as BlockHelper;
use SomethingDigital\Migration\Helper\Email\Template as EmailHelper;
use Magento\Config\Model\ResourceModel\Config as ResourceConfig;
use Magento\Customer\Setup\CustomerSetupFactory;
use Magento\Eav\Model\Entity\Attribute\SetFactory as AttributeSetFactory;

class M20190326144511AddAddressAttributes implements MigrationInterface
{
    protected $page;
    protected $block;
    protected $email;
    protected $resourceConfig;
    protected $customerSetupFactory;
    private $attributeSetFactory;

    public function __construct(PageHelper $page, BlockHelper $block, EmailHelper $email, ResourceConfig $resourceConfig, CustomerSetupFactory $customerSetupFactory, AttributeSetFactory $attributeSetFactory)
    {
        $this->page = $page;
        $this->block = $block;
        $this->email = $email;
        $this->resourceConfig = $resourceConfig;
        $this->customerSetupFactory = $customerSetupFactory;
        $this->attributeSetFactory = $attributeSetFactory;
    }

    public function execute(SetupInterface $setup)
    {
        $customerSetup = $this->customerSetupFactory->create(['setup' => $setup]);
        $customerEntity = $customerSetup->getEavConfig()->getEntityType('customer_address');
        $attributeSetId = $customerEntity->getDefaultAttributeSetId();

        $attributeSet = $this->attributeSetFactory->create();
        $attributeGroupId = $attributeSet->getDefaultGroupId($attributeSetId);
         
        $customerSetup->addAttribute('customer_address', 'is_read_only', [
            'type'          => 'int',
            'label'         => 'Is read only',
            'input'         => 'boolean',
            'source'        => \Magento\Eav\Model\Entity\Attribute\Source\Boolean::class,
            'required'      =>  false,
            'visible'       =>  true,
            'user_defined'  =>  true,
            'sort_order'    =>  13,
            'position'      =>  150,
            'system'        =>  0,
        ]);

        $attribute = $customerSetup->getEavConfig()->getAttribute('customer_address', 'is_read_only')
            ->addData([
                'attribute_set_id' => $attributeSetId,
                'attribute_group_id' => $attributeGroupId,
                'used_in_forms' => [
                  'adminhtml_checkout',
                  'adminhtml_customer',
                  'adminhtml_customer_address'
                ],
            ]);

        $attribute->save();

        $customerSetup->addAttribute('customer_address', 'is_billing', [
            'type'          => 'int',
            'label'         => 'Is billing',
            'input'         => 'boolean',
            'source'        => \Magento\Eav\Model\Entity\Attribute\Source\Boolean::class,
            'required'      =>  false,
            'visible'       =>  true,
            'user_defined'  =>  true,
            'sort_order'    =>  13,
            'position'      =>  160,
            'system'        =>  0,
        ]);

        $attribute = $customerSetup->getEavConfig()->getAttribute('customer_address', 'is_billing')
            ->addData([
                'attribute_set_id' => $attributeSetId,
                'attribute_group_id' => $attributeGroupId,
                'used_in_forms' => [
                  'adminhtml_checkout',
                  'adminhtml_customer',
                  'adminhtml_customer_address'
                ],
            ]);

        $attribute->save();
    }
}
