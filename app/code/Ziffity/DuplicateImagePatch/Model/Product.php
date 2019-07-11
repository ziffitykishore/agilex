<?php

namespace Ziffity\DuplicateImagePatch\Model;

use Magento\CatalogImportExport\Model\Import\Product as ProductImport;
use Magento\Catalog\Api\ProductRepositoryInterface;
use Magento\Catalog\Model\Config as CatalogConfig;
use Magento\Catalog\Model\Product\Visibility;
use Magento\CatalogImportExport\Model\Import\Product\ImageTypeProcessor;
use Magento\CatalogImportExport\Model\Import\Product\MediaGalleryProcessor;
use Magento\CatalogImportExport\Model\Import\Product\RowValidatorInterface as ValidatorInterface;
use Magento\CatalogImportExport\Model\StockItemImporterInterface;
use Magento\CatalogInventory\Api\Data\StockItemInterface;
use Magento\Framework\App\Filesystem\DirectoryList;
use Magento\Framework\App\ObjectManager;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\Filesystem;
use Magento\Framework\Intl\DateTimeFactory;
use Magento\Framework\Model\ResourceModel\Db\ObjectRelationProcessor;
use Magento\Framework\Model\ResourceModel\Db\TransactionManagerInterface;
use Magento\Framework\Stdlib\DateTime;
use Magento\ImportExport\Model\Import;
use Magento\ImportExport\Model\Import\Entity\AbstractEntity;
use Magento\ImportExport\Model\Import\ErrorProcessing\ProcessingError;
use Magento\ImportExport\Model\Import\ErrorProcessing\ProcessingErrorAggregatorInterface;
use Magento\Store\Model\Store;

class Product extends ProductImport
{
    const CONFIG_KEY_PRODUCT_TYPES = 'global/importexport/import_product_types';

    /**
     * Size of bunch - part of products to save in one step.
     */
    const BUNCH_SIZE = 20;

    /**
     * Size of bunch to delete attributes of products in one step.
     */
    const ATTRIBUTE_DELETE_BUNCH = 1000;

    /**
     * Pseudo multi line separator in one cell.
     *
     * Can be used as custom option value delimiter or in configurable fields cells.
     */
    const PSEUDO_MULTI_LINE_SEPARATOR = '|';

    /**
     * Symbol between Name and Value between Pairs.
     */
    const PAIR_NAME_VALUE_SEPARATOR = '=';

    /**
     * Value that means all entities (e.g. websites, groups etc.)
     */
    const VALUE_ALL = 'all';

    /**
     * Data row scopes.
     */
    const SCOPE_DEFAULT = 1;

    const SCOPE_WEBSITE = 2;

    const SCOPE_STORE = 0;

    const SCOPE_NULL = -1;

    /**
     * Permanent column names.
     *
     * Names that begins with underscore is not an attribute. This name convention is for
     * to avoid interference with same attribute name.
     */

    /**
     * Column product store.
     */
    const COL_STORE = '_store';

    /**
     * Column product store view code.
     */
    const COL_STORE_VIEW_CODE = 'store_view_code';

    /**
     * Column website.
     */
    const COL_WEBSITE = 'website_code';

    /**
     * Column product attribute set.
     */
    const COL_ATTR_SET = '_attribute_set';

    /**
     * Column product type.
     */
    const COL_TYPE = 'product_type';

    /**
     * Column product category.
     */
    const COL_CATEGORY = 'categories';

    /**
     * Column product visibility.
     */
    const COL_VISIBILITY = 'visibility';

    /**
     * Column product sku.
     */
    const COL_SKU = 'sku';

    /**
     * Column product name.
     */
    const COL_NAME = 'name';

    /**
     * Column product website.
     */
    const COL_PRODUCT_WEBSITES = '_product_websites';

    /**
     * Media gallery attribute code.
     */
    const MEDIA_GALLERY_ATTRIBUTE_CODE = 'media_gallery';

    /**
     * Column media image.
     */
    const COL_MEDIA_IMAGE = '_media_image';

    /**
     * Inventory use config.
     */
    const INVENTORY_USE_CONFIG = 'Use Config';

    /**
     * Inventory use config prefix.
     */
    const INVENTORY_USE_CONFIG_PREFIX = 'use_config_';

    /**
     * Url key attribute code
     */
    const URL_KEY = 'url_key';

    /**
     * Attribute cache
     *
     * @var array
     */
    protected $_attributeCache = [];

    /**
     * Pairs of attribute set ID-to-name.
     *
     * @var array
     */
    protected $_attrSetIdToName = [];

    /**
     * Pairs of attribute set name-to-ID.
     *
     * @var array
     */
    protected $_attrSetNameToId = [];

    /**
     * @var string
     * @since 100.0.4
     */
    protected $mediaGalleryTableName;

    /**
     * @var string
     * @since 100.0.4
     */
    protected $mediaGalleryValueTableName;
    /**
     * @var string
     * @since 100.0.4
     */
    protected $mediaGalleryEntityToValueTableName;

    /**
     * @var string
     * @since 100.0.4
     */
    protected $productEntityTableName;

    /**
     * Attributes with index (not label) value.
     *
     * @var string[]
     */
    protected $_indexValueAttributes = [
        'status',
        'tax_class_id',
    ];

    /**
     * Links attribute name-to-link type ID.
     *
     * @var array
     */
    protected $_linkNameToId = [
        '_related_' => \Magento\Catalog\Model\Product\Link::LINK_TYPE_RELATED,
        '_crosssell_' => \Magento\Catalog\Model\Product\Link::LINK_TYPE_CROSSSELL,
        '_upsell_' => \Magento\Catalog\Model\Product\Link::LINK_TYPE_UPSELL,
    ];

    /**
     * Attributes codes which shows as date
     *
     * @var array
     * @since 100.1.2
     */
    protected $dateAttrCodes = [
        'special_from_date',
        'special_to_date',
        'news_from_date',
        'news_to_date',
        'custom_design_from',
        'custom_design_to'
    ];

    /**
     * Need to log in import history
     *
     * @var bool
     */
    protected $logInHistory = true;

    /**
     * Attribute id for product images storage.
     *
     * @var array
     */
    protected $_mediaGalleryAttributeId = null;

