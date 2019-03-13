<?php

namespace RocketWeb\ShoppingFeeds\Test\Unit\Model;

/**
 * Class ModelFramework
 */
class ModelFramework extends \PHPUnit_Framework_TestCase
{
    /**** A ****/
    /**
     * @var \RocketWeb\ShoppingFeeds\Model\Product\Adapter\Type\Simple|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $adapterMock;

    /**
     * @var \RocketWeb\ShoppingFeeds\Model\Product\Adapter\Type\Configurable|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $parentAdapterMock;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject|\RocketWeb\ShoppingFeeds\Model\Product\Adapter\AdapterFactory
     */
    protected $adapterFactoryMock;


    /**** B ****/
    /**
     * @var \PHPUnit_Framework_MockObject_MockObject|\RocketWeb\ShoppingFeeds\Model\Generator\Batch
     */
    protected $batchMock;

    /**** C ****/

    /**
     * @var \RocketWeb\ShoppingFeeds\Model\Generator\Cache|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $cacheMock;

    /**
     * @var \Magento\Catalog\Helper\Data|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $catalogHelperMock;

    /**
     * @var \Magento\Catalog\Model\Product\Type\Price|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $catalogProductPriceMock;

    /**
     * @var \Magento\CatalogRule\Model\Rule\Product\Price|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $catalogRuleMock;

    /**
     * @var \Magento\CatalogRule\Model\ResourceModel\Rule\Product\Price\CollectionFactory|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $catalogRuleCollectionFactoryMock;

    /**
     * @var \Magento\CatalogRule\Model\ResourceModel\Rule\Product\Price\Collection
     */
    protected $catalogRuleCollectionMock;

    /**
     * @var \Magento\Catalog\Model\CategoryFactory|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $categoryFactoryMock;

    /**
     * @var \RocketWeb\ShoppingFeeds\Model\Product\Category\CollectionProvider|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $categoryCollectionProvider;

    /**
     * @var \Magento\Catalog\Model\ResourceModel\Category\CollectionFactory|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $categoryCollectionFactoryMock;

    /**
     * @var \RocketWeb\ShoppingFeeds\Model\Feed\Config|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $configMock;

    /**
     * @var \RocketWeb\ShoppingFeeds\Model\Feed\ConfigFactory|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $configFactoryMock;

    /**
     * @var \RocketWeb\ShoppingFeeds\Model\ResourceModel\Feed\Config\Collection|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $configCollectionMock;

    /**
     * @var \RocketWeb\ShoppingFeeds\Model\ResourceModel\Feed\Config\CollectionFactory|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $configCollectionFactoryMock;

    /**
     * @var \Magento\Framework\Model\Context|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $contextMock;


    /**** D ****/
    /**
     * @var \PHPUnit_Framework_MockObject_MockObject|\Magento\Framework\Stdlib\DateTime\Timezone
     */
    protected $dateTimeMock;

    /**
     * @var \Magento\Framework\Stdlib\DateTime|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $dateMock;


    /**** F ****/
    /**
     * @var \RocketWeb\ShoppingFeeds\Model\Feed
     */
    protected $feed;

    /**
     * @var \RocketWeb\ShoppingFeeds\Model\Feed|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $feedMock;

    /**
     * @var \RocketWeb\ShoppingFeeds\Model\FeedTypes\Config|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $feedTypesConfigMock;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject|\Magento\Framework\Filesystem\Driver\File
     */
    protected $fileDriverMock;

    /**
     * @var \RocketWeb\ShoppingFeeds\Model\Product\Filter|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $filterMock;

    /**** H ****/
    /**
     * @var \RocketWeb\ShoppingFeeds\Model\Product\Helper|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $helperMock;

    /**** I ****/
    /**
     * @var \PHPUnit_Framework_MockObject_MockObject|\Magento\Framework\Model\ResourceModel\Iterator
     */
    protected $iteratorMock;


    /**** L ****/
    /**
     * @var \PHPUnit_Framework_MockObject_MockObject|\RocketWeb\ShoppingFeeds\Model\Logger
     */
    protected $loggerMock;


