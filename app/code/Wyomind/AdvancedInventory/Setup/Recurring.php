<?php
/**
 * Copyright © 2019 Wyomind. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace Wyomind\AdvancedInventory\Setup;

class Recurring implements \Magento\Framework\Setup\InstallSchemaInterface
{
    private $_coreHelper = null;

    /**
     * Recurring constructor.
     * @param \Wyomind\Core\Helper\Data $coreHelper
     */
    public function __construct(
        \Wyomind\Core\Helper\Data $coreHelper
    )
    {
        $this->_coreHelper = $coreHelper;
    }

    /**
     * {@inheritdoc}
     */
    public function install(
        \Magento\Framework\Setup\SchemaSetupInterface $setup,
        \Magento\Framework\Setup\ModuleContextInterface $context
    )
    {
        $files = [
            "Observer/RefundOrderInventoryObserver.php"
        ];
        $this->_coreHelper->copyFilesByMagentoVersion(__FILE__, $files);
    }
}