<?php

namespace PartySupplies\Catalog\ViewModel;

use Magento\Framework\View\Element\Block\ArgumentInterface;
use Magento\CatalogInventory\Api\StockRegistryInterface;
use Magento\Framework\Pricing\Helper\Data;
use Amasty\Groupcat\Model\CustomerIdHolder;
use Magento\GroupedProduct\Model\Product\Type\Grouped;
use Magento\Catalog\Model\ProductFactory;
use Magento\Catalog\Model\ProductRepository;
use Magento\Msrp\Pricing\MsrpPriceCalculatorInterface;
use Magento\Catalog\Helper\Image;

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
     * @var Image
     */
    protected $imageHelper;

    /**
     *
     * @param StockRegistryInterface       $stockRegistry
     * @param Data                         $priceHelper
     * @param CustomerIdHolder             $customer
     * @param ProductFactory               $product
     * @param ProductRepository            $productRepository
     * @param MsrpPriceCalculatorInterface $msrpPriceCalculator
     * @param Image                        $imageHelper
     */
    public function __construct(
        StockRegistryInterface $stockRegistry,
        Data $priceHelper,
        CustomerIdHolder $customer,
        ProductFactory $product,
        ProductRepository $productRepository,
        MsrpPriceCalculatorInterface $msrpPriceCalculator,
        Image $imageHelper
    ) {
        $this->stockRegistry = $stockRegistry;
        $this->priceHelper = $priceHelper;
        $this->customerSession = $customer;
        $this->productModel = $product;
        $this->productRepository = $productRepository;
        $this->msrpPriceCalculator = $msrpPriceCalculator;
        $this->imageHelper = $imageHelper;
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
            $product = $this->productModel->create()->load($child);
            array_push(
                $childrenData,
                [
                    'id' => $product->getId(),
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
        if ($product->getTypeId() === Grouped::TYPE_CODE) {
            return $this->priceHelper->currency(
                $this->getChildProductData($product, 'minPrice') * $this->getChildProductData($product, 'minQty'),
                true,
                false
            );
        }
        
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

    /**
     * @param type $productId
     * @param type $websiteId
     * @param array $validators
     * @return JSON
     */
    public function getQuantityValidators($productId, $websiteId, array $validators = [])
    {
        $stockItem = $this->stockRegistry->getStockItem($productId, $websiteId);

        $params = [];
        $params['minAllowed']  = (float)$stockItem->getMinSaleQty();
        if ($stockItem->getMaxSaleQty()) {
            $params['maxAllowed'] = (float)$stockItem->getMaxSaleQty();
        }
        if ($stockItem->getQtyIncrements() > 0) {
            $params['qtyIncrements'] = (float)$stockItem->getQtyIncrements();
        }
        $validators['validate-item-quantity'] = $params;

        return json_encode($validators);
    }

    /**
     * @param \Magento\Catalog\Model\Product $product
     * @return boolean
     */
    public function isCallForPrice($product)
    {
        switch ($product->getTypeId()) {
            case \Magento\GroupedProduct\Model\Product\Type\Grouped::TYPE_CODE:
            case \Magento\ConfigurableProduct\Model\Product\Type\Configurable::TYPE_CODE:
                return $this->getChildProductData($product, 'minPrice')>0;
            default:
                return $product->getPrice()>0;
        }
    }

    /**
     * To return product image url
     *
     * @param \Magento\Catalog\Model\Product $product
     * @return string
     */
    public function getImageUrl($product, $isMainImage = false)
    {
        $product = $this->productRepository->get($product->getSku());

        if($isMainImage) {
            return $this->imageHelper->init(
                $product,
                'product_page_main_image'
            )->setImageFile($product->getImage())->getUrl();
        }

        return $this->imageHelper->init(
            $product,
            'product_page_image_small'
        )->setImageFile($product->getSmallImage())->getUrl();
    }
}
