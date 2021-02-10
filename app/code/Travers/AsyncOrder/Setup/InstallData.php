<?php

namespace Travers\AsyncOrder\Setup;

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
        $states = [
            'Rejected' => [
                'label' => __('Rejected'),
                'statuses' => ['Rejected' => ['default' => '1']],
                'visible_on_front' => true,
            ],
        ];

        foreach ($states as $code => $info) {
            if (isset($info['statuses'])) {
                foreach ($info['statuses'] as $status => $statusInfo) {
                    $data[] = [
                        'status' => $status,
                        'state' => $code,
                        'is_default' => is_array($statusInfo) && isset($statusInfo['default']) ? 1 : 0,
                        'visible_on_front' => 1,
                    ];
                }
            }
        }

        $setup->getConnection()->insertArray(
            $setup->getTable('sales_order_status_state'),
            ['status', 'state', 'is_default', 'visible_on_front'],
            $data
        );
    }
}