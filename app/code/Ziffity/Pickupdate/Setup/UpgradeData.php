<?php

namespace Ziffity\Pickupdate\Setup;

use Magento\Framework\Setup\UpgradeDataInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\ModuleDataSetupInterface;

/**
 * Upgrade Data script
 */
class UpgradeData implements UpgradeDataInterface
{
    /**
     * @var \Magento\Framework\App\Config\ScopeConfigInterface
     */
    private $scopeConfig;

    /**
     * @var \Magento\Framework\App\Config\Storage\WriterInterface
     */
    private $configWriter;

    public function __construct(
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig,
        \Magento\Framework\App\Config\Storage\WriterInterface $configWriter
    ) {
        $this->scopeConfig = $scopeConfig;
        $this->configWriter = $configWriter;
    }

    public function upgrade(ModuleDataSetupInterface $setup, ModuleContextInterface $context)
    {
        $setup->startSetup();
        if ($context->getVersion() && version_compare($context->getVersion(), '1.0.0', '<')) {
            $oldQuota = $this->scopeConfig->getValue('pickupdate/general/shipping_quota');
            if ($oldQuota) {
                $this->configWriter->delete('pickupdate/general/shipping_quota');
                $this->configWriter->save('pickupdate/quota/per_day', $oldQuota);
            }
            $oldQuota = $this->scopeConfig->getValue('pickupdate/general/tinterval_quota');
            if ($oldQuota) {
                $this->configWriter->delete('pickupdate/general/tinterval_quota');
                $this->configWriter->save('pickupdate/quota/tinterval_quota', $oldQuota);
            }
        }

        $setup->endSetup();
    }
}
