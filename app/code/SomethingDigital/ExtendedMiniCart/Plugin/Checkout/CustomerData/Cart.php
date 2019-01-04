<?php

namespace SomethingDigital\ExtendedMiniCart\Plugin\Checkout\CustomerData;

use Magento\Quote\Model\Quote\ItemFactory;
use Magento\Quote\Model\ResourceModel\Quote\Item;
 
class Cart 
{
    /**
     * @var ItemFactory
     */
    private $quoteItemFactory;

    /** 
     * @var Item
     */
    private $itemResourceModel;

    public function __construct( 
        ItemFactory $quoteItemFactory
        Item $itemResourceModel
    ) {
        $this->quoteItemFactory = $quoteItemFactory;
        $this->itemResourceModel = $itemResourceModel;
    }
    public function afterGetSectionData(\Magento\Checkout\CustomerData\Cart $subject, array $result)
    {
        /** @var string[][] $items */
        $items = $result['items'];
        foreach ($items as &$item) {
            $item['savings'] = $this->getSavings($item);
            $item['base_price'] = $this->getBasePrice($item);
        }
        $result['items'] = $items;
        return $result;
    }

    private function getSavings($item)
    {
    }

    private function getBasePrice($item)
    {
    }
}
