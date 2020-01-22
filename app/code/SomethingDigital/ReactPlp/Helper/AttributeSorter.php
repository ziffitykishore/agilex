<?php

namespace SomethingDigital\ReactPlp\Helper;

class AttributeSorter
{
    public const ARRAYED_ATTRIBUTES = [
        'price' => ['id' => 'price', 'label' => 'Price'],
        'sku' => ['id' => 'sku', 'label' => 'SKU']
    ];

    public const FLAT_ATTRIBUTES = [
        'price' => 'price',
        'sku' => 'sku'
    ];

    /**
     * Add SKU attribute at front of array and
     * Price attribute at right-most-viewable location.
     *
     * right-most-viewable means either the 9th element
     * of the array, or if the array is smaller than 9,
     * the last element of the array.
     *
     * The assumption of a maximum of 9 to display
     * was not made here, that can be found in:
     * TraversTool-ReactPLP/src/Products/TableView/Table/Hits.js
     *
     * @param array $tableAttributes current attributes
     * @param AttributeSorter::DEFAULT_ATTRIBUTES|AttributeSorter::CUSTOM_ATTRIBUTES $type whether this is for the default attributes or the custom attributes.
     * @return array
     */
    public function sort($tableAttributes, $type)
    {
        $priceValue = $type['price'];
        $skuValue = $type['sku'];
        //Find position of SKU in the array
        $skuPosition = array_search(
            $skuValue,
            $tableAttributes
        );

        if ($skuPosition !== 0) { //either skuPosition is false (non-existent) or is greater than 0
            if ($skuPosition > 0) {
                //If SKU exists in the array and it's not in the first position we want to remove it.
                array_splice($tableAttributes, $skuPosition, 1);
            }

            //Add sku at beginning of array.
            array_unshift($tableAttributes, $skuValue);
        }
        //Find position of Price in the array
        $pricePosition = array_search(
            $priceValue,
            $tableAttributes
        );

        //Find position that Price must be in the array.
        $pricePushPos = min(8, sizeof($tableAttributes));

        if ($pricePosition !== $pricePushPos) {
            if ($pricePosition && $pricePosition >= 0) {
                //If Price is not in the position it must be, we remove the old position.
                array_splice($tableAttributes, $pricePosition, 1);
            }

            //Add Price to required array position.
            array_splice($tableAttributes, $pricePushPos, 0, [$priceValue]);
        }

        return $tableAttributes;
    }
}
