<?php
namespace SomethingDigital\ProductCompareDisabler\Plugin;
class CompareDisabler extends \Magento\Catalog\Helper\Product\Compare
{
    /**
     * Retrieve url for adding product to compare list
     *
     * @return string
     */
    public function afterGetAddUrl($subject, $result)
    {
        //disable compare links all over site
        return false;
    }
}
