<?php

namespace SomethingDigital\AlgoliaSearch\Observer;

use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;
use Magento\Framework\Pricing\PriceCurrencyInterface;
use Magento\CatalogInventory\Api\StockRegistryInterface;
use Magento\Catalog\Api\TierPriceStorageInterface;
use Magento\Customer\Api\GroupRepositoryInterface;
use Magento\Framework\Api\SearchCriteriaBuilder;

class AddProductData implements ObserverInterface
{
    private $priceCurrency;
    private $stockRegistry;
    private $tierPriceStorage;
    private $customerGroupRepository;
    private $searchCriteriaBuilder;

    public function __construct(
        PriceCurrencyInterface $priceCurrency,
        StockRegistryInterface $stockRegistry,
        TierPriceStorageInterface $tierPriceStorage,
        GroupRepositoryInterface $customerGroupRepository,
        SearchCriteriaBuilder $searchCriteriaBuilder
    ) {
        $this->priceCurrency = $priceCurrency;
        $this->stockRegistry = $stockRegistry;
        $this->tierPriceStorage = $tierPriceStorage;
        $this->customerGroupRepository = $customerGroupRepository;
        $this->searchCriteriaBuilder = $searchCriteriaBuilder;
    }

    /**
     * @param Observer $observer
     * @return void
     */
    public function execute(Observer $observer)
    {
        $product = $observer->getEvent()->getData('productObject');
        $transport = $observer->getEvent()->getData('custom_data');
        $this->addManufacturerPrice($product, $transport);
        $this->addStockData($product, $transport);
        $this->addTierPrices($product, $transport);
    }

    /**
     * Add manufacturer_price attribute to algolia data
     *
     * @param \Magento\Catalog\Model\Product $product
     * @param \Magento\Framework\DataObject $transport
     */
    private function addManufacturerPrice($product, $transport)
    {
        $store = $product->getStore();
        $algoliaProductData = $transport->getData();
        $currentCurrencyCode = $store->getCurrentCurrencyCode();
        $price = $product->getData('manufacturer_price');
        $priceConverted = $this->priceCurrency->convert($price, $store, $currentCurrencyCode); 

        $algoliaProductData['manufacturer_price'] = [
            'price' => $this->priceCurrency->round($priceConverted),
            'price_formated' => $this->formatPrice($priceConverted, $store, $currentCurrencyCode)
        ];
        $transport->setData($algoliaProductData);
    }

    /**
     * Add min_sale_qty and qty_increment attributes to algolia data
     *
     * @param \Magento\Catalog\Model\Product $product
     * @param \Magento\Framework\DataObject $transport
     */
    private function addStockData($product, $transport)
    {
        $stockItem = $this->stockRegistry->getStockItem($product->getId());
        if ($stockItem) {
            $algoliaProductData = $transport->getData();
            $algoliaProductData['min_sale_qty'] = $stockItem->getMinSaleQty();
            $algoliaProductData['qty_increment'] = $stockItem->getQtyIncrements();
            $transport->setData($algoliaProductData);
        }
    }

    /**
     * Add tier prices to algolia data
     *
     * @param \Magento\Catalog\Model\Product $product
     * @param \Magento\Framework\DataObject $transport
     */
    private function addTierPrices($product, $transport)
    {
        $tiers = [];
        $store = $product->getStore();
        $algoliaProductData = $transport->getData();
        $currentCurrencyCode = $store->getCurrentCurrencyCode();
        $minSaleQty = 1;
        $stockItem = $this->stockRegistry->getStockItem($product->getId());
        if ($stockItem) {
            $minSaleQty = round($stockItem->getMinSaleQty(), 4);
        }

        $regularPrice = $product->getPrice();
        $regularPriceConverted = $this->priceCurrency->convert($regularPrice, $store, $currentCurrencyCode); 

        $customerGroups = $this->customerGroupRepository->getList($this->searchCriteriaBuilder->create())->getItems();
        foreach ($customerGroups as $customerGroup) {
            $tiers[$customerGroup->getId()] = [
                $minSaleQty => [
                    'qty' => $minSaleQty,
                    'price' => $regularPriceConverted,
                    'price_formatted' => $this->formatPrice($regularPriceConverted, $store, $currentCurrencyCode)
                ]
            ];
        }

        if ($product->getData('tier_price')) {
            foreach ($product->getData('tier_price') as $tier) {
                if (!isset($tiers[$tier['cust_group']])) {
                    continue; // unwanted record for non-exiting customer group
                }
                $tierPrice = $this->priceCurrency->convert($tier['price'], $store, $currentCurrencyCode);

                $tiers[$tier['cust_group']][round($tier['price_qty'], 4)] = [
                    'qty' => floatval($tier['price_qty']),
                    'price' => number_format($tierPrice, 2),
                    'price_formatted' => $this->formatPrice($tierPrice, $store, $currentCurrencyCode)
                ];
            }
        }

        foreach ($tiers as $groupId => $formattedTier) {
            if (count($formattedTier) === 1) {
                continue; // this is just customer group group pricing or default price for min_sale_qty
            }
            ksort($formattedTier, SORT_NUMERIC);
            $fieldName = 'group_' . $groupId . '_tiers';
            $algoliaProductData[$fieldName] = json_encode(array_values($formattedTier));
        }
        $transport->setData($algoliaProductData);
    }

    /**
     * Format price for displaying
     *
     * @param string $amount
     * @param \Magento\Store\Model\Store $store
     * @param string $currencyCode
     * @return string
     */
    private function formatPrice($amount, $store, $currencyCode)
    {
        return $this->priceCurrency->format(
            $amount,
            false,
            PriceCurrencyInterface::DEFAULT_PRECISION,
            $store,
            $currencyCode
        );
    }
}