    /**
     * Validation failure message template definitions
     *
     * @var array
     * @codingStandardsIgnoreStart
     */
    protected $_messageTemplates = [
        ValidatorInterface::ERROR_INVALID_SCOPE => 'Invalid value in Scope column',
        ValidatorInterface::ERROR_INVALID_WEBSITE => 'Invalid value in Website column (website does not exist?)',
        ValidatorInterface::ERROR_INVALID_STORE => 'Invalid value in Store column (store doesn\'t exist?)',
        ValidatorInterface::ERROR_INVALID_ATTR_SET => 'Invalid value for Attribute Set column (set doesn\'t exist?)',
        ValidatorInterface::ERROR_INVALID_TYPE => 'Product Type is invalid or not supported',
        ValidatorInterface::ERROR_INVALID_CATEGORY => 'Category does not exist',
        ValidatorInterface::ERROR_VALUE_IS_REQUIRED => 'Please make sure attribute "%s" is not empty.',
        ValidatorInterface::ERROR_TYPE_CHANGED => 'Trying to change type of existing products',
        ValidatorInterface::ERROR_SKU_IS_EMPTY => 'SKU is empty',
        ValidatorInterface::ERROR_NO_DEFAULT_ROW => 'Default values row does not exist',
        ValidatorInterface::ERROR_CHANGE_TYPE => 'Product type change is not allowed',
        ValidatorInterface::ERROR_DUPLICATE_SCOPE => 'Duplicate scope',
        ValidatorInterface::ERROR_DUPLICATE_SKU => 'Duplicate SKU',
        ValidatorInterface::ERROR_CHANGE_ATTR_SET => 'Attribute set change is not allowed',
        ValidatorInterface::ERROR_TYPE_UNSUPPORTED => 'Product type is not supported',
        ValidatorInterface::ERROR_ROW_IS_ORPHAN => 'Orphan rows that will be skipped due default row errors',
        ValidatorInterface::ERROR_INVALID_TIER_PRICE_QTY => 'Tier Price data price or quantity value is invalid',
        ValidatorInterface::ERROR_INVALID_TIER_PRICE_SITE => 'Tier Price data website is invalid',
        ValidatorInterface::ERROR_INVALID_TIER_PRICE_GROUP => 'Tier Price customer group ID is invalid',
        ValidatorInterface::ERROR_TIER_DATA_INCOMPLETE => 'Tier Price data is incomplete',
        ValidatorInterface::ERROR_SKU_NOT_FOUND_FOR_DELETE => 'Product with specified SKU not found',
        ValidatorInterface::ERROR_SUPER_PRODUCTS_SKU_NOT_FOUND => 'Product with specified super products SKU not found',
        ValidatorInterface::ERROR_MEDIA_DATA_INCOMPLETE => 'Media data is incomplete',
        ValidatorInterface::ERROR_EXCEEDED_MAX_LENGTH => 'Attribute %s exceeded max length',
        ValidatorInterface::ERROR_INVALID_ATTRIBUTE_TYPE => 'Value for \'%s\' attribute contains incorrect value',
        ValidatorInterface::ERROR_ABSENT_REQUIRED_ATTRIBUTE => 'Attribute %s is required',
        ValidatorInterface::ERROR_INVALID_ATTRIBUTE_OPTION => 'Value for \'%s\' attribute contains incorrect value, see acceptable values on settings specified for Admin',
        ValidatorInterface::ERROR_DUPLICATE_UNIQUE_ATTRIBUTE => 'Duplicated unique attribute',
        ValidatorInterface::ERROR_INVALID_VARIATIONS_CUSTOM_OPTIONS => 'Value for \'%s\' sub attribute in \'%s\' attribute contains incorrect value, acceptable values are: \'dropdown\', \'checkbox\', \'radio\', \'text\'',
        ValidatorInterface::ERROR_INVALID_MEDIA_URL_OR_PATH => 'Wrong URL/path used for attribute %s',
        ValidatorInterface::ERROR_MEDIA_PATH_NOT_ACCESSIBLE => 'Imported resource (image) does not exist in the local media storage',
        ValidatorInterface::ERROR_MEDIA_URL_NOT_ACCESSIBLE => 'Imported resource (image) could not be downloaded from external resource due to timeout or access permissions',
        ValidatorInterface::ERROR_INVALID_WEIGHT => 'Product weight is invalid',
        ValidatorInterface::ERROR_DUPLICATE_URL_KEY => 'Url key: \'%s\' was already generated for an item with the SKU: \'%s\'. You need to specify the unique URL key manually',
        ValidatorInterface::ERROR_DUPLICATE_MULTISELECT_VALUES => 'Value for multiselect attribute %s contains duplicated values',
        'invalidNewToDateValue' => 'Make sure new_to_date is later than or the same as new_from_date',
    ];
    //@codingStandardsIgnoreEnd

    /**
     * Map between import file fields and system fields/attributes.
     *
     * @var array
     */
    protected $_fieldsMap = [
        'image' => 'base_image',
        'image_label' => "base_image_label",
        'thumbnail' => 'thumbnail_image',
        'thumbnail_label' => 'thumbnail_image_label',
        self::COL_MEDIA_IMAGE => 'additional_images',
        '_media_image_label' => 'additional_image_labels',
        '_media_is_disabled' => 'hide_from_product_page',
        Product::COL_STORE => 'store_view_code',
        Product::COL_ATTR_SET => 'attribute_set_code',
        Product::COL_TYPE => 'product_type',
        Product::COL_PRODUCT_WEBSITES => 'product_websites',
        'status' => 'product_online',
        'news_from_date' => 'new_from_date',
        'news_to_date' => 'new_to_date',
        'options_container' => 'display_product_options_in',
        'minimal_price' => 'map_price',
        'msrp' => 'msrp_price',
        'msrp_enabled' => 'map_enabled',
        'special_from_date' => 'special_price_from_date',
        'special_to_date' => 'special_price_to_date',
        'min_qty' => 'out_of_stock_qty',
        'backorders' => 'allow_backorders',
        'min_sale_qty' => 'min_cart_qty',
        'max_sale_qty' => 'max_cart_qty',
        'notify_stock_qty' => 'notify_on_stock_below',
        '_related_sku' => 'related_skus',
        '_related_position' => 'related_position',
        '_crosssell_sku' => 'crosssell_skus',
        '_crosssell_position' => 'crosssell_position',
        '_upsell_sku' => 'upsell_skus',
        '_upsell_position' => 'upsell_position',
        'meta_keyword' => 'meta_keywords',
    ];

    /**
     * Existing products SKU-related information in form of array:
     *
     * [SKU] => array(
     *     'type_id'        => (string) product type
     *     'attr_set_id'    => (int) product attribute set ID
     *     'entity_id'      => (int) product ID
     *     'supported_type' => (boolean) is product type supported by current version of import module
     * )
     *
     * @var array
     */
    protected $_oldSku = [];

    /**
     * Column names that holds values with particular meaning.
     *
     * @var string[]
     */
    protected $_specialAttributes = [
        self::COL_STORE,
        self::COL_ATTR_SET,
        self::COL_TYPE,
        self::COL_CATEGORY,
        '_product_websites',
        self::COL_PRODUCT_WEBSITES,
        '_tier_price_website',
        '_tier_price_customer_group',
        '_tier_price_qty',
        '_tier_price_price',
        '_related_sku',
        '_related_position',
        '_crosssell_sku',
        '_crosssell_position',
        '_upsell_sku',
        '_upsell_position',
        '_custom_option_store',
        '_custom_option_type',
        '_custom_option_title',
        '_custom_option_is_required',
        '_custom_option_price',
        '_custom_option_sku',
        '_custom_option_max_characters',
        '_custom_option_sort_order',
        '_custom_option_file_extension',
        '_custom_option_image_size_x',
        '_custom_option_image_size_y',
        '_custom_option_row_title',
        '_custom_option_row_price',
        '_custom_option_row_sku',
        '_custom_option_row_sort',
        '_media_attribute_id',
        self::COL_MEDIA_IMAGE,
        '_media_label',
        '_media_position',
        '_media_is_disabled',
    ];

    /**
     * @var array
     */
    protected $defaultStockData = [
        'manage_stock' => 1,
        'use_config_manage_stock' => 1,
        'qty' => 0,
        'min_qty' => 0,
        'use_config_min_qty' => 1,
        'min_sale_qty' => 1,
        'use_config_min_sale_qty' => 1,
        'max_sale_qty' => 10000,
        'use_config_max_sale_qty' => 1,
        'is_qty_decimal' => 0,
        'backorders' => 0,
        'use_config_backorders' => 1,
        'notify_stock_qty' => 1,
        'use_config_notify_stock_qty' => 1,
        'enable_qty_increments' => 0,
        'use_config_enable_qty_inc' => 1,
        'qty_increments' => 0,
        'use_config_qty_increments' => 1,
        'is_in_stock' => 1,
        'low_stock_date' => null,
        'stock_status_changed_auto' => 0,
        'is_decimal_divided' => 0,
    ];

    /**
     * Column names that holds images files names
     *
     * Note: the order of array items has a value in order to properly set 'position' value
     * of media gallery items.
     *
     * @var string[]
     */
    protected $_imagesArrayKeys = [];

    /**
     * Permanent entity columns.
     *
     * @var string[]
     */
    protected $_permanentAttributes = [self::COL_SKU];

    /**
     * Array of supported product types as keys with appropriate model object as value.
     *
     * @var \Magento\CatalogImportExport\Model\Import\Product\Type\AbstractType[]
     */
    protected $_productTypeModels = [];

    /**
     * Media files uploader
     *
     * @var \Magento\CatalogImportExport\Model\Import\Uploader
     */
    protected $_fileUploader;