    /**** M ****/
    /**
     * @var \RocketWeb\ShoppingFeeds\Model\Product\Mapper\MapperFactory|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $mapperFactoryMock;

    /**
     * @var \RocketWeb\ShoppingFeeds\Model\Product\Mapper\MapperAbstract|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $mapperMock;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject|\RocketWeb\ShoppingFeeds\Model\FeedFactory
     */
    protected $memoryMock;


    /**** O ****/
    /**
     * @var \RocketWeb\ShoppingFeeds\Model\Product\OptionFactory|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $optionFactoryMock;

    /**
     * @var \Magento\Catalog\Model\Product\Option|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $optionMock;


    /**** P ****/
    /**
     * @var \Magento\Directory\Model\PriceCurrency|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $priceCurrencyMock;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject|\Magento\Catalog\Model\Product
     */
    protected $productMock;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject|\Magento\Catalog\Model\ProductFactory
     */
    protected $productFactoryMock;

    /**
     * @var \Magento\Catalog\Api\ProductRepositoryInterface
     */
    protected $productRepositoryMock;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject|\Magento\Catalog\Model\ResourceModel\Product\CollectionFactory
     */
    protected $productCollectionProviderMock;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject|\Magento\Catalog\Model\ResourceModel\Product\Collection
     */
    protected $productCollectionMock;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject|\RocketWeb\ShoppingFeeds\Model\ResourceModel\Generator\Process\Collection
     */
    protected $processCollectionMock;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject|\RocketWeb\ShoppingFeeds\Model\ResourceModel\Generator\Process\CollectionFactory
     */
    protected $processCollectionFactoryMock;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject|\RocketWeb\ShoppingFeeds\Model\Generator\Process
     */
    protected $processMock;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject|\RocketWeb\ShoppingFeeds\Model\Generator\ProcessFactory
     */
    protected $processFactoryMock;

    /**** R ****/

    /**
     * @var \Magento\Framework\Registry|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $registryMock;

    /**
     * @var \Magento\Framework\Model\ResourceModel\Db\AbstractDb|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $resource;

    /**
     * @var \Magento\Framework\Data\Collection\AbstractDb|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $resourceCollection;

    /**** Q ****/
    /**
     * @var \PHPUnit_Framework_MockObject_MockObject|\RocketWeb\ShoppingFeeds\Model\Generator\Queue
     */
    protected $queueMock;

    /**** S ****/
    /**
     * @var \Magento\Framework\App\Config\ScopeConfigInterface|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $scopeConfigMock;

    /**
     * @var \RocketWeb\ShoppingFeeds\Model\Feed\Schedule|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $scheduleMock;

    /**
     * @var \RocketWeb\ShoppingFeeds\Model\Feed\ScheduleFactory|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $scheduleFactoryMock;

    /**
     * @var \RocketWeb\ShoppingFeeds\Model\ResourceModel\Feed\Schedule\Collection|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $scheduleCollectionMock;

    /**
     * @var \RocketWeb\ShoppingFeeds\Model\ResourceModel\Feed\Schedule\CollectionFactory|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $scheduleCollectionFactoryMock;

    /**
     * @var \RocketWeb\ShoppingFeeds\Model\Generator\Cache\ShippingProvider|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $shippingProviderMock;

    /**
     * @var \Magento\CatalogInventory\Model\Stock\Status|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $statusMock;

    /**
     * @var \Magento\CatalogInventory\Model\StockState|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $stockStateMock;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject|\Magento\CatalogInventory\Helper\Stock
     */
    protected $stockMock;

    /**
     * @var \Magento\Store\Model\Store|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $storeMock;


    /**** T ****/
    /**
     * @var \Magento\Tax\Helper\Data|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $taxHelperMock;


    /**** U ****/
    /**
     * @var \PHPUnit_Framework_MockObject_MockObject|\RocketWeb\ShoppingFeeds\Model\ResourceModel\Feed\Upload\CollectionFactory
     */
    protected $uploadCollectionFactoryMock;

