<?php

namespace PartySupplies\Catalog\ViewModel;

use Magento\Framework\View\Element\Block\ArgumentInterface;
use Magento\CatalogInventory\Api\StockRegistryInterface;
use Magento\Framework\Pricing\Helper\Data;
use Amasty\Groupcat\Model\CustomerIdHolder;
use Magento\GroupedProduct\Model\Product\Type\Grouped;
use Magento\Catalog\Model\Product as ProductModel;
use Magento\Catalog\Model\ProductRepository;
use Magento\Msrp\Pricing\MsrpPriceCalculatorInterface;

class Product implements ArgumentInterface
{
    /**
     * @var StockRegistryInterface
     */
    protected $stockRegistry;

    /**
     * @var Data
     */
    protected $priceHelper;

    /**
     * @var CustomerIdHolder
     */
    protected $customerSession;

    /**
     * @var ProductModel
     */
    protected $productModel;

    /**
     * @var ProductRepository
     */
    protected $productRepository;

    /**
     * @var MsrpPriceCalculatorInterface
     */
    protected $msrpPriceCalculator;

    /**
     *
     * @param StockRegistryInterface       $stockRegistry
     * @param Data                         $priceHelper
     * @param CustomerIdHolder             $customer
     * @param ProductModel                 $product
     * @param ProductRepository            $productRepository
     * @param MsrpPriceCalculatorInterface $msrpPriceCalculator
     */
    public function __construct(
        StockRegistryInterface $stockRegistry,
        Data $priceHelper,
        CustomerIdHolder $customer,
        ProductModel $product,
        ProductRepository $productRepository,
        MsrpPriceCalculatorInterface $msrpPriceCalculator
    ) {
        $this->stockRegistry = $stockRegistry;
        $this->priceHelper = $priceHelper;
        $this->customerSession = $customer;
        $this->productModel = $product;
        $this->productRepository = $productRepository;
        $this->msrpPriceCalculator = $msrpPriceCalculator;
    }

    /**
     *
     * @param \Magento\Catalog\Model\Product $product
     * @return float
     */
    public function getMinQty($product)
    {
        if ($product->getTypeId() === Grouped::TYPE_CODE) {
            return $this->getChildProductData($product, 'minQty');
        }
        $stockItem = $this->stockRegistry->getStockItem($product->getId(), $product->getStore()->getWebsiteId());
        $minSaleQty = $stockItem->getMinSaleQty();

        return $minSaleQty > 0 ? $minSaleQty : null;
    }

    /**
     *
     * @param \Magento\Catalog\Model\Product $product
     * @param string $type
     * @return mixed
     */
    public function getChildProductData($product, $type)
    {
        $childrenData = [];
        $children = $product->getTypeInstance()->getChildrenIds($product->getId());
        $children = array_shift($children);
        foreach ($children as $child) {
            $product = $this->productModel->load($child);
            array_push(
                $childrenData,
                [
                    'id' => $this->productModel->load($child)->getId(),
                    'price' =>(float)$product->getPriceInfo()->getPrice('final_price')->getValue(),
                    'minQty' => (int)$this->stockRegistry->getStockItem(
                        $product->getId(),
                        $product->getStore()->getWebsiteId()
                    )->getMinSaleQty(),
                    'msrp' => (float)$this->msrpPriceCalculator->getMsrpPriceValue($product)
                ]
            );
        }
        if ($type == 'minPrice') {
            return $this->getMinPriceOfChildren($childrenData);
        } elseif ($type == 'minQty') {
            return $this->getMinQtyOfChildren($childrenData);
        } elseif ($type == 'msrp') {
            return $this->getMsrpOfChildren($childrenData);
        }
    }
    /**
     *
     * @param \Magento\Catalog\Model\Product $product
     * @return float
     */
    public function getCasePrice($product)
    {
        return $this->priceHelper->currency(
            $product->getPriceInfo()->getPrice('final_price')->getValue() * $this->getMinQty($product),
            true,
            false
        );
    }

    /**
     *
     * @param \Magento\Catalog\Model\Product $product
     * @return string
     */
    public function getEachPrice($product)
    {
        if ($product->getTypeId() === Grouped::TYPE_CODE) {
             $price = $this->getChildProductData($product, 'minPrice');
        }

        return $this->priceHelper->currency($price, true, false);
    }

    /**
     * @param string $productSku
     * @param string $attribute
     * @return string
     */
    public function getCustomAttribute($productSku, $attribute)
    {
        $value = $this->productRepository->get($productSku)->getAttributeText($attribute);
        return ($value || $value === "0") ? $value : null;
    }

    /**
     *
     * @param \Magento\Catalog\Model\Product $product
     * @return string
     */
    public function getMsrpPrice($product)
    {
        if ($product->getTypeId() === Grouped::TYPE_CODE) {
            $price = $this->getChildProductData($product, 'msrp');
        }

        return $this->priceHelper->currency($price, true, false);
    }

    /**
     *
     * @return int|Null
     */
    public function getCustomerId()
    {
        return $this->customerSession->getCustomerId();
    }

    /**
     *
     * @param array $children
     * @return float
     */
    public function getMinPriceOfChildren(array $children)
    {
        return min(array_column($children, 'price'));
    }

    /**
     *
     * @param array $children
     * @return int
     */
    public function getMinQtyOfChildren(array $children)
    {
        $minPrice = min(array_column($children, 'price'));
        $minQtyIndex = array_search($minPrice, array_column($children, 'price'), true);

        return $children[$minQtyIndex]['minQty'];
    }

    /**
     *
     * @param array $children
     * @return float
     */
    public function getMsrpOfChildren(array $children)
    {
        $minPrice = min(array_column($children, 'price'));
        $minQtyIndex = array_search($minPrice, array_column($children, 'price'), true);

        return $children[$minQtyIndex]['msrp'];
    }
}