    /**
     * Import entity which provide import of product custom options
     *
     * @var \Magento\CatalogImportExport\Model\Import\Product\Option
     */
    protected $_optionEntity;

    /**
     * Catalog data
     *
     * @var \Magento\Catalog\Helper\Data
     */
    protected $_catalogData = null;

    /**
     * @var \Magento\CatalogInventory\Api\StockRegistryInterface
     */
    protected $stockRegistry;

    /**
     * @var \Magento\CatalogInventory\Api\StockConfigurationInterface
     */
    protected $stockConfiguration;

    /**
     * @var \Magento\CatalogInventory\Model\Spi\StockStateProviderInterface
     */
    protected $stockStateProvider;

    /**
     * Core event manager proxy
     *
     * @var \Magento\Framework\Event\ManagerInterface
     */
    protected $_eventManager = null;

    /**
     * @var \Magento\ImportExport\Model\Import\Config
     */
    protected $_importConfig;

    /**
     * @var \Magento\CatalogImportExport\Model\Import\Proxy\Product\ResourceModelFactory
     */
    protected $_resourceFactory;

    /**
     * @var \Magento\CatalogImportExport\Model\Import\Proxy\Product\ResourceModel
     */
    protected $_resource;

    /**
     * @var \Magento\Eav\Model\ResourceModel\Entity\Attribute\Set\CollectionFactory
     */
    protected $_setColFactory;

    /**
     * @var \Magento\CatalogImportExport\Model\Import\Product\Type\Factory
     */
    protected $_productTypeFactory;

    /**
     * @var \Magento\Catalog\Model\ResourceModel\Product\LinkFactory
     */
    protected $_linkFactory;

    /**
     * @var \Magento\CatalogImportExport\Model\Import\Proxy\ProductFactory
     */
    protected $_proxyProdFactory;

    /**
     * @var \Magento\CatalogImportExport\Model\Import\UploaderFactory
     */
    protected $_uploaderFactory;

    /**
     * @var \Magento\Framework\Filesystem\Directory\WriteInterface
     */
    protected $_mediaDirectory;

    /**
     * @var \Magento\CatalogInventory\Model\ResourceModel\Stock\ItemFactory
     * @deprecated 101.0.0 this variable isn't used anymore.
     */
    protected $_stockResItemFac;

    /**
     * @var \Magento\Framework\Stdlib\DateTime\TimezoneInterface
     */
    protected $_localeDate;

    /**
     * @var DateTime
     */
    protected $dateTime;

    /**
     * @var \Magento\Framework\Indexer\IndexerRegistry
     */
    protected $indexerRegistry;

    /**
     * @var Product\StoreResolver
     */
    protected $storeResolver;

    /**
     * @var Product\SkuProcessor
     */
    protected $skuProcessor;

    /**
     * @var Product\CategoryProcessor
     */
    protected $categoryProcessor;

    /**
     * @var \Magento\Framework\App\Config\ScopeConfigInterface
     * @since 100.0.3
     */
    protected $scopeConfig;

    /**
     * @var \Magento\Catalog\Model\Product\Url
     * @since 100.0.3
     */
    protected $productUrl;

    /**
     * @var array
     */
    protected $websitesCache = [];

    /**
     * @var array
     */
    protected $categoriesCache = [];

    /**
     * @var array
     * @since 100.0.3
     */
    protected $productUrlSuffix = [];

    /**
     * @var array
     * @deprecated 100.0.3
     * @since 100.0.3
     */
    protected $productUrlKeys = [];

    /**
     * Instance of product tax class processor.
     *
     * @var Product\TaxClassProcessor
     */
    protected $taxClassProcessor;

    /**
     * @var Product\Validator
     */
    protected $validator;

    /**
     * Array of validated rows.
     *
     * @var array
     */
    protected $validatedRows;

    /**
     * @var \Psr\Log\LoggerInterface
     */
    private $_logger;

    /**
     * {@inheritdoc}
     */
    protected $masterAttributeCode = 'sku';

    /**
     * @var ObjectRelationProcessor
     */
    protected $objectRelationProcessor;

    /**
     * @var TransactionManagerInterface
     */
    protected $transactionManager;

    /**
     * Flag for replace operation.
     *
     * @var null
     */
    protected $_replaceFlag = null;

    /**
     * Flag for replace operation.
     *
     * @var null
     */
    protected $cachedImages = null;

    /**
     * @var array
     * @since 100.0.3
     */
    protected $urlKeys = [];

    /**
     * @var array
     * @since 100.0.3
     */
    protected $rowNumbers = [];

    /**
     * Product entity link field
     *
     * @var string
     */
    private $productEntityLinkField;

    /**
     * Product entity identifier field
     *
     * @var string
     */
    private $productEntityIdentifierField;

    /**
     * Escaped separator value for regular expression.
     * The value is based on PSEUDO_MULTI_LINE_SEPARATOR constant.
     * @var string
     */
    private $multiLineSeparatorForRegexp;

    /**
     * Container for filesystem object.
     *
     * @var Filesystem
     */
    private $filesystem;

    /**
     * Catalog config.
     *
     * @var CatalogConfig
     */
    private $catalogConfig;

    /**
     * Stock Item Importer
     *
     * @var StockItemImporterInterface
     */
    private $stockItemImporter;

    /**
     * @var ImageTypeProcessor
     */
    private $imageTypeProcessor;

    /**
     * Provide ability to process and save images during import.
     *
     * @var MediaGalleryProcessor
     */
    private $mediaProcessor;

    /**
     * @var DateTimeFactory
     */
    private $dateTimeFactory;

    /**
     * @var ProductRepositoryInterface
     */
    private $productRepository;

