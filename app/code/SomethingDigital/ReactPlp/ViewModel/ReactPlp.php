<?php

namespace SomethingDigital\ReactPlp\ViewModel;

use Magento\Framework\Registry;
use Magento\Framework\Serialize\Serializer\Json as JsonEncoder;
use Magento\Customer\Model\Session;
use Magento\Store\Model\StoreManagerInterface;
use Magento\Framework\Data\Form\FormKey;
use Magento\Store\Model\ScopeInterface;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Catalog\Model\ResourceModel\Product\Attribute\CollectionFactory;
use Magento\Swatches\Model\Swatch;
use Magento\Swatches\Helper\Data;
use Magento\Swatches\Helper\Media;
use Magento\Framework\App\Request\Http;
use Magento\Framework\App\Http\Context as HttpContext;
use Magento\Framework\App\Filesystem\DirectoryList;
use Magento\Framework\App\CacheInterface;
use Magento\Framework\Serialize\SerializerInterface;
use SomethingDigital\ReactPlp\Helper\AttributeSorter;

class ReactPlp implements \Magento\Framework\View\Element\Block\ArgumentInterface
{
    const LIST_ATTRIBUTES_CACHE_ID = 'reactListAttributes';
    const TABLE_ATTRIBUTES_CACHE_ID = 'reactTableAttributes';
    const SEARCH_ATTRIBUTES_CACHE_ID = 'reactSearchAttributes';
    const FILTER_ATTRIBUTES_CACHE_ID = 'reactFilterAttributes';
    const FILTER_ATTRIBUTES_SEARCH_CACHE_ID = 'reactFilterAttributesSearch';

    private $storeManager;
    private $customerSession;
    private $formKey;
    private $scopeConfig;
    private $collectionFactory;
    private $coreRegistry;
    private $jsonEncoder;
    private $swatchHelper;
    private $swatchHelperMedia;
    private $request;
    private $httpContext;
    private $cache;
    private $serializer;
    /**
     * @var AttributeSorter
     */
    private $attributeSorter;

    public function __construct(
        Registry $registry,
        JsonEncoder $jsonEncoder,
        Session $customerSession,
        StoreManagerInterface $storeManager,
        FormKey $formKey,
        ScopeConfigInterface $scopeConfig,
        CollectionFactory $collectionFactory,
        Data $swatchHelper,
        Media $swatchHelperMedia,
        Http $request,
        HttpContext $httpContext,
        DirectoryList $directoryList,
        CacheInterface $cache,
        SerializerInterface $serializer,
        AttributeSorter $attributeSorter
    ) {
        $this->coreRegistry = $registry;
        $this->jsonEncoder = $jsonEncoder;
        $this->customerSession = $customerSession;
        $this->storeManager = $storeManager;
        $this->formKey = $formKey;
        $this->scopeConfig = $scopeConfig;
        $this->collectionFactory = $collectionFactory;
        $this->swatchHelper = $swatchHelper;
        $this->swatchHelperMedia = $swatchHelperMedia;
        $this->request = $request;
        $this->httpContext = $httpContext;
        $this->directoryList = $directoryList;
        $this->cache = $cache;
        $this->serializer = $serializer;
        $this->attributeSorter = $attributeSorter;
    }

    /**
     * @return integer
     */
    public function getCategoryId()
    {
        if ($this->request->getFullActionName() == 'catalog_category_view') {
            $category = $this->coreRegistry->registry('current_category');
            return $category->getId();
        } else {
            return null;
        }
    }

