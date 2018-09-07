<?php
/**
 * Mageplaza
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Mageplaza.com license that is
 * available through the world-wide-web at this URL:
 * https://mageplaza.com/LICENSE.txt
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade this extension to newer
 * version in the future.
 *
 * @category    Mageplaza
 * @package     Mageplaza_Seo
 * @copyright   Copyright (c) Mageplaza (https://www.mageplaza.com/)
 * @license     http://mageplaza.com/LICENSE.txt
 */

namespace Mageplaza\Seo\Plugin;

use Magento\Catalog\Model\ProductFactory;
use Magento\CatalogInventory\Api\StockRegistryInterface;
use Magento\CatalogInventory\Model\Stock\StockItemRepository;
use Magento\Framework\App\Request\Http;
use Magento\Framework\Event\Manager;
use Magento\Framework\Message\ManagerInterface;
use Magento\Framework\Pricing\Helper\Data as PriceHelper;
use Magento\Framework\Registry;
use Magento\Framework\UrlInterface;
use Magento\Framework\View\Page\Config as PageConfig;
use Magento\Framework\View\Page\Config\Renderer;
use Magento\Review\Model\ReviewFactory;
use Magento\Search\Helper\Data as SearchHelper;
use Magento\Store\Model\StoreManagerInterface;
use Mageplaza\Seo\Helper\Data as HelperData;

/**
 * Class SeoBeforeRender
 * @package Mageplaza\Seo\Plugin
 */
class SeoRender
{
    const GOOLE_SITE_VERIFICATION = 'google-site-verification';
    const MSVALIDATE_01           = 'msvalidate.01';
    const P_DOMAIN_VERIFY         = 'p:domain_verify';
    const YANDEX_VERIFICATION     = 'yandex-verification';

    /**
     * @var \Magento\Framework\View\Page\Config
     */
    protected $pageConfig;

    /**
     * @var \Magento\Framework\App\Request\Http
     */
    protected $request;

    /**
     * @var \Mageplaza\Seo\Helper\Data
     */
    protected $helperData;

    /**
     * @var \Magento\CatalogInventory\Model\Stock\StockItemRepository
     */
    protected $stockItemRepository;

    /**
     * @var \Magento\Framework\Registry
     */
    protected $registry;

    /**
     * @var \Magento\Store\Model\StoreManagerInterface
     */
    protected $_storeManager;

    /**
     * @var \Magento\Review\Model\ReviewFactory
     */
    protected $reviewFactory;

    /**
     * @var \Magento\Framework\UrlInterface
     */
    protected $_urlBuilder;

    /**
     * @var ProductFactory
     */
    protected $productFactory;

    /**
     * @var \Magento\Framework\Message\ManagerInterface
     */
    protected $messageManager;

    /**
     * @var \Magento\CatalogInventory\Api\StockRegistryInterface
     */
    protected $stockState;

    /**
     * @var \Magento\Search\Helper\Data
     */
    protected $_searchHelper;

    /**
     * @var \Magento\Framework\Pricing\Helper\Data
     */
    protected $_priceHelper;

    /**
     * @var \Magento\Framework\Event\Manager
     */
    protected $_eventManager;

    /**
     * SeoRender constructor.
     * @param \Magento\Framework\View\Page\Config $pageConfig
     * @param \Magento\Framework\App\Request\Http $request
     * @param \Mageplaza\Seo\Helper\Data $helpData
     * @param \Magento\CatalogInventory\Model\Stock\StockItemRepository $stockItemRepository
     * @param \Magento\Framework\Registry $registry
     * @param \Magento\Review\Model\ReviewFactory $reviewFactory
     * @param \Magento\Store\Model\StoreManagerInterface $storeManager
     * @param \Magento\Framework\UrlInterface $urlBuilder
     * @param \Magento\Catalog\Model\ProductFactory $productFactory
     * @param \Magento\Framework\Message\ManagerInterface $messageManager
     * @param \Magento\CatalogInventory\Api\StockRegistryInterface $stockState
     * @param \Magento\Search\Helper\Data $searchHelper
     * @param \Magento\Framework\Pricing\Helper\Data $priceHelper
     * @param \Magento\Framework\Event\Manager $eventManager
     */
    function __construct(
        PageConfig $pageConfig,
        Http $request,
        HelperData $helpData,
        StockItemRepository $stockItemRepository,
        Registry $registry,
        ReviewFactory $reviewFactory,
        StoreManagerInterface $storeManager,
        UrlInterface $urlBuilder,
        ProductFactory $productFactory,
        ManagerInterface $messageManager,
        StockRegistryInterface $stockState,
        SearchHelper $searchHelper,
        PriceHelper $priceHelper,
        Manager $eventManager
    )
    {
        $this->pageConfig          = $pageConfig;
        $this->request             = $request;
        $this->helperData          = $helpData;
        $this->stockItemRepository = $stockItemRepository;
        $this->registry            = $registry;
        $this->_storeManager       = $storeManager;
        $this->reviewFactory       = $reviewFactory;
        $this->_urlBuilder         = $urlBuilder;
        $this->productFactory      = $productFactory;
        $this->messageManager      = $messageManager;
        $this->stockState          = $stockState;
        $this->_searchHelper       = $searchHelper;
        $this->_priceHelper        = $priceHelper;
        $this->_eventManager       = $eventManager;
    }

