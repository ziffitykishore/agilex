<?php

namespace PartySupplies\Customer\Setup;

use Magento\Customer\Model\Customer;
use Magento\Customer\Setup\CustomerSetup;
use Magento\Customer\Setup\CustomerSetupFactory;
use Magento\Eav\Model\Entity\Attribute\SetFactory as AttributeSetFactory;
use Magento\Framework\Setup\InstallDataInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\ModuleDataSetupInterface;
use PartySupplies\Customer\Model\Source\AccountType;

class InstallData implements InstallDataInterface
{

    /**
     * @var CustomerSetupFactory
     */
    protected $customerSetupFactory;

    /**
     * @var AttributeSetFactory
     */
    protected $attributeSetFactory;

    public function __construct(
        CustomerSetupFactory $customerSetupFactory,
        AttributeSetFactory $attributeSetFactory
    ) {
        $this->customerSetupFactory = $customerSetupFactory;
        $this->attributeSetFactory = $attributeSetFactory;
    }

    /**
     * {@inheritdoc}
     */
    public function install(
        ModuleDataSetupInterface $setup,
        ModuleContextInterface $context
    ) {
        /** @var CustomerSetup $customerSetup */
        $customerSetup = $this->customerSetupFactory->create(['setup' => $setup]);

        $customerEntity = $customerSetup
            ->getEavConfig()
            ->getEntityType(Customer::ENTITY);
        $attributeSetId = $customerEntity->getDefaultAttributeSetId();

        /** @var $attributeSet AttributeSet */
        $attributeSet = $this->attributeSetFactory->create();
        $attributeGroupId = $attributeSet->getDefaultGroupId($attributeSetId);

        /**
         * Field keys
         */
        $type = 'type';
        $label = 'label';
        $input = 'input';
        $required = 'required';
        $visible = 'visible';
        $userDefined = 'user_defined';
        $sortOrder = 'sort_order';
        $position = 'position';
        $system = 'system';

        $customerSetup->addAttribute(Customer::ENTITY, 'account_type', [
            $type => 'varchar',$label => 'Account Type',$input => 'select',
            'source' => AccountType::class,$required => false,'default' => 'customer',
            $visible => true,$userDefined => true,$sortOrder => 1000,$position => 1000,$system => 0,
        ]);

        $customerSetup->addAttribute(Customer::ENTITY, 'reseller_certificate', [
            $type => 'varchar',$label => 'Reseller Certificate',$input => 'file',$required => false,
            $visible => true,$userDefined => true,$sortOrder => 1100,$position => 1100,$system => 0,
        ]);

        $customerSetup->addAttribute(Customer::ENTITY, 'is_certificate_approved', [
            $type => 'int',$label => 'Certificate Approved',$input => 'boolean',$required => false,
            'default' => '0',$visible => true,$userDefined => true,$sortOrder => 1101,$position => 1101,
            $system => 0,
        ]);

        $customerSetup->addAttribute(Customer::ENTITY, 'nav_customer_id', [
            $type => 'varchar',$label => 'NAV Customer-ID',$input => 'text',$required => false,'default' => null,
            $visible => true,$userDefined => true,$sortOrder => 81,$position => 81,$system => 0,
        ]);

        $data = [
            'attribute_set_id' => $attributeSetId,
            'attribute_group_id' => $attributeGroupId,
            'used_in_forms' => ['adminhtml_customer', 'customer_account_create', 'customer_account_edit'],
        ];
        $accountType = $customerSetup->getEavConfig()->getAttribute(
            Customer::ENTITY,
            'account_type'
        )->addData($data);

        $accountType->save();

        $resellerCertificate = $customerSetup->getEavConfig()->getAttribute(
            Customer::ENTITY,
            'reseller_certificate'
        )->addData($data);

        $resellerCertificate->save();

        $isCertificated = $customerSetup->getEavConfig()->getAttribute(
            Customer::ENTITY,
            'is_certificate_approved'
        )->addData($data);

        $isCertificated->save();

        $navCustomerId = $customerSetup->getEavConfig()->getAttribute(
            Customer::ENTITY,
            'nav_customer_id'
        )->addData($data);

        $navCustomerId->save();
    }
}
