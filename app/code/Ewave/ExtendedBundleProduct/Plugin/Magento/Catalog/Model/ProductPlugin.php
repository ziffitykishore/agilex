<?php
namespace Ewave\ExtendedBundleProduct\Plugin\Magento\Catalog\Model;

use Magento\Catalog\Model\Product as Subject;

/**
 * Class ProductPlugin
 */
class ProductPlugin
{
    const BUNDLE_IDENTITY_OPTION = 'bundle_identity';

    /**
     * @param Subject $subject
     * @param string $code
     * @param mixed $value
     * @param null $product
     * @return array
     */
    public function beforeAddCustomOption(
        Subject $subject,
        $code,
        $value,
        $product = null
    ) {
        if ($code == self::BUNDLE_IDENTITY_OPTION) {
            $value .= '_' . time();
        }
        return [$code, $value, $product];
    }
}