    /**
     * @param \Magento\Framework\Json\Helper\Data $jsonHelper
     * @param \Magento\ImportExport\Helper\Data $importExportData
     * @param \Magento\ImportExport\Model\ResourceModel\Import\Data $importData
     * @param \Magento\Eav\Model\Config $config
     * @param \Magento\Framework\App\ResourceConnection $resource
     * @param \Magento\ImportExport\Model\ResourceModel\Helper $resourceHelper
     * @param \Magento\Framework\Stdlib\StringUtils $string
     * @param ProcessingErrorAggregatorInterface $errorAggregator
     * @param \Magento\Framework\Event\ManagerInterface $eventManager
     * @param \Magento\CatalogInventory\Api\StockRegistryInterface $stockRegistry
     * @param \Magento\CatalogInventory\Api\StockConfigurationInterface $stockConfiguration
     * @param \Magento\CatalogInventory\Model\Spi\StockStateProviderInterface $stockStateProvider
     * @param \Magento\Catalog\Helper\Data $catalogData
     * @param \Magento\ImportExport\Model\Import\Config $importConfig
     * @param Proxy\Product\ResourceModelFactory $resourceFactory
     * @param Product\OptionFactory $optionFactory
     * @param \Magento\Eav\Model\ResourceModel\Entity\Attribute\Set\CollectionFactory $setColFactory
     * @param Product\Type\Factory $productTypeFactory
     * @param \Magento\Catalog\Model\ResourceModel\Product\LinkFactory $linkFactory
     * @param Proxy\ProductFactory $proxyProdFactory
     * @param UploaderFactory $uploaderFactory
     * @param \Magento\Framework\Filesystem $filesystem
     * @param \Magento\CatalogInventory\Model\ResourceModel\Stock\ItemFactory $stockResItemFac
     * @param DateTime\TimezoneInterface $localeDate
     * @param DateTime $dateTime
     * @param \Psr\Log\LoggerInterface $logger
     * @param \Magento\Framework\Indexer\IndexerRegistry $indexerRegistry
     * @param Product\StoreResolver $storeResolver
     * @param Product\SkuProcessor $skuProcessor
     * @param Product\CategoryProcessor $categoryProcessor
     * @param Product\Validator $validator
     * @param ObjectRelationProcessor $objectRelationProcessor
     * @param TransactionManagerInterface $transactionManager
     * @param Product\TaxClassProcessor $taxClassProcessor
     * @param \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig
     * @param \Magento\Catalog\Model\Product\Url $productUrl
     * @param array $data
     * @param array $dateAttrCodes
     * @param CatalogConfig $catalogConfig
     * @param ImageTypeProcessor $imageTypeProcessor
     * @param MediaGalleryProcessor $mediaProcessor
     * @param StockItemImporterInterface|null $stockItemImporter
     * @param DateTimeFactory $dateTimeFactory
     * @param ProductRepositoryInterface|null $productRepository
     * @throws LocalizedException
     * @throws \Magento\Framework\Exception\FileSystemException
     * @SuppressWarnings(PHPMD.ExcessiveParameterList)
     * @SuppressWarnings(PHPMD.ExcessiveMethodLength)
     */
    public function __construct(
        \Magento\Framework\Json\Helper\Data $jsonHelper,
        \Magento\ImportExport\Helper\Data $importExportData,
        \Magento\ImportExport\Model\ResourceModel\Import\Data $importData,
        \Magento\Eav\Model\Config $config,
        \Magento\Framework\App\ResourceConnection $resource,
        \Magento\ImportExport\Model\ResourceModel\Helper $resourceHelper,
        \Magento\Framework\Stdlib\StringUtils $string,
        ProcessingErrorAggregatorInterface $errorAggregator,
        \Magento\Framework\Event\ManagerInterface $eventManager,
        \Magento\CatalogInventory\Api\StockRegistryInterface $stockRegistry,
        \Magento\CatalogInventory\Api\StockConfigurationInterface $stockConfiguration,
        \Magento\CatalogInventory\Model\Spi\StockStateProviderInterface $stockStateProvider,
        \Magento\Catalog\Helper\Data $catalogData,
        \Magento\ImportExport\Model\Import\Config $importConfig,
        \Magento\CatalogImportExport\Model\Import\Proxy\Product\ResourceModelFactory $resourceFactory,
        \Magento\CatalogImportExport\Model\Import\Product\OptionFactory $optionFactory,
        \Magento\Eav\Model\ResourceModel\Entity\Attribute\Set\CollectionFactory $setColFactory,
        \Magento\CatalogImportExport\Model\Import\Product\Type\Factory $productTypeFactory,
        \Magento\Catalog\Model\ResourceModel\Product\LinkFactory $linkFactory,
        \Magento\CatalogImportExport\Model\Import\Proxy\ProductFactory $proxyProdFactory,
        \Magento\CatalogImportExport\Model\Import\UploaderFactory $uploaderFactory,
        \Magento\Framework\Filesystem $filesystem,
        \Magento\CatalogInventory\Model\ResourceModel\Stock\ItemFactory $stockResItemFac,
        \Magento\Framework\Stdlib\DateTime\TimezoneInterface $localeDate,
        DateTime $dateTime,
        \Psr\Log\LoggerInterface $logger,
        \Magento\Framework\Indexer\IndexerRegistry $indexerRegistry,
        \Magento\CatalogImportExport\Model\Import\Product\StoreResolver $storeResolver,
        \Magento\CatalogImportExport\Model\Import\Product\SkuProcessor $skuProcessor,
        \Magento\CatalogImportExport\Model\Import\Product\CategoryProcessor $categoryProcessor,
        \Magento\CatalogImportExport\Model\Import\Product\Validator $validator,
        ObjectRelationProcessor $objectRelationProcessor,
        TransactionManagerInterface $transactionManager,
        \Magento\CatalogImportExport\Model\Import\Product\TaxClassProcessor $taxClassProcessor,
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig,
        \Magento\Catalog\Model\Product\Url $productUrl,
        array $data = [],
        array $dateAttrCodes = [],
        CatalogConfig $catalogConfig = null,
        ImageTypeProcessor $imageTypeProcessor = null,
        MediaGalleryProcessor $mediaProcessor = null,
        StockItemImporterInterface $stockItemImporter = null,
        DateTimeFactory $dateTimeFactory = null,
        ProductRepositoryInterface $productRepository = null
    ) {
        $this->_logger = $logger;
        $this->filesystem = $filesystem;
        $this->dateAttrCodes = array_merge($this->dateAttrCodes, $dateAttrCodes);
        $this->catalogConfig = $catalogConfig ?: ObjectManager::getInstance()->get(CatalogConfig::class);
        $this->imageTypeProcessor = $imageTypeProcessor ?: ObjectManager::getInstance()->get(ImageTypeProcessor::class);
        $this->mediaProcessor = $mediaProcessor ?: ObjectManager::getInstance()->get(MediaGalleryProcessor::class);
        $this->stockItemImporter = $stockItemImporter ?: ObjectManager::getInstance()
            ->get(StockItemImporterInterface::class);        
        parent::__construct(
            $jsonHelper,
            $importExportData,
            $importData,
            $config,
            $resource,
            $resourceHelper,
            $string,
            $errorAggregator,
            $eventManager,
            $stockRegistry,
            $stockConfiguration,
            $stockStateProvider,
            $catalogData,
            $importConfig,
            $resourceFactory,
            $optionFactory,
            $setColFactory,
            $productTypeFactory,
            $linkFactory,
            $proxyProdFactory,
            $uploaderFactory,
            $filesystem,
            $stockResItemFac,
            $localeDate,
            $dateTime,
            $logger,
            $indexerRegistry,
            $storeResolver,
            $skuProcessor,
            $categoryProcessor,
            $validator,
            $objectRelationProcessor,
            $transactionManager,
            $taxClassProcessor,
            $scopeConfig,
            $productUrl                
        );
    } 
    
