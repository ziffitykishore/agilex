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
            /** @var \Magento\Quote\Api\Data\CartItemInterface */
            $cartItem = $this->loadCartItem($item['item_id'])
            $item['savings'] = $this->getSavings($cartItem);
            $item['base_price'] = $this->getBasePrice($cartItem);
        }
        $result['items'] = $items;
        return $result;
    }

    private function getSavings(\Magento\Quote\Api\Data\CartItemInterface $item)
    {

    }

    private function getBasePrice(\Magento\Quote\Api\Data\CartItemInterface $item)
    {

    }

    private function loadCartItem($itemId)
    {
        /** @var \Magento\Quote\Api\Data\CartItemInterface */
        $quoteItem = $this->quoteItemFactory->create();
        $this->itemResourceModel->load($quoteItem, $itemId);
        return $quoteItem;
    }
}