    /**
     * @param Renderer $subject
     */
    public function beforeRenderMetadata(Renderer $subject)
    {
        if ($this->helperData->isEnabled()) {
            $this->showVerifications();

            $pages = [
                'catalogsearch_result_index',
                'cms_noroute_index',
                'catalogsearch_advanced_result'
            ];
            if (in_array($this->getFullActionName(), $pages)) {
                $this->pageConfig->setMetadata('robots', 'NOINDEX,NOFOLLOW');
            }
        }
    }

    /**
     * @param Renderer $subject
     * @param $result
     * @return string
     */
    public function afterRenderHeadContent(Renderer $subject, $result)
    {
        if ($this->helperData->isEnabled()) {
            $fullActionname = $this->getFullActionName();
            switch ($fullActionname) {
                case 'catalog_product_view':
                    if ($this->helperData->getRichsnippetsConfig('enable_product')) {
                        $productStructuredData = $this->showProductStructuredData();
                        $result                = $result . $productStructuredData;
                    }
                    break;
                case 'cms_index_index':
                    if ($this->helperData->getInfoConfig('enable')) {
                        $result = $result . $this->showBusinessStructuredData();
                    }
                    if ($this->helperData->getRichsnippetsConfig('enable_site_link')) {
                        $result = $result . $this->showSiteLinksStructuredData();
                    }
                    break;
            }
        }

        return $result;
    }

    /**
     *  Show verifications from config
     */
    public function showVerifications()
    {
        $this->pageConfig->setMetadata(self::GOOLE_SITE_VERIFICATION, $this->helperData->getVerficationConfig('google'));
        $this->pageConfig->setMetadata(self::MSVALIDATE_01, $this->helperData->getVerficationConfig('bing'));
        $this->pageConfig->setMetadata(self::P_DOMAIN_VERIFY, $this->helperData->getVerficationConfig('pinterest'));
        $this->pageConfig->setMetadata(self::YANDEX_VERIFICATION, $this->helperData->getVerficationConfig('yandex'));
    }

    /**
     * Get full action name
     * @return string
     */
    public function getFullActionName()
    {
        return $this->request->getFullActionName();
    }

    /**
     * Get current product
     * @return mixed
     */
    public function getProduct()
    {
        return $this->registry->registry('current_product');
    }

    /**
     * Get Url
     * @param string $route
     * @param array $params
     * @return string
     */
    public function getUrl($route = '', $params = [])
    {
        return $this->_urlBuilder->getUrl($route, $params);
    }

    /**
     * @param $productId
     * @return \Magento\CatalogInventory\Api\Data\StockItemInterface
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function getProductStock($productId)
    {
        return $this->stockItemRepository->get($productId);
    }

    /**
     * Get review count
     * @return mixed
     */
    public function getReviewCount()
    {
        if (!$this->getProduct()->getRatingSummary()) {
            $this->getEntitySummary($this->getProduct());
        }

        return $this->getProduct()->getRatingSummary()->getReviewsCount();
    }

    /**
     * Get rating summary
     * @return mixed
     */
    public function getRatingSummary()
    {
        if (!$this->getProduct()->getRatingSummary()) {
            $this->getEntitySummary($this->getProduct());
        }

        return $this->getProduct()->getRatingSummary()->getRatingSummary();
    }

