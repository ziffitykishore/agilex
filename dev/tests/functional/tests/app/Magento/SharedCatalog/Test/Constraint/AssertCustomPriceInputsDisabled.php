<?php
/**
 * Copyright © 2016 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magento\SharedCatalog\Test\Constraint;

use Magento\SharedCatalog\Test\Page\Adminhtml\SharedCatalogConfigure;
use Magento\Mtf\Constraint\AbstractConstraint;

/**
 * Assert that custom price select and custom price input are disabled.
 */
class AssertCustomPriceInputsDisabled extends AbstractConstraint
{
    /**
     * Assert that custom price select and custom price input are disabled.
     *
     * @param SharedCatalogConfigure $sharedCatalogConfigure
     * @param array $products
     * @param string $allWebsitesName
     * @return void
     */
    public function processAssert(
        SharedCatalogConfigure $sharedCatalogConfigure,
        array $products,
        $allWebsitesName
    ) {
        $sharedCatalogConfigure->getPricingGrid()->filterProductsByWebsite($allWebsitesName);
        foreach ($products as $product) {
            $sharedCatalogConfigure->getPricingGrid()->search(['sku' => $product->getSku()]);
            \PHPUnit_Framework_Assert::assertTrue(
                $sharedCatalogConfigure->getPricingGrid()->isCustomPriceTypeSelectDisabled(),
                'Custom price type select for product \'' . $product->getName() . '\'' . ' is enabled.'
            );
            \PHPUnit_Framework_Assert::assertTrue(
                $sharedCatalogConfigure->getPricingGrid()->isCustomPriceInputDisabled(),
                'Custom price input for product \'' . $product->getName() . '\'' . ' is enabled.'
            );
        }
    }

    /**
     * Returns a string representation of the object.
     *
     * @return string
     */
    public function toString()
    {
        return 'Custom price type select and custom price input are disabled.';
    }
}
