<?php

namespace SomethingDigital\ExtendedMiniCart\Plugin\Checkout\CustomerData;

use Magento\Quote\Model\Quote\ItemFactory;
use Magento\Quote\Model\ResourceModel\Quote\Item;
use Magento\Catalog\Api\ProductRepositoryInterface;
use Magento\Quote\Api\Data\CartItemInterface;
use Magento\Catalog\Api\Data\ProductInterface;
use Magento\Framework\Exception\NoSuchEntityException;
 
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

    /**
     * @var ProductRepositoryInterface
     */
    private $productRepo;

    public function __construct( 
        ItemFactory $quoteItemFactory,
        Item $itemResourceModel,
        ProductRepositoryInterface $productRepo
    ) {
        $this->quoteItemFactory = $quoteItemFactory;
        $this->itemResourceModel = $itemResourceModel;
        $this->productRepo = $productRepo;
    }

    public function afterGetSectionData(\Magento\Checkout\CustomerData\Cart $subject, array $result)
    {
        /** @var string[][] $items */
        $items = $result['items'];
        foreach ($items as &$item) {
            try {
                /** @var CartItemInterface */
                $cartItem = $this->loadCartItem($item['item_id']);
                /** @var ProductInterface */
                $product = $this->productRepo->get($cartItem->getSku());
            } catch (NoSuchEntityException $e) {
                continue;
                // @TODO
            }
            if ($cartItem->getPrice() !== $product->getPrice()) {
                $item['savings'] = $this->getSavings($cartItem, $product);
                $item['base_price'] = $product->getPrice();
                $item['special_price'] = $product->getSpecialPrice() ?? false;
                $item['manufacturer_price'] = $product->getManufacturerPrice() ?? false;
            }
        }
        $result['items'] = $items;
        return $result;
    }

    private function getSavings(CartItemInterface $item, ProductInterface $product)
    {
        /** @var float $basePrice */
        $basePrice = $product->getPrice();
        $msrpPrice = $product->getManufacturerPrice();
        if ($basePrice > $msrpPrice) {
            $basePrice = $msrpPrice;
        }
        /** @var float $finalPrice */
        $finalPrice = $item->getPrice(); 
        return floor((($basePrice - $finalPrice) / $basePrice) * 100);
    }

    private function loadCartItem($itemId)
    {
        /** @var \Magento\Quote\Api\Data\CartItemInterface */
        $quoteItem = $this->quoteItemFactory->create();
        $this->itemResourceModel->load($quoteItem, $itemId);
        return $quoteItem;
    }
}
