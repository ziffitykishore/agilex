<?php

namespace Travers\CustomerLinking\Setup;

use Magento\Eav\Setup\EavSetup;
use Magento\Eav\Setup\EavSetupFactory;
use Magento\Framework\Setup\InstallDataInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\ModuleDataSetupInterface;
use Magento\Eav\Model\Config;

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
        Config $eavConfig
    ) {
        $this->eavSetupFactory = $eavSetupFactory;
        $this->eavConfig = $eavConfig;
    }

    public function install(ModuleDataSetupInterface $setup, ModuleContextInterface $context)
    {
        $eavSetup = $this->eavSetupFactory->create(['setup' => $setup]);
        $eavSetup->addAttribute(
            \Magento\Customer\Model\Customer::ENTITY,
            'last_account_linking_date',
            [
                'type'         => 'text',
                'label'        => 'Last Account Linking Date',
                'input'        => 'text',
                'required'     => false,
                'visible'      => true,
                'user_defined' => true,
                'position'     => 1000,
                'system'       => false,
            ]
        );
        $eavSetup->addAttribute(
            \Magento\Customer\Model\Customer::ENTITY,
            'last_account_linking_message',
            [
                'type'         => 'text',
                'label'        => 'Last Account Linking Message',
                'input'        => 'text',
                'required'     => false,
                'visible'      => true,
                'user_defined' => true,
                'position'     => 1001,
                'system'       => false,
            ]
        );
        $customer = $this->eavConfig->getAttribute(\Magento\Customer\Model\Customer::ENTITY, 'last_account_linking_message');
        $customer->setData(
            'used_in_forms',
            [
                'adminhtml_customer',
                'customer_account_edit',
            ]
        );
        $customer->save();

        $customer = $this->eavConfig->getAttribute(\Magento\Customer\Model\Customer::ENTITY, 'last_account_linking_date');
        $customer->setData(
            'used_in_forms',
            [
                'adminhtml_customer',
                'customer_account_edit',
            ]
        );
        $customer->save();
    }
}