    /**
     * Gather and save information about product entities.
     *
     * @return $this
     * @SuppressWarnings(PHPMD.CyclomaticComplexity)
     * @SuppressWarnings(PHPMD.NPathComplexity)
     * @SuppressWarnings(PHPMD.ExcessiveMethodLength)
     * @SuppressWarnings(PHPMD.UnusedLocalVariable)
     * @throws LocalizedException
     */
    protected function _saveProducts()
    {
        $priceIsGlobal = $this->_catalogData->isPriceGlobal();
        $productLimit = null;
        $productsQty = null;
        $entityLinkField = $this->getProductEntityLinkField();

        while ($bunch = $this->_dataSourceModel->getNextBunch()) {
            $entityRowsIn = [];
            $entityRowsUp = [];
            $attributes = [];
            $this->websitesCache = [];
            $this->categoriesCache = [];
            $tierPrices = [];
            $mediaGallery = [];
            $labelsForUpdate = [];
            $imagesForChangeVisibility = [];
            $uploadedImages = [];
            $previousType = null;
            $prevAttributeSet = null;
            $importDir = $this->_mediaDirectory->getAbsolutePath($this->getImportDir());

            $existingImages = $this->getExistingImages($bunch);

            $this->addImageHashes($existingImages);

            foreach ($bunch as $rowNum => $rowData) {
                // reset category processor's failed categories array
                $this->categoryProcessor->clearFailedCategories();

                if (!$this->validateRow($rowData, $rowNum)) {
                    continue;
                }
                if ($this->getErrorAggregator()->hasToBeTerminated()) {
                    $this->getErrorAggregator()->addRowToSkip($rowNum);
                    continue;
                }
                $rowScope = $this->getRowScope($rowData);

                $urlKey = $this->getUrlKey($rowData);
                if (!empty($rowData[self::URL_KEY])) {
                    // If url_key column and its value were in the CSV file
                    $rowData[self::URL_KEY] = $urlKey;
                } elseif ($this->isNeedToChangeUrlKey($rowData)) {
                    // If url_key column was empty or even not declared in the CSV file but by the rules it is need to
                    // be setteed. In case when url_key is generating from name column we have to ensure that the bunch
                    // of products will pass for the event with url_key column.
                    $bunch[$rowNum][self::URL_KEY] = $rowData[self::URL_KEY] = $urlKey;
                }

                $rowSku = $rowData[self::COL_SKU];

                if (null === $rowSku) {
                    $this->getErrorAggregator()->addRowToSkip($rowNum);
                    continue;
                }

                if (self::SCOPE_STORE == $rowScope) {
                    // set necessary data from SCOPE_DEFAULT row
                    $rowData[self::COL_TYPE] = $this->skuProcessor->getNewSku($rowSku)['type_id'];
                    $rowData['attribute_set_id'] = $this->skuProcessor->getNewSku($rowSku)['attr_set_id'];
                    $rowData[self::COL_ATTR_SET] = $this->skuProcessor->getNewSku($rowSku)['attr_set_code'];
                }

                // 1. Entity phase
                if ($this->isSkuExist($rowSku)) {
                    // existing row
                    if (isset($rowData['attribute_set_code'])) {
                        $attributeSetId = $this->catalogConfig->getAttributeSetId(
                            $this->getEntityTypeId(),
                            $rowData['attribute_set_code']
                        );

                        // wrong attribute_set_code was received
                        if (!$attributeSetId) {
                            throw new LocalizedException(
                                __(
                                    'Wrong attribute set code "%1", please correct it and try again.',
                                    $rowData['attribute_set_code']
                                )
                            );
                        }
                    } else {
                        $attributeSetId = $this->skuProcessor->getNewSku($rowSku)['attr_set_id'];
                    }

                    $entityRowsUp[] = [
                        'updated_at' => (new \DateTime())->format(DateTime::DATETIME_PHP_FORMAT),
                        'attribute_set_id' => $attributeSetId,
                        $entityLinkField => $this->getExistingSku($rowSku)[$entityLinkField]
                    ];
                } else {
                    if (!$productLimit || $productsQty < $productLimit) {
                        $entityRowsIn[strtolower($rowSku)] = [
                            'attribute_set_id' => $this->skuProcessor->getNewSku($rowSku)['attr_set_id'],
                            'type_id' => $this->skuProcessor->getNewSku($rowSku)['type_id'],
                            'sku' => $rowSku,
                            'has_options' => isset($rowData['has_options']) ? $rowData['has_options'] : 0,
                            'created_at' => (new \DateTime())->format(DateTime::DATETIME_PHP_FORMAT),
                            'updated_at' => (new \DateTime())->format(DateTime::DATETIME_PHP_FORMAT),
                        ];
                        $productsQty++;
                    } else {
                        $rowSku = null;
                        // sign for child rows to be skipped
                        $this->getErrorAggregator()->addRowToSkip($rowNum);
                        continue;
                    }
                }

                if (!array_key_exists($rowSku, $this->websitesCache)) {
                    $this->websitesCache[$rowSku] = [];
                }
                // 2. Product-to-Website phase
                if (!empty($rowData[self::COL_PRODUCT_WEBSITES])) {
                    $websiteCodes = explode($this->getMultipleValueSeparator(), $rowData[self::COL_PRODUCT_WEBSITES]);
                    foreach ($websiteCodes as $websiteCode) {
                        $websiteId = $this->storeResolver->getWebsiteCodeToId($websiteCode);
                        $this->websitesCache[$rowSku][$websiteId] = true;
                    }
                } else {
                    $product = $this->retrieveProductBySku($rowSku);
                    if ($product) {
                        $websiteIds = $product->getWebsiteIds();
                        foreach ($websiteIds as $websiteId) {
                            $this->websitesCache[$rowSku][$websiteId] = true;
                        }
                    }
                }

                // 3. Categories phase
                if (!array_key_exists($rowSku, $this->categoriesCache)) {
                    $this->categoriesCache[$rowSku] = [];
                }
                $rowData['rowNum'] = $rowNum;
                $categoryIds = $this->processRowCategories($rowData);
                foreach ($categoryIds as $id) {
                    $this->categoriesCache[$rowSku][$id] = true;
                }
                unset($rowData['rowNum']);

                // 4.1. Tier prices phase
                if (!empty($rowData['_tier_price_website'])) {
                    $tierPrices[$rowSku][] = [
                        'all_groups' => $rowData['_tier_price_customer_group'] == self::VALUE_ALL,
                        'customer_group_id' => $rowData['_tier_price_customer_group'] ==
                        self::VALUE_ALL ? 0 : $rowData['_tier_price_customer_group'],
                        'qty' => $rowData['_tier_price_qty'],
                        'value' => $rowData['_tier_price_price'],
                        'website_id' => self::VALUE_ALL == $rowData['_tier_price_website'] ||
                        $priceIsGlobal ? 0 : $this->storeResolver->getWebsiteCodeToId($rowData['_tier_price_website']),
                    ];
                }

                if (!$this->validateRow($rowData, $rowNum)) {
                    continue;
                }

                // 5. Media gallery phase
                list($rowImages, $rowLabels) = $this->getImagesFromRow($rowData);
                $storeId = !empty($rowData[self::COL_STORE])
                    ? $this->getStoreIdByCode($rowData[self::COL_STORE])
                    : Store::DEFAULT_STORE_ID;
                $imageHiddenStates = $this->getImagesHiddenStates($rowData);
                foreach (array_keys($imageHiddenStates) as $image) {
                    if (array_key_exists($rowSku, $existingImages)
                        && array_key_exists($image, $existingImages[$rowSku])
                    ) {
                        $rowImages[self::COL_MEDIA_IMAGE][] = $image;
                        $uploadedImages[$image] = $image;
                    }

                    if (empty($rowImages)) {
                        $rowImages[self::COL_MEDIA_IMAGE][] = $image;
                    }
                }

                $rowData[self::COL_MEDIA_IMAGE] = [];

                /*
                 * Note: to avoid problems with undefined sorting, the value of media gallery items positions
                 * must be unique in scope of one product.
                 */
                $position = 0;
                foreach ($rowImages as $column => $columnImages) {
                    foreach ($columnImages as $columnImageKey => $columnImage) {
                        $filename = $importDir . DIRECTORY_SEPARATOR . $columnImage;
                        $hash = '';
                        if ($this->_mediaDirectory->isReadable($filename)) {
                            $hash = md5_file($filename);
                        }

                        if (!isset($existingImages[$rowSku])) {
                            $imageAlreadyExists = false;
                        } else {
                            $imageAlreadyExists = array_reduce($existingImages[$rowSku], function ($exists, $file) use ($hash) {
                                if ($exists) {
                                    return $exists;
                                }
                                if ($file['hash'] === $hash) {
                                    return $file['value'];
                                }
                                return $exists;
                            }, '');
                        }

                        if ($imageAlreadyExists) {
                            $uploadedFile = $imageAlreadyExists;
                        } else {
                            if (!isset($uploadedImages[$columnImage])) {
                                $uploadedFile = $this->uploadMediaFiles($columnImage);
                                $uploadedFile = $uploadedFile ?: $this->getSystemFile($columnImage);
                                if ($uploadedFile) {
                                    $uploadedImages[$columnImage] = $uploadedFile;
                                } else {
                                    unset($rowData[$column]);
                                    $this->skipRow($rowNum, ValidatorInterface::ERROR_MEDIA_URL_NOT_ACCESSIBLE);
                                }
                            } else {
                                $uploadedFile = $uploadedImages[$columnImage];
                            }
                        }

                        if ($uploadedFile && $column !== self::COL_MEDIA_IMAGE) {
                            $rowData[$column] = $uploadedFile;
                        }

                        if ($uploadedFile && !isset($mediaGallery[$storeId][$rowSku][$uploadedFile])) {
                            if (isset($existingImages[$rowSku][$uploadedFile])) {
                                $currentFileData = $existingImages[$rowSku][$uploadedFile];
                                if (isset($rowLabels[$column][$columnImageKey])
                                    && $rowLabels[$column][$columnImageKey] !=
                                    $currentFileData['label']
                                ) {
                                    $labelsForUpdate[] = [
                                        'label' => $rowLabels[$column][$columnImageKey],
                                        'imageData' => $currentFileData
                                    ];
                                }

                                if (array_key_exists($uploadedFile, $imageHiddenStates)
                                    && $currentFileData['disabled'] != $imageHiddenStates[$uploadedFile]
                                ) {
                                    $imagesForChangeVisibility[] = [
                                        'disabled' => $imageHiddenStates[$uploadedFile],
                                        'imageData' => $currentFileData
                                    ];
                                }
                            } else {
                                if ($column == self::COL_MEDIA_IMAGE) {
                                    $rowData[$column][] = $uploadedFile;
                                }
                                $mediaGallery[$storeId][$rowSku][$uploadedFile] = [
                                    'attribute_id' => $this->getMediaGalleryAttributeId(),
                                    'label' => isset($rowLabels[$column][$columnImageKey])
                                        ? $rowLabels[$column][$columnImageKey]
                                        : '',
                                    'position' => ++$position,
                                    'disabled' => isset($imageHiddenStates[$columnImage])
                                        ? $imageHiddenStates[$columnImage] : '0',
                                    'value' => $uploadedFile,
                                ];
                            }
                        }
                    }
                }

                // 6. Attributes phase
                $rowStore = (self::SCOPE_STORE == $rowScope)
                    ? $this->storeResolver->getStoreCodeToId($rowData[self::COL_STORE])
                    : 0;
                $productType = isset($rowData[self::COL_TYPE]) ? $rowData[self::COL_TYPE] : null;
                if ($productType !== null) {
                    $previousType = $productType;
                }
                if (isset($rowData[self::COL_ATTR_SET])) {
                    $prevAttributeSet = $rowData[self::COL_ATTR_SET];
                }
                if (self::SCOPE_NULL == $rowScope) {
                    // for multiselect attributes only
                    if ($prevAttributeSet !== null) {
                        $rowData[self::COL_ATTR_SET] = $prevAttributeSet;
                    }
                    if ($productType === null && $previousType !== null) {
                        $productType = $previousType;
                    }
                    if ($productType === null) {
                        continue;
                    }
                }

                $productTypeModel = $this->_productTypeModels[$productType];
                if (!empty($rowData['tax_class_name'])) {
                    $rowData['tax_class_id'] =
                        $this->taxClassProcessor->upsertTaxClass($rowData['tax_class_name'], $productTypeModel);
                }

                if ($this->getBehavior() == Import::BEHAVIOR_APPEND ||
                    empty($rowData[self::COL_SKU])
                ) {
                    $rowData = $productTypeModel->clearEmptyData($rowData);
                }

                $rowData = $productTypeModel->prepareAttributesWithDefaultValueForSave(
                    $rowData,
                    !$this->isSkuExist($rowSku)
                );
                $product = $this->_proxyProdFactory->create(['data' => $rowData]);

                foreach ($rowData as $attrCode => $attrValue) {
                    $attribute = $this->retrieveAttributeByCode($attrCode);

                    if ('multiselect' != $attribute->getFrontendInput() && self::SCOPE_NULL == $rowScope) {
                        // skip attribute processing for SCOPE_NULL rows
                        continue;
                    }
                    $attrId = $attribute->getId();
                    $backModel = $attribute->getBackendModel();
                    $attrTable = $attribute->getBackend()->getTable();
                    $storeIds = [0];

                    if ('datetime' == $attribute->getBackendType()
                        && (
                            in_array($attribute->getAttributeCode(), $this->dateAttrCodes)
                            || $attribute->getIsUserDefined()
                        )
                    ) {
                        $attrValue = $this->dateTime->formatDate($attrValue, false);
                    } elseif ('datetime' == $attribute->getBackendType() && strtotime($attrValue)) {
                        $attrValue = gmdate(
                            'Y-m-d H:i:s',
                            $this->_localeDate->date($attrValue)->getTimestamp()
                        );
                    } elseif ($backModel) {
                        $attribute->getBackend()->beforeSave($product);
                        $attrValue = $product->getData($attribute->getAttributeCode());
                    }
                    if (self::SCOPE_STORE == $rowScope) {
                        if (self::SCOPE_WEBSITE == $attribute->getIsGlobal()) {
                            // check website defaults already set
                            if (!isset($attributes[$attrTable][$rowSku][$attrId][$rowStore])) {
                                $storeIds = $this->storeResolver->getStoreIdToWebsiteStoreIds($rowStore);
                            }
                        } elseif (self::SCOPE_STORE == $attribute->getIsGlobal()) {
                            $storeIds = [$rowStore];
                        }
                        if (!$this->isSkuExist($rowSku)) {
                            $storeIds[] = 0;
                        }
                    }
                    foreach ($storeIds as $storeId) {
                        if (!isset($attributes[$attrTable][$rowSku][$attrId][$storeId])) {
                            $attributes[$attrTable][$rowSku][$attrId][$storeId] = $attrValue;
                        }
                    }
                    // restore 'backend_model' to avoid 'default' setting
                    $attribute->setBackendModel($backModel);
                }
            }

            foreach ($bunch as $rowNum => $rowData) {
                if ($this->getErrorAggregator()->isRowInvalid($rowNum)) {
                    unset($bunch[$rowNum]);
                }
            }

            $this->saveProductEntity(
                $entityRowsIn,
                $entityRowsUp
            )->_saveProductWebsites(
                $this->websitesCache
            )->_saveProductCategories(
                $this->categoriesCache
            )->_saveProductTierPrices(
                $tierPrices
            )->_saveMediaGallery(
                $mediaGallery
            )->_saveProductAttributes(
                $attributes
            )->updateMediaGalleryVisibility(
                $imagesForChangeVisibility
            )->updateMediaGalleryLabels(
                $labelsForUpdate
            );

            $this->_eventManager->dispatch(
                'catalog_product_import_bunch_save_after',
                ['adapter' => $this, 'bunch' => $bunch]
            );
        }

        return $this;
    }    
    
