<?php
/**
 * Build option list for Out of Stock module for Configuration
 * 
 */
namespace Ziffity\StockStatus\Model\Source;

class PageTypes implements \Magento\Framework\Option\ArrayInterface
{
    /**
     * List of pages require to configure Out of Stock status
     * 
     * @return array
     */
    public function toOptionArray()
    {
        return [
            ['value' => 'home_page', 'label' => __('Home Page')],
            ['value' => 'product_listing', 'label' => __('Product Listing')],
            ['value' => 'configure_detail_page', 'label' => __('Configure Product Option')],
        ];
    }
}