    /**
     * @param $product
     * @return mixed
     */
    public function getEntitySummary($product)
    {
        $this->reviewFactory->create()->getEntitySummary($product, $this->_storeManager->getStore()->getId());
    }

    /**
     * Show product structured data
     * @return string
     *
     * Learn more: https://developers.google.com/structured-data/rich-snippets/products#single_product_page
     */
    public function showProductStructuredData()
    {
        if ($currentProduct = $this->getProduct()) {
            try {
                $productId = $currentProduct->getId() ? $currentProduct->getId() : $this->request->getParam('id');

                $product         = $this->productFactory->create()->load($productId);
                $availability    = $product->isAvailable() ? 'InStock' : 'OutOfStock';
                $stockItem       = $this->stockState->getStockItem(
                    $product->getId(),
                    $product->getStore()->getWebsiteId()
                );
                $priceValidUntil = $currentProduct->getSpecialToDate();

                $productStructuredData = [
                    '@context'    => 'http://schema.org/',
                    '@type'       => 'Product',
                    'name'        => $currentProduct->getName(),
                    'description' => trim(strip_tags($currentProduct->getDescription())),
                    'sku'         => $currentProduct->getSku(),
                    'url'         => $currentProduct->getProductUrl(),
                    'image'       => $this->getUrl('pub/media/catalog') . 'product' . $currentProduct->getImage(),
                    'offers'      => [
                        '@type'         => 'Offer',
                        'priceCurrency' => $this->_storeManager->getStore()->getCurrentCurrencyCode(),
                        'price'         => $currentProduct->getPriceInfo()->getPrice('final_price')->getValue(),
                        'itemOffered'   => $stockItem->getQty(),
                        'availability'  => 'http://schema.org/' . $availability
                    ]
                ];
                $productStructuredData = $this->addProductStructuredDataByType($currentProduct->getTypeId(), $currentProduct, $productStructuredData);

                if (!empty($priceValidUntil)) {
                    $productStructuredData['offers']['priceValidUntil'] = $priceValidUntil;
                }

                if ($this->getReviewCount()) {
                    $productStructuredData['aggregateRating']['@type']       = 'AggregateRating';
                    $productStructuredData['aggregateRating']['bestRating']  = 100;
                    $productStructuredData['aggregateRating']['worstRating'] = 0;
                    $productStructuredData['aggregateRating']['ratingValue'] = $this->getRatingSummary();
                    $productStructuredData['aggregateRating']['reviewCount'] = $this->getReviewCount();
                }

                $objectStructuredData = new \Magento\Framework\DataObject(['mpdata' => $productStructuredData]);
                $this->_eventManager->dispatch('mp_seo_product_structured_data', ['structured_data' => $objectStructuredData]);
                $productStructuredData = $objectStructuredData->getMpdata();

                return $this->helperData->createStructuredData($productStructuredData, '<!-- Product Structured Data by Mageplaza SEO-->');
            } catch (\Exception $e) {
                $this->messageManager->addError(__('Can not add structured data'));
            }
        }
    }

    /**
     * get Business Structured Data
     *
     * @return string
     */
    public function showBusinessStructuredData()
    {
        $businessStructuredData = [
            '@context' => 'http://schema.org/',
            '@type'    => 'Organization',
            'url'      => $this->getUrl(),
            'logo'     => $this->helperData->getLogo(),
            'name'     => $this->helperData->getInfoConfig('business_name')

        ];
        if (!empty($this->getSocialProfiles())) {
            $businessStructuredData['sameAs'] = $this->getSocialProfiles();
        }
        $businessStructuredData['contactPoint'] = [];

        // get customer service info
        if ($this->helperData->getInfoConfig('customer_service_phone')
            || $this->helperData->getInfoConfig('customer_service_contact_option')
            || $this->helperData->getInfoConfig('customer_service_area_serve')
        ) {
            $customerService = [
                '@type'         => 'ContactPoint',
                'telephone'     => $this->helperData->getInfoConfig('customer_service_phone'),
                'contactType'   => 'customer service',
                'contactOption' => $this->helperData->getInfoConfig('customer_service_contact_option'),
                'areaServed'    => $this->helperData->getInfoConfig('customer_service_area_serve')
            ];
            array_push($businessStructuredData['contactPoint'], $customerService);
        }
        // get technical support info
        if ($this->helperData->getInfoConfig('technical_support_phone')
            || $this->helperData->getInfoConfig('technical_support_contact_option')
            || $this->helperData->getInfoConfig('technical_support_area_serve')
        ) {
            $technicalSupport = [
                '@type'         => 'ContactPoint',
                'telephone'     => $this->helperData->getInfoConfig('technical_support_phone'),
                'contactType'   => 'technical support',
                'contactOption' => $this->helperData->getInfoConfig('technical_support_contact_option'),
                'areaServed'    => $this->helperData->getInfoConfig('technical_support_area_serve')
            ];
            array_push($businessStructuredData['contactPoint'], $technicalSupport);
        }
        // get sales info
        if ($this->helperData->getInfoConfig('sales_phone')
            || $this->helperData->getInfoConfig('sales_contact_option')
            || $this->helperData->getInfoConfig('sales_area_serve')
        ) {
            $sales = [
                '@type'         => 'ContactPoint',
                'telephone'     => $this->helperData->getInfoConfig('sales_phone'),
                'contactType'   => 'sales',
                'contactOption' => $this->helperData->getInfoConfig('sales_contact_option'),
                'areaServed'    => $this->helperData->getInfoConfig('sales_area_serve')
            ];
            array_push($businessStructuredData['contactPoint'], $sales);
        }

        return $this->helperData->createStructuredData($businessStructuredData, '<!-- Business Structured Data by Mageplaza SEO-->');
    }