    /**
     * Generate md5 hashes for existing images for comparison with newly uploaded images.
     *
     * @param array $images
     */
    public function addImageHashes(&$images) {
        $dirConfig = DirectoryList::getDefaultConfig();
        $dirAddon = $dirConfig[DirectoryList::MEDIA][DirectoryList::PATH];
        $productPath = $this->_mediaDirectory->getAbsolutePath($dirAddon . '/catalog/product');

        foreach ($images as $sku => $files) {
            foreach ($files as $path => $file) {
                $images[$sku][$path]['hash'] = md5_file($productPath . $file['value']);
            }
        }
    }

    /**
     * Returns the import directory if specified or a default import directory (media/import).
     *
     * @return string
     */
    protected function getImportDir()
    {
        $dirConfig = DirectoryList::getDefaultConfig();
        $dirAddon = $dirConfig[DirectoryList::MEDIA][DirectoryList::PATH];

        if (!empty($this->_parameters[Import::FIELD_NAME_IMG_FILE_DIR])) {
            $tmpPath = $this->_parameters[Import::FIELD_NAME_IMG_FILE_DIR];
        } else {
            $tmpPath = $dirAddon . '/' . $this->_mediaDirectory->getRelativePath('import');
        }
        return $tmpPath;
    }

    /**
     * Returns an object for upload a media files
     *
     * @return \Magento\CatalogImportExport\Model\Import\Uploader
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    protected function _getUploader()
    {
        if ($this->_fileUploader === null) {
            $this->_fileUploader = $this->_uploaderFactory->create();

            $this->_fileUploader->init();

            $dirConfig = DirectoryList::getDefaultConfig();
            $dirAddon = $dirConfig[DirectoryList::MEDIA][DirectoryList::PATH];

            $tmpPath = $this->getImportDir();

            if (!$this->_fileUploader->setTmpDir($tmpPath)) {
                throw new LocalizedException(
                    __('File directory \'%1\' is not readable.', $tmpPath)
                );
            }
            $destinationDir = "catalog/product";
            $destinationPath = $dirAddon . '/' . $this->_mediaDirectory->getRelativePath($destinationDir);

            $this->_mediaDirectory->create($destinationPath);
            if (!$this->_fileUploader->setDestDir($destinationPath)) {
                throw new LocalizedException(
                    __('File directory \'%1\' is not writable.', $destinationPath)
                );
            }
        }
        return $this->_fileUploader;
    }    
    
    /**
     * Get product entity link field
     *
     * @return string
     */
    private function getProductEntityLinkField()
    {
        if (!$this->productEntityLinkField) {
            $this->productEntityLinkField = $this->getMetadataPool()
                ->getMetadata(\Magento\Catalog\Api\Data\ProductInterface::class)
                ->getLinkField();
        }
        return $this->productEntityLinkField;
    }

