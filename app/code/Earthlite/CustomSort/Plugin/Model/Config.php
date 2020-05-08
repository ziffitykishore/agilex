<?php
namespace Earthlite\CustomSort\Plugin\Model;

class Config
{

/**
* Adding custom options and changing labels
*
* @param \Magento\Catalog\Model\Config $catalogConfig
* @param [] $options
* @return []
*/
    public function afterGetAttributeUsedForSortByArray(\Magento\Catalog\Model\Config $catalogConfig, $options)
    {
        $newOption['position_asc'] = __('Position: Low to High');
        $newOption['position_desc'] = __('Position: High to Low');
        $newOption['name_asc'] = __('Product Name: A to Z');
        $newOption['name_desc'] = __('Product Name: Z to A');
        $newOption['price_asc'] = __('Price: Low to High');
        $newOption['price_desc'] = __('Price: High to Low');

        //Merge default sorting options with new options
        $options = array_merge($newOption, $options);

        return $options;
    }
}
