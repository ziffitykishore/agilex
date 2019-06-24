<?php
namespace Ewave\ExtendedBundleProduct\Setup;

use Ewave\ExtendedBundleProduct\Helper\Data;
use Ewave\ExtendedBundleProduct\Model\Config\Source\Bundle\CountSeparatelyOption;
use Magento\Eav\Model\Entity\Attribute\ScopedAttributeInterface;
use Magento\Eav\Setup\EavSetup;
use Magento\Eav\Setup\EavSetupFactory;
use Magento\Framework\Setup\UpgradeDataInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\ModuleDataSetupInterface;

/**
 * Class UpgradeData
 * @package Ewave\ExtendedBundleProduct\Setup
 */
class UpgradeData implements UpgradeDataInterface
{
    /**
     * @var EavSetupFactory
     */
    private $eavSetupFactory;

    /**
     * @var EavSetup
     */
    private $eavSetup;

    /**
     * @param EavSetupFactory $eavSetupFactory
     */
    public function __construct(EavSetupFactory $eavSetupFactory)
    {
        $this->eavSetupFactory = $eavSetupFactory;
    }

    /**
     * @param ModuleDataSetupInterface $setup
     * @param ModuleContextInterface $context
     * @return void
     */
    public function upgrade(ModuleDataSetupInterface $setup, ModuleContextInterface $context)
    {
        $setup->startSetup();

        if ($this->isNeedUpgrade($context, '1.0.2')) {
            $eavSetup = $this->getEavSetup($setup);
            $this->addAttributeCountBundleItemsSeparately($eavSetup);
        }

        $setup->endSetup();
    }

    /**
     * @param EavSetup $eavSetup
     * @return $this
     */
    protected function addAttributeCountBundleItemsSeparately(EavSetup $eavSetup)
    {
        $eavSetup->addAttribute(
            \Magento\Catalog\Model\Product::ENTITY,
            Data::CODE_ATTRIBUTE_BUNDLE_IS_COUNT_ITEMS_SEPARATE,
            [
                'group' => 'Bundle Items',
                'type' => 'int',
                'backend' => '',
                'frontend' => '',
                'label' => 'Count Bundle Items Separately',
                'input' => 'select',
                'class' => '',
                'source' => CountSeparatelyOption::class,
                'global' => ScopedAttributeInterface::SCOPE_WEBSITE,
                'visible' => true,
                'required' => true,
                'user_defined' => true,
                'default' => CountSeparatelyOption::VALUE_USE_CONFIG,
                'searchable' => false,
                'filterable' => false,
                'comparable' => false,
                'visible_on_front' => false,
                'used_in_product_listing' => true,
                'unique' => false,
                'apply_to' => 'bundle'
            ]
        );
        return $this;
    }

    /**
     * @param ModuleDataSetupInterface $setup
     * @return EavSetup
     */
    protected function getEavSetup(ModuleDataSetupInterface $setup)
    {
        if (!$this->eavSetup) {
            $this->eavSetup = $this->eavSetupFactory->create(['setup' => $setup]);
        }
        return $this->eavSetup;
    }

    /**
     * @param ModuleContextInterface $context
     * @param string $version
     * @return bool
     */
    protected function isNeedUpgrade(ModuleContextInterface $context, $version)
    {
        return $context->getVersion() && (version_compare($context->getVersion(), $version) < 0);
    }
}