    /**
     * Whether a url key is needed to be change.
     *
     * @param array $rowData
     * @return bool
     */
    private function isNeedToChangeUrlKey(array $rowData): bool
    {
        $urlKey = $this->getUrlKey($rowData);
        $productExists = $this->isSkuExist($rowData[self::COL_SKU]);
        $markedToEraseUrlKey = isset($rowData[self::URL_KEY]);
        // The product isn't new and the url key index wasn't marked for change.
        if (!$urlKey && $productExists && !$markedToEraseUrlKey) {
            // Seems there is no need to change the url key
            return false;
        }

        return true;
    }

    /**
     * Check if product exists for specified SKU
     *
     * @param string $sku
     * @return bool
     */
    private function isSkuExist($sku)
    {
        $sku = strtolower($sku);
        return isset($this->_oldSku[$sku]);
    }    

    /**
     * Get existing product data for specified SKU
     *
     * @param string $sku
     * @return array
     */
    private function getExistingSku($sku)
    {
        return $this->_oldSku[strtolower($sku)];
    }
   
    /**
     * Initialize image array keys.
     *
     * @return $this
     */
    private function initImagesArrayKeys()
    {
        $this->_imagesArrayKeys = $this->imageTypeProcessor->getImageTypes();
        return $this;
    } 
    
    /**
     * Return additional data, needed to select.
     *
     * @return array
     */
    private function getOldSkuFieldsForSelect()
    {
        return ['type_id', 'attribute_set_id'];
    }

    /**
     * Adds newly created products to _oldSku
     *
     * @param array $newProducts
     * @return void
     */
    private function updateOldSku(array $newProducts)
    {
        $oldSkus = [];
        foreach ($newProducts as $info) {
            $typeId = $info['type_id'];
            $sku = strtolower($info['sku']);
            $oldSkus[$sku] = [
                'type_id' => $typeId,
                'attr_set_id' => $info['attribute_set_id'],
                $this->getProductIdentifierField() => $info[$this->getProductIdentifierField()],
                'supported_type' => isset($this->_productTypeModels[$typeId]),
                $this->getProductEntityLinkField() => $info[$this->getProductEntityLinkField()],
            ];
        }

        $this->_oldSku = array_replace($this->_oldSku, $oldSkus);
    }    
    
    /**
     * Get new SKU fields for select
     *
     * @return array
     */
    private function getNewSkuFieldsForSelect()
    {
        $fields = ['sku', $this->getProductEntityLinkField()];
        if ($this->getProductEntityLinkField() != $this->getProductIdentifierField()) {
            $fields[] = $this->getProductIdentifierField();
        }
        return $fields;
    }    
    
    /**
     * Prepare array with image states (visible or hidden from product page)
     *
     * @param array $rowData
     * @return array
     */
    private function getImagesHiddenStates($rowData)
    {
        $statesArray = [];
        $mappingArray = [
            '_media_is_disabled' => '1'
        ];

        foreach ($mappingArray as $key => $value) {
            if (isset($rowData[$key]) && strlen(trim($rowData[$key]))) {
                $items = explode($this->getMultipleValueSeparator(), $rowData[$key]);

                foreach ($items as $item) {
                    $statesArray[$item] = $value;
                }
            }
        }

        return $statesArray;
    }

    /**
     * Try to find file by it's path.
     *
     * @param string $fileName
     * @return string
     */
    private function getSystemFile($fileName)
    {
        $filePath = 'catalog' . DIRECTORY_SEPARATOR . 'product' . DIRECTORY_SEPARATOR . $fileName;
        /** @var \Magento\Framework\Filesystem\Directory\ReadInterface $read */
        $read = $this->filesystem->getDirectoryRead(DirectoryList::MEDIA);

        return $read->isExist($filePath) && $read->isReadable($filePath) ? $fileName : '';
    }

    /**
     * Initiate product reindex by product ids
     *
     * @param array $productIdsToReindex
     * @return void
     */
    private function reindexProducts($productIdsToReindex = [])
    {
        $indexer = $this->indexerRegistry->get('catalog_product_category');
        if (is_array($productIdsToReindex) && count($productIdsToReindex) > 0 && !$indexer->isScheduled()) {
            $indexer->reindexList($productIdsToReindex);
        }
    }    
    
    /**
     * Check if need to validate url key.
     *
     * @param array $rowData
     * @return bool
     */
    private function isNeedToValidateUrlKey($rowData)
    {
        return (!empty($rowData[self::URL_KEY]) || !empty($rowData[self::COL_NAME]))
            && (empty($rowData[self::COL_VISIBILITY])
                || $rowData[self::COL_VISIBILITY]
                !== (string)Visibility::getOptionArray()[Visibility::VISIBILITY_NOT_VISIBLE]);
    }

    /**
     * Prepare new SKU data
     *
     * @param string $sku
     * @return array
     */
    private function prepareNewSkuData($sku)
    {
        $data = [];
        foreach ($this->getExistingSku($sku) as $key => $value) {
            $data[$key] = $value;
        }

        $data['attr_set_code'] = $this->_attrSetIdToName[$this->getExistingSku($sku)['attr_set_id']];

        return $data;
    }

    /**
     * Parse attributes names and values string to array.
     *
     * @param array $rowData
     *
     * @return array
     */
    private function _parseAdditionalAttributes($rowData)
    {
        if (empty($rowData['additional_attributes'])) {
            return $rowData;
        }
        $rowData = array_merge($rowData, $this->getAdditionalAttributes($rowData['additional_attributes']));
        return $rowData;
    }

    /**
     * Retrieves additional attributes in format:
     * [
     *      code1 => value1,
     *      code2 => value2,
     *      ...
     *      codeN => valueN
     * ]
     *
     * @param string $additionalAttributes Attributes data that will be parsed
     * @return array
     */
    private function getAdditionalAttributes($additionalAttributes)
    {
        return empty($this->_parameters[Import::FIELDS_ENCLOSURE])
            ? $this->parseAttributesWithoutWrappedValues($additionalAttributes)
            : $this->parseAttributesWithWrappedValues($additionalAttributes);
    }

    /**
     * Parses data and returns attributes in format:
     * [
     *      code1 => value1,
     *      code2 => value2,
     *      ...
     *      codeN => valueN
     * ]
     *
     * @param string $attributesData Attributes data that will be parsed. It keeps data in format:
     *      code=value,code2=value2...,codeN=valueN
     * @return array
     */
    private function parseAttributesWithoutWrappedValues($attributesData)
    {
        $attributeNameValuePairs = explode($this->getMultipleValueSeparator(), $attributesData);
        $preparedAttributes = [];
        $code = '';
        foreach ($attributeNameValuePairs as $attributeData) {
            //process case when attribute has ImportModel::DEFAULT_GLOBAL_MULTI_VALUE_SEPARATOR inside its value
            if (strpos($attributeData, self::PAIR_NAME_VALUE_SEPARATOR) === false) {
                if (!$code) {
                    continue;
                }
                $preparedAttributes[$code] .= $this->getMultipleValueSeparator() . $attributeData;
                continue;
            }
            list($code, $value) = explode(self::PAIR_NAME_VALUE_SEPARATOR, $attributeData, 2);
            $code = mb_strtolower($code);
            $preparedAttributes[$code] = $value;
        }
        return $preparedAttributes;
    }

