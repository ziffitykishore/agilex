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

class ReactPlp implements \Magento\Framework\View\Element\Block\ArgumentInterface
{
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
        DirectoryList $directoryList

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
                    'url' => $this->storeManager->getStore()->getBaseUrl().'travers-catalog/cms/view/id',
                    'params' => []
                ],
                'grouping' => [
                    'url' => $this->storeManager->getStore()->getBaseUrl().'travers-catalog/grouping/view/id',
                    'params' => []
                ],
                'addToCart' => [
                    'url' => $this->storeManager->getStore()->getBaseUrl().'checkout/cart/add/product',
                    'params' => [
                        'form_key' => $this->formKey->getFormKey()
                    ]
                ],
                'pricing' => [
                    'url' => $this->storeManager->getStore()->getBaseUrl().'travers-catalog/pricing/view/products',
                    'params' => []
                ],
                'attributes' => [
                    'url' => $this->storeManager->getStore()->getBaseUrl().'travers-catalog/attributes/view/id',
                    'params' => []
                ]
            ],
            'flyoutAttributes' => $this->getFlyoutAttributes(),
            'defaultListAttributes' => [],
            'currency' => $this->storeManager->getStore()->getCurrentCurrency()->getCode(),
            'defaultTableAttributes' => [],
            'hasSpotPricing' => $this->httpContext->getValue(\Magento\Customer\Model\Context::CONTEXT_AUTH),
            'defaultFilterAttributesInfo' => [],
            'swatchImages' => $this->getSwatchImages(),
            'algolia' => [
                'applicationId' => $this->scopeConfig->getValue("algoliasearch_credentials/credentials/application_id", ScopeInterface::SCOPE_STORE),
                'searchApiKey' => $this->scopeConfig->getValue("algoliasearch_credentials/credentials/search_only_api_key", ScopeInterface::SCOPE_STORE),
                'productsIndexName' => $index_prefix.$this->storeManager->getStore()->getCode().'_products',
                'categoriesIndexName' => $index_prefix.$this->storeManager->getStore()->getCode().'_categories'
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
        $collection->addFieldToFilter('include_in_flyout', true);
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
        $collection = $this->collectionFactory->create();
        $collection->addFieldToFilter('include_in_list', true);
        $collection->setOrder('list_position','ASC');
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
     * Returns array with table attributes
     *
     * @return array
     */
    public function getTableAttributes()
    {
        $collection = $this->collectionFactory->create();
        $collection->addFieldToFilter('include_in_table', true);
        $collection->setOrder('table_position','ASC');
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
     * Returns array with filter attributes
     *
     * @return array
     */
    public function getFilterAttributes()
    {
        $collection = $this->collectionFactory->create();
        $collection->addFieldToFilter('is_filterable', true);
        $collection->setOrder('position','ASC');
        $attr = [];
        foreach ($collection as $item) {
            $attr[] = [
                'id' => $item->getAttributeCode(),
                'searchable' => $item->getSearchableInLayeredNav(),
                'label' => $item->getStoreLabel(),
                'displayType' => $this->getDisplayType($item),
                'description' => $item->getLayeredNavDescription()
            ];
        }
        return $attr;
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
        $collection->addFieldToFilter('is_filterable', true);
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
                        if (file_exists($this->directoryList->getPath('pub').'/media/attribute/swatch'.$swatch['value'])) {
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
}