    /**** W ****/
    /**
     * @var \Magento\Weee\Helper\Data|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $weeHelperMock;

    /**
     * @var \Magento\Framework\TestFramework\Unit\Helper\ObjectManager
     */
    protected $objectManagerHelper;

    protected function setUp()
    {
        $this->adapterMock = $this->getModelMock(
            'RocketWeb\ShoppingFeeds\Model\Product\Adapter\Type\Simple',
            ['getProduct', 'getData', 'getFilter', 'getFeed', 'getMapAttribute', 'getAttributeValue',
                'getMapValue', 'mapEmptyValues', 'getTimezone', 'getStore', 'getPrices',
                'getOptionProcessor', 'getOptions', 'hasParentAdapter', 'getParentAdapter',
                'getInventoryCount', 'getSalePriceEffectiveDates', 'hasSpecialPrice', 'getUrlOptions',
                'isSkipped', 'isDuplicate', 'map', 'setTestMode']
        );
        $this->expectSelf($this->adapterMock, 'getParentAdapter');

        $this->adapterFactoryMock = $this->getModelMock(
            'RocketWeb\ShoppingFeeds\Model\Product\Adapter\AdapterFactory',
            ['create', 'map', 'getSkipProduct', 'getSkipMessage']
        );

        $this->batchMock = $this->getModelMock(
            'RocketWeb\ShoppingFeeds\Model\Generator\Batch',
            ['getOffset', 'setOffset', 'isEnabled']
        );

        $this->cacheMock = $this->getModelMock(
            'RocketWeb\ShoppingFeeds\Model\Generator\Cache', ['getCache', 'setCache']);

        $this->catalogHelperMock = $this->getModelMock(
            'RocketWeb\ShoppingFeeds\Model\Product\Helper\Catalog', ['getTaxPrice']
        );

        $this->catalogProductPriceMock = $this->getModelMock('Magento\Catalog\Model\Product\Type\Price',
            ['calculatePrice']
        );

        $this->catalogRuleCollectionFactoryMock = $this->getModelMock(
            'Magento\CatalogRule\Model\ResourceModel\Rule\Product\Price\CollectionFactory',
            ['create']
        );
        $this->catalogRuleCollectionMock = $this->getModelMock(
            'Magento\CatalogRule\Model\ResourceModel\Rule\Product\Price\Collection',
            ['addFieldToFilter', 'addFieldToSelect', 'getFirstItem']
        );
        $this->catalogRuleMock = $this->getModelMock(
            'Magento\CatalogRule\Model\Rule\Product\Price', ['getData']);
        $this->expectReturn($this->catalogRuleCollectionFactoryMock, 'create', $this->catalogRuleCollectionMock);
        $this->expectReturn($this->catalogRuleCollectionMock, 'getFirstItem', $this->catalogRuleMock);
        $this->expectSelf($this->catalogRuleCollectionMock, ['addFieldToFilter', 'addFieldToSelect']);


        $this->categoryFactoryMock = $this->getModelMock(
            'Magento\Catalog\Model\CategoryFactory', ['create','load', 'setStoreId', 'getName']);

        $this->categoryCollectionProvider = $this->getModelMock(
            'RocketWeb\ShoppingFeeds\Model\Product\Category\CollectionProvider',
            ['getCategories', 'addFieldToFilter', 'exportToArray']);

        $this->categoryCollectionFactoryMock = $this->getModelMock(
            'Magento\Catalog\Model\ResourceModel\Category\CollectionFactory', ['create']
        );

        $this->configMock = $this->getModelMock(
            'RocketWeb\ShoppingFeeds\Model\Feed\Config',
            ['getData', 'setData', 'save']
        );

        $this->configFactoryMock = $this->getModelMock(
            'RocketWeb\ShoppingFeeds\Model\Feed\ConfigFactory',
            ['create']
        );
        $this->expectReturn($this->configFactoryMock, 'create', $this->configMock);

        $this->configCollectionFactoryMock = $this->getModelMock(
            'RocketWeb\ShoppingFeeds\Model\ResourceModel\Feed\Config\CollectionFactory',
            ['create']
        );

        $this->dateMock = $this->getModelMock('Magento\Framework\Stdlib\DateTime', ['date', 'isEmptyDate']);

        $this->dateTimeMock = $this->getModelMock(
            'Magento\Framework\Stdlib\DateTime\Timezone',
            ['date', 'getTimestamp', 'formatDate', 'formatDateTime']
        );

        $this->feedMock = $this->getModelMock(
            'RocketWeb\ShoppingFeeds\Model\Feed',
            ['getId', 'getConfig', 'getFile', 'getColumnsMap', 'getData', 'hasData', 'setData', 'saveMessages', 'saveStatus',
                'getStore', 'getUploadCollection']
        );

        $this->feedTypesConfigMock = $this->getModelMock(
            'RocketWeb\ShoppingFeeds\Model\FeedTypes\Config',
            ['getFeed', 'isAllowedDirective', 'getDirective']
        );

        $this->fileDriverMock = $this->getModelMock(
            'Magento\Framework\Filesystem\Driver\File',
            ['fileOpen', 'fileWrite', 'fileClose', 'rename', 'isExists', 'createDirectory']
        );

        $this->filterMock = $this->getModelMock('RocketWeb\ShoppingFeeds\Model\Product\Filter',
            ['findAndReplace', 'cleanField']
        );
        $this->expectSelf($this->filterMock, ['findAndReplace']);
        $this->expectAdvencedReturn($this->filterMock, 'cleanField', $this->returnArgument(0));
        $this->expectReturn($this->adapterMock, 'getFilter', $this->filterMock);

        $this->helperMock = $this->getModelMock('RocketWeb\ShoppingFeeds\Model\Product\Helper',
            ['getQuantityIcrements', 'hasMsrp']
        );

        $this->iteratorMock = $this->getModelMock('Magento\Framework\Model\ResourceModel\Iterator',
            ['walk']
        );

        $this->loggerMock = $this->getModelMock(
            'RocketWeb\ShoppingFeeds\Model\Logger',
            ['info', 'warning', 'debug', 'addHandler']
        );

        $this->priceCurrencyMock = $this->getModelMock(
            'Magento\Directory\Model\PriceCurrency',
            ['getCurrency']
        );

        $this->memoryMock = $this->getModelMock(
            'RocketWeb\ShoppingFeeds\Model\Generator\Memory',
            ['isCloseToPhpLimit']
        );

        $this->mapperFactoryMock = $this->getModelMock('RocketWeb\ShoppingFeeds\Model\Product\Mapper\MapperFactory',
            ['create', 'getMapperData']
        );

        $this->mapperMock = $this->getModelMock('RocketWeb\ShoppingFeeds\Model\Product\Mapper\MapperAbstract',
            ['map', 'format', 'filter', 'addAdapter', 'popAdapter']
        );
        $this->expectAdvencedReturn($this->mapperMock, 'format', $this->returnArgument(0));

        $this->optionFactoryMock = $this->getModelMock('RocketWeb\ShoppingFeeds\Model\Product\OptionFactory',
            ['create', 'process']
        );

        $this->optionMock = $this->getModelMock('Magento\Catalog\Model\Product\Option',
            ['getTitle', 'getId', 'getValues', 'getGroupByType']);

        $this->expectSelf($this->optionFactoryMock, 'create');
        $this->expectAdvencedReturn($this->optionFactoryMock, 'process', $this->returnArgument(0));

        $this->productMock = $this->getModelMock('Magento\Catalog\Model\Product',
            ['getId', 'getMediaGalleryImages', 'hasData', 'getData', 'toFlatArray', 'isInStock',
                'getCategoryIds', 'getProductUrl', 'getStore', 'getCategoryCollection', 'getSku',
                'getResource', 'getPrice', 'getSpecialPrice', 'getFinalPrice', 'getSpecialFromDate',
                'getSpecialToDate', 'getQty', 'getTypeInstance', 'getUsedProductCollection',
                'addAttributeToSelect', 'getConfigurableAttributes', 'getOptionsIds', 'getSelectionsCollection',
                'getPriceModel', 'setData', 'setSpecialPrice', 'setFinalPrice', 'getTotalPrices',
                'getAssociatedProductCollection', 'addFilterByRequiredOptions', 'setPositionOrder',
                'addStoreFilter', 'getOptions']
        );
        $this->expectSelf($this->productMock, ['setData', 'setSpecialPrice', 'setFinalPrice', 'getPriceModel']);

        $this->productFactoryMock = $this->getModelMock('Magento\Catalog\Model\ProductFactory',
            ['create', 'load']
        );

        $this->productRepositoryMock = $this->getModelMock('Magento\Catalog\Api\ProductRepositoryInterface',
            ['save', 'get', 'getById', 'delete', 'deleteById', 'getList']
        );
        $this->expectReturn($this->productRepositoryMock, 'getById', $this->productMock);

        $this->productCollectionProviderMock = $this->getModelMock(
            'RocketWeb\ShoppingFeeds\Model\Product\CollectionProvider',
            ['create', 'setStoreId', 'addStoreFilter', 'addAttributeToFilter',
                'getSelect', 'group', 'limit', 'getCollection', 'reset', 'getSize']
        );

        $this->productCollectionMock = $this->getModelMock(
            'Magento\Catalog\Model\ResourceModel\Product\Collection',
            ['setStoreId', 'addStoreFilter', 'addAttributeToFilter', 'getSelect', 'group', 'limit',
                'getPart', 'setPart', 'joinInner', 'getConnection', 'quoteInto', 'where', 'getTable',
                'addPriceData', 'reset', 'getSize']
        );

        $this->processMock = $this->getModelMock(
            'RocketWeb\ShoppingFeeds\Model\Generator\Process',
            ['load', 'getId', 'setParentItemId', 'getStatus', 'getParentItemId',
            'setFeedId', 'setItemId', 'setStatus', 'save']
        );
        $this->expectSelf($this->processMock, [
            'load', 'setParentItemId',
            'setFeedId', 'setItemId', 'setStatus', 'save'
        ]);

        $this->processFactoryMock = $this->getModelMock(
            'RocketWeb\ShoppingFeeds\Model\Generator\ProcessFactory', ['create']
        );
        $this->expectReturn($this->processFactoryMock, 'create', $this->processMock);

        $this->processCollectionMock = $this->getModelMock(
            'RocketWeb\ShoppingFeeds\Model\ResourceModel\Generator\Process\Collection',
            ['load', 'truncate', 'setFeedFilter', 'setProductFilter']
        );
        $this->expectSelf($this->processCollectionMock, ['truncate', 'setFeedFilter', 'setProductFilter']);

        $this->processCollectionFactoryMock = $this->getModelMock(
            'RocketWeb\ShoppingFeeds\Model\ResourceModel\Generator\Process\CollectionFactory',
            ['create', 'truncate']
        );
        $this->expectSelf($this->processCollectionFactoryMock, ['truncate']);
        $this->expectReturn($this->processCollectionFactoryMock, 'create', $this->processCollectionMock);

        $this->queueMock = $this->getModelMock(
            'RocketWeb\ShoppingFeeds\Model\Generator\Queue',
            ['getBatch', 'setBatch', 'setData', 'save', 'getId', 'delete']
        );

        $this->scopeConfigMock = $this->getModelMock(
            'Magento\Framework\App\Config',
            ['getValue', 'isSetFlag']
        );

        /** Schedule Mocks */
        $this->scheduleMock = $this->getModelMock(
            'RocketWeb\ShoppingFeeds\Model\Feed\Schedule',
            ['getId', 'getData', 'getFormattedSchedule', 'load', 'save', 'delete']
        );

        $this->scheduleFactoryMock = $this->getModelMock(
            'RocketWeb\ShoppingFeeds\Model\Feed\ScheduleFactory',
            ['create']
        );
        $this->expectReturn($this->scheduleFactoryMock, 'create', $this->scheduleMock);

        $this->scheduleCollectionFactoryMock = $this->getModelMock(
            'RocketWeb\ShoppingFeeds\Model\ResourceModel\Feed\Schedule\CollectionFactory',
            ['create']
        );

        $this->shippingProviderMock = $this->getModelMock(
            'RocketWeb\ShoppingFeeds\Model\Generator\Cache\ShippingProvider',
            ['clearCache', 'prepareCache', 'getCache', 'setCache', 'getShipping']
        );

        $this->uploadCollectionFactoryMock = $this->getModelMock(
            'RocketWeb\ShoppingFeeds\Model\ResourceModel\Feed\Upload\CollectionFactory',
            ['create', 'setFeedFilter']
        );
        $this->expectSelf($this->uploadCollectionFactoryMock, ['create', 'setFeedFilter']);

        $this->taxHelperMock = $this->getModelMock('Magento\Tax\Helper\Data', ['getConfig']);

        $this->weeHelperMock = $this->getModelMock('Magento\Weee\Helper\Data',
            ['getAmountExclTax', 'isTaxable', 'getProductWeeeAttributesForRenderer']
        );

        /**** SYSTEM MOCKS ****/
        $eventManager = $this->getMock('Magento\Framework\Event\ManagerInterface', [], [], '', false);
        $eventManager->expects($this->any())
            ->method('dispatch')
            ->will($this->returnSelf());

        $this->contextMock = $this->getMock('Magento\Framework\Model\Context', [], [], '', false);
        $this->expectReturn($this->contextMock, 'getEventDispatcher', $eventManager);

        $this->registryMock = $this->getMock('Magento\Framework\Registry');
        $this->resource = $this->getMock('Magento\Review\Model\ResourceModel\Review', [], [], '', false);
        $this->resourceCollection = $this->getMock('Magento\Framework\Data\Collection\AbstractDb', [], [], '', false);
        $this->storeMock = $this->getModelMock('Magento\Store\Model\Store',
            ['getStoreId', 'load', 'getCode', 'getCurrentCurrency',
                'getBaseUrl', 'getBaseCurrency', 'convert']
        );
        $this->statusMock = $this->getModelMock('Magento\CatalogInventory\Model\Stock\Status',
            ['load', 'getStockStatus', 'getStockQty']
        );
        $this->stockStateMock = $this->getModelMock('Magento\CatalogInventory\Model\StockState', ['getStockQty']);
        $this->expectSelf($this->statusMock, 'load');

        /** @var \DateTime $dateTimeMock */
        $datetimeMock = $this->getMock('\DateTime', [], [], '', false);
        $datetimeMock->expects($this->any())
            ->method('format')
            ->will($this->returnValue(date('Y-m-d H:i:s')));

        $this->localeDateMock = $this->getMockBuilder('\Magento\Framework\Stdlib\DateTime\TimezoneInterface')->getMockForAbstractClass();
        $this->localeDateMock->expects($this->any())
            ->method('date')
            ->will($this->returnValue($datetimeMock));
    }
    
    