    /**
     * get Social Profiles config
     *
     * @return array|string
     */

    public function getSocialProfiles()
    {
        $lines    = [];
        if ($profiles = $this->helperData->getSocialProfiles()) {
            foreach ($profiles as $_profile) {
                if ($_profile) {
                    $lines[] = $_profile;
                }
            }
        }

        return $lines;
    }

    /**
     * get Sitelinks Searchbox Structured Data
     *
     * @return string
     */
    public function showSiteLinksStructuredData()
    {
        $siteLinksStructureData = [
            '@context'        => 'http://schema.org',
            '@type'           => 'WebSite',
            'url'             => $this->_urlBuilder->getBaseUrl(),
            'potentialAction' => [
                '@type'       => 'SearchAction',
                'target'      => $this->_searchHelper->getResultUrl() . '?q={searchbox_target}',
                'query-input' => 'required name=searchbox_target'
            ]
        ];

        return $this->helperData->createStructuredData($siteLinksStructureData, '<!-- Sitelinks Searchbox Structured Data by Mageplaza SEO-->');
    }

    /**
     * add Grouped Product Structured Data
     *
     * @param $currentProduct
     * @param $productStructuredData
     * @return mixed
     */
    public function getGroupedProductStructuredData($currentProduct, $productStructuredData)
    {
        $productStructuredData['offers']['@type'] = 'AggregateOffer';
        $childrenPrice                            = [];
        $offerData                                = [];
        $typeInstance                             = $currentProduct->getTypeInstance();
        $childProductCollection                   = $typeInstance->getAssociatedProducts($currentProduct);
        foreach ($childProductCollection as $child) {
            $imageUrl = $this->_storeManager->getStore()->getBaseUrl(UrlInterface::URL_TYPE_MEDIA)
                . 'catalog/product' . $child->getImage();

            $offerData[]     = [
                '@type' => "Offer",
                'name'  => $child->getName(),
                'price' => $this->_priceHelper->currency($child->getPrice(), false),
                'sku'   => $child->getSku(),
                'image' => $imageUrl
            ];
            $childrenPrice[] = $this->_priceHelper->currency($child->getPrice(), false);
        }

        $productStructuredData['offers']['highPrice'] = array_sum($childrenPrice);
        $productStructuredData['offers']['lowPrice']  = min($childrenPrice);
        unset($productStructuredData['offers']['price']);

        if (!empty($offerData)) {
            $productStructuredData['offers']['offers'] = $offerData;
        }

        return $productStructuredData;
    }

