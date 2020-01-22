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
     * @param array $tableAttributes current attributes
     * @param AttributeSorter::DEFAULT_ATTRIBUTES|AttributeSorter::CUSTOM_ATTRIBUTES $type whether this is for the default attributes or the custom attributes.
     * @return array $newAttributes
     */
    public function sort(array $tableAttributes, $type)
    {
        $newAttributes = $tableAttributes;
        $priceObj = $type['price'];
        $skuObj = $type['sku'];
        //Find position of SKU in the array
        $skuPos = array_search(
            $skuObj,
            $newAttributes
        );

        if ($skuPos !== 0) { //either skuPos is false (non-existent) or is greater than 0
            if ($skuPos > 0) {
                //If SKU exists in the array and it's not in the first position we want to remove it.
                array_splice($newAttributes, $skuPos, 1);
            }

            //Add sku at beginning of array.
            array_unshift($newAttributes, $skuObj);
        }
        //Find position of Price in the array
        $pricePos = array_search(
            $priceObj,
            $newAttributes
        );

        //Find position that Price must be in the array.
        $pricePushPos = min(8, sizeof($newAttributes));

        if ($pricePos !== $pricePushPos) {
            if ($pricePos && $pricePos >= 0) {
                //If Price is not in the position it must be, we remove the old position.
                array_splice($newAttributes, $pricePos, 1);
            }

            //Add Price to required array position.
            array_splice($newAttributes, $pricePushPos, 0, [$priceObj]);
        }

        return $newAttributes;
    }
}
