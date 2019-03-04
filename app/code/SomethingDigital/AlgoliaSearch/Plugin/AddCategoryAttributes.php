<?php

namespace SomethingDigital\AlgoliaSearch\Plugin;

class AddCategoryAttributes
{
    /**
     * Add configuration for additional category attributes to Algolia index
     *
     * @param \Algolia\AlgoliaSearch\Helper\ConfigHelper $subject
     * @param type $result
     * @param type $storeId
     */
    public function afterGetCategoryAdditionalAttributes(\Algolia\AlgoliaSearch\Helper\ConfigHelper $subject, $result, $storeId = null)
    {
        $result[] = [
            'attribute' => 'image',
            'searchable' => 2,
            'order' => 'unordered',
            'retrievable' => 1
        ];
        
        return $result;
    }
}