    /**
     * Parses data and returns attributes in format:
     * [
     *      code1 => value1,
     *      code2 => value2,
     *      ...
     *      codeN => valueN
     * ]
     * All values have unescaped data except mupliselect attributes,
     * they should be parsed in additional method - parseMultiselectValues()
     *
     * @param string $attributesData Attributes data that will be parsed. It keeps data in format:
     *      code="value",code2="value2"...,codeN="valueN"
     *  where every value is wrapped in double quotes. Double quotes as part of value should be duplicated.
     *  E.g. attribute with code 'attr_code' has value 'my"value'. This data should be stored as attr_code="my""value"
     *
     * @return array
     */
    private function parseAttributesWithWrappedValues($attributesData)
    {
        $attributes = [];
        preg_match_all(
            '~((?:[a-zA-Z0-9_])+)="((?:[^"]|""|"' . $this->getMultiLineSeparatorForRegexp() . '")+)"+~',
            $attributesData,
            $matches
        );
        foreach ($matches[1] as $i => $attributeCode) {
            $attribute = $this->retrieveAttributeByCode($attributeCode);
            $value = 'multiselect' != $attribute->getFrontendInput()
                ? str_replace('""', '"', $matches[2][$i])
                : '"' . $matches[2][$i] . '"';
            $attributes[mb_strtolower($attributeCode)] = $value;
        }
        return $attributes;
    }

    /**
     * Retrieves escaped PSEUDO_MULTI_LINE_SEPARATOR if it is metacharacter for regular expression
     *
     * @return string
     */
    private function getMultiLineSeparatorForRegexp()
    {
        if (!$this->multiLineSeparatorForRegexp) {
            $this->multiLineSeparatorForRegexp = in_array(self::PSEUDO_MULTI_LINE_SEPARATOR, str_split('[\^$.|?*+(){}'))
                ? '\\' . self::PSEUDO_MULTI_LINE_SEPARATOR
                : self::PSEUDO_MULTI_LINE_SEPARATOR;
        }
        return $this->multiLineSeparatorForRegexp;
    }

    /**
     * Set values in use_config_ fields.
     *
     * @param array $rowData
     *
     * @return array
     */
    private function _setStockUseConfigFieldsValues($rowData)
    {
        $useConfigFields = [];
        foreach ($rowData as $key => $value) {
            $useConfigName = $key === StockItemInterface::ENABLE_QTY_INCREMENTS
                ? StockItemInterface::USE_CONFIG_ENABLE_QTY_INC
                : self::INVENTORY_USE_CONFIG_PREFIX . $key;

            if (isset($this->defaultStockData[$key])
                && isset($this->defaultStockData[$useConfigName])
                && !empty($value)
                && empty($rowData[$useConfigName])
            ) {
                $useConfigFields[$useConfigName] = ($value == self::INVENTORY_USE_CONFIG) ? 1 : 0;
            }
        }
        $rowData = array_merge($rowData, $useConfigFields);
        return $rowData;
    }

    /**
     * Custom fields mapping for changed purposes of fields and field names.
     *
     * @param array $rowData
     *
     * @return array
     */
    private function _customFieldsMapping($rowData)
    {
        foreach ($this->_fieldsMap as $systemFieldName => $fileFieldName) {
            if (array_key_exists($fileFieldName, $rowData)) {
                $rowData[$systemFieldName] = $rowData[$fileFieldName];
            }
        }

        $rowData = $this->_parseAdditionalAttributes($rowData);

        $rowData = $this->_setStockUseConfigFieldsValues($rowData);
        if (array_key_exists('status', $rowData)
            && $rowData['status'] != \Magento\Catalog\Model\Product\Attribute\Source\Status::STATUS_ENABLED
        ) {
            if ($rowData['status'] == 'yes') {
                $rowData['status'] = \Magento\Catalog\Model\Product\Attribute\Source\Status::STATUS_ENABLED;
            } elseif (!empty($rowData['status']) || $this->getRowScope($rowData) == self::SCOPE_DEFAULT) {
                $rowData['status'] = \Magento\Catalog\Model\Product\Attribute\Source\Status::STATUS_DISABLED;
            }
        }
        return $rowData;
    }    
    
   /**
     * Get product entity identifier field
     *
     * @return string
     */
    private function getProductIdentifierField()
    {
        if (!$this->productEntityIdentifierField) {
            $this->productEntityIdentifierField = $this->getMetadataPool()
                ->getMetadata(\Magento\Catalog\Api\Data\ProductInterface::class)
                ->getIdentifierField();
        }
        return $this->productEntityIdentifierField;
    }

    /**
     * Update media gallery labels
     *
     * @param array $labels
     * @return void
     */
    private function updateMediaGalleryLabels(array $labels)
    {
        if (!empty($labels)) {
            $this->mediaProcessor->updateMediaGalleryLabels($labels);
        }
    }

    /**
     * Update 'disabled' field for media gallery entity
     *
     * @param array $images
     * @return $this
     */
    private function updateMediaGalleryVisibility(array $images)
    {
        if (!empty($images)) {
            $this->mediaProcessor->updateMediaGalleryVisibility($images);
        }

        return $this;
    }

    /**
     * Parse values from multiple attributes fields
     *
     * @param string $labelRow
     * @return array
     */
    private function parseMultipleValues($labelRow)
    {
        return $this->parseMultiselectValues(
            $labelRow,
            $this->getMultipleValueSeparator()
        );
    }    
    
    /**
     * Format row data to DB compatible values.
     *
     * @param array $rowData
     * @return array
     */
    private function formatStockDataForRow(array $rowData): array
    {
        $sku = $rowData[self::COL_SKU];
        $row['product_id'] = $this->skuProcessor->getNewSku($sku)['entity_id'];
        $row['website_id'] = $this->stockConfiguration->getDefaultScopeId();
        $row['stock_id'] = $this->stockRegistry->getStock($row['website_id'])->getStockId();

        $stockItemDo = $this->stockRegistry->getStockItem($row['product_id'], $row['website_id']);
        $existStockData = $stockItemDo->getData();

        $row = array_merge(
            $this->defaultStockData,
            array_intersect_key($existStockData, $this->defaultStockData),
            array_intersect_key($rowData, $this->defaultStockData),
            $row
        );

        if ($this->stockConfiguration->isQty($this->skuProcessor->getNewSku($sku)['type_id'])) {
            $stockItemDo->setData($row);
            $row['is_in_stock'] = $row['is_in_stock'] ?? $this->stockStateProvider->verifyStock($stockItemDo);
            if ($this->stockStateProvider->verifyNotification($stockItemDo)) {
                $date = $this->dateTimeFactory->create('now', new \DateTimeZone('UTC'));
                $row['low_stock_date'] = $date->format(DateTime::DATETIME_PHP_FORMAT);
            }
            $row['stock_status_changed_auto'] = (int)!$this->stockStateProvider->verifyStock($stockItemDo);
        } else {
            $row['qty'] = 0;
        }

        return $row;
    }

    /**
     * Retrieve product by sku.
     *
     * @param string $sku
     * @return \Magento\Catalog\Api\Data\ProductInterface|null
     */
    private function retrieveProductBySku($sku)
    {
        try {
            $product = $this->productRepository->get($sku);
        } catch (NoSuchEntityException $e) {
            return null;
        }
        return $product;
    }

    /**
     * Add row as skipped
     *
     * @param int $rowNum
     * @param string $errorCode Error code or simply column name
     * @param string $errorLevel error level
     * @param string|null $colName optional column name
     * @return $this
     */
    private function skipRow(
        $rowNum,
        string $errorCode,
        string $errorLevel = ProcessingError::ERROR_LEVEL_NOT_CRITICAL,
        $colName = null
    ): self {
        $this->addRowError($errorCode, $rowNum, $colName, null, $errorLevel);
        $this->getErrorAggregator()
            ->addRowToSkip($rowNum);
        return $this;
    }

    /**
     * Returns errorLevel for validation
     *
     * @param string $sku
     * @return string
     */
    private function getValidationErrorLevel($sku): string
    {
        return (!$this->isSkuExist($sku) && Import::BEHAVIOR_REPLACE !== $this->getBehavior())
            ? ProcessingError::ERROR_LEVEL_CRITICAL
            : ProcessingError::ERROR_LEVEL_NOT_CRITICAL;
    }    
}
