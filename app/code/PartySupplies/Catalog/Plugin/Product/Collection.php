<?php

namespace PartySupplies\Catalog\Plugin\Product;

class Collection
{
    /**
     * To sort products based on Case Price.
     *
     * @param \Magento\Catalog\Model\ResourceModel\Product\Collection $subject
     * @param Collection $result
     * @param string $attribute
     * @param string $dir
     * @return Collection $result
     * 
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function afterAddAttributeToSort(
        \Magento\Catalog\Model\ResourceModel\Product\Collection $subject,
        $result,
        $attribute,
        $dir
    ) {
        if ($attribute == 'price') {
            $result->getSelect()
                ->reset('order')
                ->join(
                    ['cataloginventory_stock_item'],
                    'e.entity_id = cataloginventory_stock_item.product_id',
                    [
                        'case_price' => '(cataloginventory_stock_item.qty_increments * min_price)'
                    ]
                )->order('case_price '. $dir);
        }
        return $result;
    }
}