    /**
     * Returns a JSON object
     * 
     * @return string JSON
     */
    public function getJsProps()
    {
        // * @var \Magento\Catalog\Api\Data\CategoryInterface $category 

        if ($this->httpContext->getValue(\Magento\Customer\Model\Context::CONTEXT_AUTH)) {
            $customerGroupId = $this->httpContext->getValue(\Magento\Customer\Model\Context::CONTEXT_GROUP);
        } else {
            $customerGroupId = 0;
        }

        if ($this->scopeConfig->getValue("algoliasearch_credentials/credentials/read_index_prefix", ScopeInterface::SCOPE_STORE)) {
            $index_prefix = $this->scopeConfig->getValue("algoliasearch_credentials/credentials/read_index_prefix", ScopeInterface::SCOPE_STORE);
        } else {
            $index_prefix = $this->scopeConfig->getValue("algoliasearch_credentials/credentials/index_prefix", ScopeInterface::SCOPE_STORE);
        }
        $props = [
            'defaultCategoryId' => $this->getCategoryId(),
            'customerGroupId' => $customerGroupId,
            'apiUrls' => [
                'cms' => [
                    'url' => $this->storeManager->getStore()->getBaseUrl() . 'travers-catalog/cms/view/id',
                    'params' => []
                ],
                'grouping' => [
                    'url' => $this->storeManager->getStore()->getBaseUrl() . 'travers-catalog/grouping/view/id',
                    'params' => []
                ],
                'addToCart' => [
                    'url' => $this->storeManager->getStore()->getBaseUrl() . 'checkout/cart/add/product',
                    'params' => [
                        'form_key' => $this->formKey->getFormKey()
                    ]
                ],
                'pricing' => [
                    'url' => $this->storeManager->getStore()->getBaseUrl() . 'travers-catalog/pricing/view/products',
                    'params' => []
                ],
                'attributes' => [
                    'url' => $this->storeManager->getStore()->getBaseUrl() . 'travers-catalog/attributes/view/id',
                    'params' => []
                ]
            ],
            'flyoutAttributes' => $this->getFlyoutAttributes(),
            'defaultListAttributes' => $this->getListAttributes(),
            'currency' => $this->storeManager->getStore()->getCurrentCurrency()->getCode(),
            'defaultTableAttributes' => $this->getTableAttributes(),
            'searchTableAttributes' => $this->getSearchAttributes(),
            'hasSpotPricing' => $this->httpContext->getValue(\Magento\Customer\Model\Context::CONTEXT_AUTH),
            'defaultFilterAttributesInfo' => $this->getFilterAttributes(),
            'swatchImages' => $this->getSwatchImages(),
            'attributesValuesLimit' => $this->scopeConfig->getValue("catalog/reactapp/limit_filter_attr_values", ScopeInterface::SCOPE_STORE),
            'algolia' => [
                'applicationId' => $this->scopeConfig->getValue("algoliasearch_credentials/credentials/application_id", ScopeInterface::SCOPE_STORE),
                'searchApiKey' => $this->scopeConfig->getValue("algoliasearch_credentials/credentials/search_only_api_key", ScopeInterface::SCOPE_STORE),
                'productsIndexName' => $index_prefix . $this->storeManager->getStore()->getCode() . '_products',
                'categoriesIndexName' => $index_prefix . $this->storeManager->getStore()->getCode() . '_categories',
                'attributeOptionsIndexName' => $index_prefix . 'default_attribute_option'
            ]
        ];
        return $this->jsonEncoder->serialize($props);
    }

    /** 
     * Returns array with flyout attributes
     *
     * @return array
     */
    public function getFlyoutAttributes()
    {
        $collection = $this->collectionFactory->create();
        $collection->addFieldToFilter('include_in_flyout', ['eq' => 1]);
        $collection->setOrder('flyout_position','ASC');
        $attr = [];
        foreach ($collection as $item) {
            $attr[] = [
                'id' => $item->getAttributeCode(),
                'label' => $item->getStoreLabel()
            ];
        }
        return $attr;
    }

    /** 
     * Returns array with list attributes
     *
     * @return array
     */
    public function getListAttributes()
    {
        $listAttributes = $this->loadDataFromCache(static::LIST_ATTRIBUTES_CACHE_ID);
        if (!$listAttributes) {
            $collection = $this->collectionFactory->create();
            $collection->addFieldToFilter('include_in_list', ['eq' => 1]);
            $collection->setOrder('list_position','ASC');
            $listAttributes = [];
            foreach ($collection as $item) {
                $listAttributes[] = [
                    'id' => $item->getAttributeCode(),
                    'label' => $item->getStoreLabel()
                ];
            }
            $this->saveDataInsideCache($listAttributes, static::LIST_ATTRIBUTES_CACHE_ID);
        }

        return $listAttributes;
    }

    /** 
     * Returns array with table attributes
     *
     * @return array
     */
    public function getTableAttributes()
    {
        $tableAttributes = $this->loadDataFromCache(static::TABLE_ATTRIBUTES_CACHE_ID);
        if (!$tableAttributes) {
            $collection = $this->collectionFactory->create();
            $collection->addFieldToFilter('include_in_table', ['eq' => 1]);
            $collection->setOrder('table_position','ASC');
            $tableAttributes = [];
            foreach ($collection as $item) {
                $tableAttributes[] = [
                    'id' => $item->getAttributeCode(),
                    'label' => $item->getStoreLabel()
                ];
            }
            $tableAttributes = $this->attributeSorter->sort($tableAttributes, AttributeSorter::ARRAYED_ATTRIBUTES);
            $this->saveDataInsideCache($tableAttributes, static::TABLE_ATTRIBUTES_CACHE_ID);
        }

        return $tableAttributes;
    }