    /**
     * add Downloadable Product Structured Data
     *
     * @param $currentProduct
     * @param $productStructuredData
     * @return mixed
     */
    public function getDownloadableProductStructuredData($currentProduct, $productStructuredData)
    {
        $productStructuredData['offers']['@type'] = 'AggregateOffer';

        $typeInstance           = $currentProduct->getTypeInstance();
        $childProductCollection = $typeInstance->getLinks($currentProduct);
        $childrenPrice          = [];
        foreach ($childProductCollection as $child) {
            $offerData[]     = [
                '@type' => "Offer",
                'name'  => $child->getTitle(),
                'price' => $this->_priceHelper->currency($child->getPrice(), false)
            ];
            $childrenPrice[] = $this->_priceHelper->currency($child->getPrice(), false);
        }
        $productStructuredData['offers']['highPrice'] = array_sum($childrenPrice);
        $productStructuredData['offers']['lowPrice']  = min($childrenPrice);

        if (!empty($offerData)) {
            $productStructuredData['offers']['offers'] = $offerData;
        }

        return $productStructuredData;
    }

    /**
     * add Configurable Product Structured Data
     *
     * @param $currentProduct
     * @param $productStructuredData
     * @return mixed
     */
    public function getConfigurableProductStructuredData($currentProduct, $productStructuredData)
    {
        $productStructuredData['offers']['@type']     = 'AggregateOffer';
        $productStructuredData['offers']['highPrice'] = $currentProduct->getPriceInfo()->getPrice('regular_price')->getMaxRegularAmount()->getValue();
        $productStructuredData['offers']['lowPrice']  = $currentProduct->getPriceInfo()->getPrice('regular_price')->getMinRegularAmount()->getValue();
        $offerData                                    = [];
        $typeInstance                                 = $currentProduct->getTypeInstance();
        $childProductCollection                       = $typeInstance->getUsedProductCollection($currentProduct)->addAttributeToSelect('*');
        foreach ($childProductCollection as $child) {
            $imageUrl = $this->_storeManager->getStore()->getBaseUrl(UrlInterface::URL_TYPE_MEDIA)
                . 'catalog/product' . $child->getImage();

            $offerData[] = [
                '@type' => "Offer",
                'name'  => $child->getName(),
                'price' => $this->_priceHelper->currency($child->getPrice(), false),
                'sku'   => $child->getSku(),
                'image' => $imageUrl
            ];
        }
        if (!empty($offerData)) {
            $productStructuredData['offers']['offers'] = $offerData;
        }

        return $productStructuredData;
    }

    /**
     * add Bundle Product Structured Data
     *
     * @param $currentProduct
     * @param $productStructuredData
     * @return mixed
     */
    public function getBundleProductStructuredData($currentProduct, $productStructuredData)
    {
        $productStructuredData['offers']['@type']     = 'AggregateOffer';
        $productStructuredData['offers']['highPrice'] = $currentProduct->getPriceInfo()->getPrice('regular_price')->getMaximalPrice()->getValue();
        $productStructuredData['offers']['lowPrice']  = $currentProduct->getPriceInfo()->getPrice('regular_price')->getMinimalPrice()->getValue();
        unset($productStructuredData['offers']['price']);
        $offerData              = [];
        $typeInstance           = $currentProduct->getTypeInstance();
        $childProductCollection = $typeInstance->getSelectionsCollection($typeInstance->getOptionsIds($currentProduct), $currentProduct);
        foreach ($childProductCollection as $child) {
            $imageUrl = $this->_storeManager->getStore()->getBaseUrl(UrlInterface::URL_TYPE_MEDIA)
                . 'catalog/product' . $child->getImage();

            $offerData[] = [
                '@type' => "Offer",
                'name'  => $child->getName(),
                'price' => $this->_priceHelper->currency($child->getPrice(), false),
                'sku'   => $child->getSku(),
                'image' => $imageUrl
            ];
        }
        if (!empty($offerData)) {
            $productStructuredData['offers']['offers'] = $offerData;
        }

        return $productStructuredData;
    }

    /**
     * add Product Structured Data By Type
     *
     * @param $productType
     * @param $currentProduct
     * @param $productStructuredData
     * @return mixed
     */
    public function addProductStructuredDataByType($productType, $currentProduct, $productStructuredData)
    {
        switch ($productType) {
            case 'grouped':
                $productStructuredData = $this->getGroupedProductStructuredData($currentProduct, $productStructuredData);
                break;
            case 'bundle':
                $productStructuredData = $this->getBundleProductStructuredData($currentProduct, $productStructuredData);
                break;
            case 'downloadable':
                $productStructuredData = $this->getDownloadableProductStructuredData($currentProduct, $productStructuredData);
                break;
            case 'configurable':
                $productStructuredData = $this->getConfigurableProductStructuredData($currentProduct, $productStructuredData);
                break;
        }

        return $productStructuredData;
    }
}
