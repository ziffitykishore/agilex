<?php
/**
 * Mirasvit
 *
 * This source file is subject to the Mirasvit Software License, which is available at https://mirasvit.com/license/.
 * Do not edit or add to this file if you wish to upgrade the to newer versions in the future.
 * If you wish to customize this module for your needs.
 * Please refer to http://www.magentocommerce.com for more information.
 *
 * @category  Mirasvit
 * @package   mirasvit/module-core
 * @version   1.2.68
 * @copyright Copyright (C) 2018 Mirasvit (https://mirasvit.com/)
 */


namespace Mirasvit\Core\Setup;

use Magento\Framework\Setup\UpgradeSchemaInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\SchemaSetupInterface;

class UpgradeSchema implements UpgradeSchemaInterface
{
    /**
     * {@inheritdoc}
     */
    public function upgrade(SchemaSetupInterface $setup, ModuleContextInterface $context)
    {
        $installer = $setup;

        if (version_compare($context->getVersion(), '1.0.1') < 0) {
            include_once 'Upgrade_1_0_1.php';

            Upgrade_1_0_1::upgrade($installer, $context);
        }

        if (version_compare($context->getVersion(), '1.0.2') < 0) {
            include_once 'Upgrade_1_0_2.php';

            Upgrade_1_0_2::upgrade($installer, $context);
        }

        if (version_compare($context->getVersion(), '1.0.3') < 0) {
            include_once 'Upgrade_1_0_3.php';

            Upgrade_1_0_3::upgrade($installer, $context);
        }
    }
}