    /**
     * Returns array with search attributes
     *
     * @return array
     */
    public function getSearchAttributes()
    {
        $searchAttributes = $this->loadDataFromCache(static::SEARCH_ATTRIBUTES_CACHE_ID);
        if (!$searchAttributes) {
            $collection = $this->collectionFactory->create();
            $collection->addFieldToFilter('include_in_search_table', ['eq' => 1]);
            $collection->setOrder('search_position','ASC');
            $searchAttributes = [];
            foreach ($collection as $item) {
                $searchAttributes[] = [
                    'id' => $item->getAttributeCode(),
                    'label' => $item->getStoreLabel()
                ];
            }
            $searchAttributes = $this->attributeSorter->sort($searchAttributes, AttributeSorter::ARRAYED_ATTRIBUTES);
            $this->saveDataInsideCache($searchAttributes, static::SEARCH_ATTRIBUTES_CACHE_ID);
        }

        return $searchAttributes;
    }

    /** 
     * Returns array with filter attributes
     *
     * @return array
     */
    public function getFilterAttributes()
    {
        if ($this->request->getFullActionName() == 'catalogsearch_result_index') {
            $cacheId = static::FILTER_ATTRIBUTES_SEARCH_CACHE_ID;
        } else {
            $cacheId = static::FILTER_ATTRIBUTES_CACHE_ID;
        }

        $filterAttributes = $this->loadDataFromCache($cacheId);
        if (!$filterAttributes) {
            $collection = $this->collectionFactory->create();
            $collection->addFieldToFilter('is_filterable', ['eq' => 1]);

            if ($this->request->getFullActionName() == 'catalogsearch_result_index') {
                $collection->addFieldToFilter('is_filterable_in_search', ['eq' => 1]);
            }

            $collection->setOrder('position','ASC');
            $filterAttributes = [];
            foreach ($collection as $item) {
                $filterAttributes[] = [
                    'id' => $item->getAttributeCode(),
                    'searchable' => $item->getSearchableInLayeredNav(),
                    'label' => $item->getStoreLabel(),
                    'displayType' => $this->getDisplayType($item),
                    'description' => $item->getLayeredNavDescription()
                ];
            }
            $this->saveDataInsideCache($filterAttributes, $cacheId);
        }
        return $filterAttributes;
    }

    /** 
     * Returns attribute display type
     *
     * @return string
     */
    public function getDisplayType($attribute)
    {
        if ($this->swatchHelper->isVisualSwatch($attribute)) {
            return 'visual-swatch';
        } elseif ($this->swatchHelper->isTextSwatch($attribute)) {
            return 'text-swatch';
        } else {
            return $attribute->getFrontendInput();
        }
    }

    /** 
     * Returns array with swatch images
     *
     * @return array
     */
    public function getSwatchImages()
    {
        $collection = $this->collectionFactory->create();
        $collection->addFieldToFilter('is_filterable', ['eq' => 1]);
        $attr = [];
        foreach ($collection as $item) {
            if ($this->swatchHelper->isVisualSwatch($item)) {
                $options = $item->getSource()->getAllOptions();
                $valueIds = [];
                $optionsLabels = [];
                foreach ( $options as $option ) {
                    $valueIds[] = $option['value'];
                    $optionsLabels[$option['value']] = $option['label'];
                }
                $swatches = $this->swatchHelper->getSwatchesByOptionsId($valueIds);

                foreach ($swatches as $key => $swatch) {
                    if ($swatch['type'] == Swatch::SWATCH_TYPE_VISUAL_IMAGE) {
                        if (file_exists($this->directoryList->getPath('pub') . '/media/attribute/swatch' . $swatch['value'])) {
                            $swatchImage = $this->swatchHelperMedia->getSwatchAttributeImage('swatch_thumb', $swatch['value']);
                        } else {
                            $swatchImage = '';
                        }
                        $attr[$item->getAttributeCode()][] = [
                            'value' => $optionsLabels[$swatch['option_id']],
                            'image_url' => $swatchImage
                        ];
                    } elseif ($swatch['type'] == Swatch::SWATCH_TYPE_VISUAL_COLOR) {
                        $attr[$item->getAttributeCode()][] = [
                            'value' => $optionsLabels[$swatch['option_id']],
                            'hex_code' => $swatch['value']
                        ];
                    }
                }
            }
        }
        return $attr;
    }

    /**
     * Returns array attributes data
     *
     * @param  string $cacheId
     * @return array|boolean
     */
    public function loadDataFromCache($cacheId)
    {
        $data = $this->cache->load($cacheId);
        if (!$data) {
            return false;
        }

        $data = $this->serializer->unserialize($data);
        return $data;
    }

    /**
     * Returns array attributes data
     *
     * @param  array $data
     * @param  string $cacheId
     * @return array|boolean
     */
    public function saveDataInsideCache($data, $cacheId)
    {
        $data = $this->serializer->serialize($data);
        $this->cache->save($data, $cacheId);
    }
}