    /**
     * @param string $className
     * @param array $methods
     * @return \PHPUnit_Framework_MockObject_MockObject
     */
    public function getModelMock($className, array $methods = [])
    {
        return $this->getMockBuilder($className)
            ->disableOriginalConstructor()
            ->setMethods($methods)
            ->getMock();
    }

    /**
     * Advanced return expection method, passing Stub object
     * @param \PHPUnit_Framework_MockObject_MockObject $mock
     * @param string $method
     * @param \PHPUnit_Framework_MockObject_Stub $return
     */
    public function expectAdvencedReturn(&$mock, $method, $return)
    {
        $mock->expects($this->any())
            ->method($method)
            ->will($return);
    }

    /**
     * @param \PHPUnit_Framework_MockObject_MockObject $mock
     * @param string $method
     * @param mixed $return
     */
    public function expectReturn(&$mock, $method, $return)
    {
        $mock->expects($this->any())
            ->method($method)
            ->will($this->returnValue($return));
    }

    /**
     * @param \PHPUnit_Framework_MockObject_MockObject $mock
     * @param string $method
     */
    public function expectSelf(&$mock, $methods)
    {
        if (!is_array($methods)) {
            $methods = [$methods];
        }

        foreach ($methods as $method) {
            $mock->expects($this->any())
                ->method($method)
                ->will($this->returnSelf());
        }
    }
}