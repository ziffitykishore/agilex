<?php
namespace Ziffity\Promo\Plugin\SalesRule\Model;

use \Magento\SalesRule\Model\Rule\Metadata\ValueProvider as Source;

class MetadataValueProvider
{
    /**
     * Add the Gift action option to SalesRule
     *
     * @see \Magento\SalesRule\Model\Rule\Metadata\ValueProvider::getMetadataValues
     * @plugin after
     * @param Source $subject
     * @param array $resultMetadataValues
     * @return array
     */
    public function afterGetMetadataValues(Source $subject, $resultMetadataValues)
    {
        $resultMetadataValues['actions']['children']['simple_action']['arguments']['data']['config']['options'][] = [
            'value' => 'ampromo_items',
            'label' => __('Auto add promo items with products'),
        ];
        $resultMetadataValues['actions']['children']['simple_action']['arguments']['data']['config']['options'][] = [
            'value' => 'ampromo_cart',
            'label' => __('Auto add promo items for the whole cart'),
        ];
        return $resultMetadataValues;
    }
}
