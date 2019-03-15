<?php
/**
 * RocketWeb
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 *
 * @category  RocketWeb
 * @package   RocketWeb_ShoppingFeeds
 * @copyright Copyright (c) 2016 RocketWeb (http://rocketweb.com)
 * @license   http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 * @author    Rocket Web Inc.
 */


namespace RocketWeb\ShoppingFeeds\Test\Unit\Model;

use Magento\Framework\TestFramework\Unit\Helper\ObjectManager as ObjectManagerHelper;

/**
 * Class GeneratorTest
 */
class GeneratorTest extends ModelFramework
{
    /**
     * @var \RocketWeb\ShoppingFeeds\Model\Generator
     */
    protected $model;
    
    /**
     * @var array
     */
    protected $objectData = [];

    public function setUp()
    {
        global $setGlobalMockFunctionLimit;
        parent::setUp();

        $this->expectSelf($this->dateTimeMock, 'date');
        $this->expectSelf($this->feedMock, ['saveMessages', 'saveStatus']);

        $this->objectData = [
            'productRepositoryInterface'    => $this->productRepositoryMock,
            'fileDriver'        => $this->fileDriverMock,
            'iterator'          => $this->iteratorMock,
            'dateTime'          => $this->dateTimeMock,
            'collectionProvider'=> $this->productCollectionProviderMock,
            'logger'            => $this->loggerMock,
            'adapterFactory'    => $this->adapterFactoryMock,
            'batch'             => $this->batchMock,
            'memory'            => $this->memoryMock,
            'processCollectionFactory' => $this->processCollectionFactoryMock,
            'queue'             => $this->queueMock,
            'feed'              => $this->feedMock
        ];

        $setGlobalMockFunctionLimit = false;
    }

    protected function createModel()
    {
        $this->objectManagerHelper = new ObjectManagerHelper($this);
        $this->model = $this->objectManagerHelper->getObject(
            'RocketWeb\ShoppingFeeds\Model\Generator',
            $this->objectData
        );
    }

    public function testGetTotalItems()
    {
        $this->expectReturn($this->dateTimeMock, 'timestamp', 1000000);

        $this->expectSelf($this->productCollectionMock, ['setStoreId', 'addStoreFilter', 'getSelect', 'getConnection']);
        $this->expectReturn($this->productCollectionMock, 'getPart', ['cat_product' => []]);
        $this->expectReturn($this->productCollectionMock, 'getSize', 5);

        $this->expectReturn($this->productCollectionProviderMock, 'getCollection', $this->productCollectionMock);

        $this->expectReturn($this->feedMock, 'getId', 1);
        
        $this->feedMock->expects($this->any())
            ->method('getConfig')
            ->will($this->returnCallback(function($key, $default = '') {
                switch($key) {
                    case 'filters_product_types':
                        return ['simple','configurable','bundle','virtual','downloadable'];
                    case 'filters_add_out_of_stock':
                        return false;
                    case 'categories_include_all_products':
                        return true;
                    case 'filters_attribute_sets':
                        return ['', 'attribute_set'];
                    case 'categories_provider_taxonomy_by_category':
                        return [['category' => 5, 'disabled' => true]];
                    default:
                        break;
                }
                return $default;
            }));

        $this->createModel();
        $this->model->setTestSku('testSku');

        $this->assertEquals(5, $this->model->getTotalItems());
    }

    public function testGetCollection()
    {
        $this->expectReturn($this->dateTimeMock, 'timestamp', 1000000);

        $this->expectSelf($this->productCollectionMock, ['setStoreId', 'addStoreFilter', 'getSelect', 'getConnection']);
        $this->expectReturn($this->productCollectionMock, 'getPart', ['cat_product' => []]);
        $this->expectReturn($this->productCollectionMock, 'getSize', 5);

        $this->expectReturn($this->productCollectionProviderMock, 'getCollection', $this->productCollectionMock);
        $this->expectReturn($this->batchMock, 'isEnabled', true);
        $this->expectReturn($this->queueMock, 'getBatch', $this->batchMock);

        $this->createModel();
        $this->model->setTestSku('testSku');

        $this->assertInstanceOf('Magento\Catalog\Model\ResourceModel\Product\Collection', $this->model->getCollection());
    }

    public function testProcessProductCallbackSkippedPhpLimit()
    {
        $args = [
            'row' => [
                'type_id' => 'simple',
                'entity_id' => 1,
                'sku' => 'fakeSku'
            ]
        ];

        $this->setProcessProductCallbackMocks();
        $this->expectReturn($this->queueMock, 'isEnabled', false);

        $this->expectReturn($this->dateTimeMock, 'getTimestamp', 1000);
        $this->expectReturn($this->dateTimeMock, 'formatDate', '01-01-2010');
        $this->expectReturn($this->memoryMock, 'isCloseToPhpLimit', true);

        $this->createModel();
        $this->model->setData('total_items', 100);

        $this->setExpectedException('Exception');

        $this->model->processProductCallback($args);
    }


    public function testProcessProductCallbackSkipped()
    {
        $args = [
            'row' => [
                'type_id' => 'simple',
                'entity_id' => 1,
                'sku' => 'fakeSku'
            ]
        ];

        $this->setProcessProductCallbackMocks();
        $this->expectReturn($this->dateTimeMock, 'formatDateTime', '01-01-2010');

        $this->createModel();
        $this->model->setData('total_items', 100);

        $this->assertEquals(null, $this->model->processProductCallback($args));
    }

    protected function setProcessProductCallbackMocks()
    {
        $this->feedMock->expects($this->any())
            ->method('getConfig')
            ->will($this->onConsecutiveCalls(['simple'], 'path'));
        $this->expectReturn($this->feedMock, 'getId', 1);

        $this->expectSelf($this->productFactoryMock, 'create');
        $this->expectReturn($this->productFactoryMock, 'load', $this->productMock);

        $this->expectReturn($this->adapterFactoryMock, 'create', $this->adapterMock);
        $this->expectReturn($this->adapterMock, 'isSkipped', false);
        $this->expectReturn($this->adapterMock, 'map', []);
        $this->expectReturn($this->adapterMock, 'getSkipProduct', true);
        $this->expectReturn($this->adapterMock, 'getSkipMessage', 'Skip message');

        $this->expectReturn($this->queueMock, 'getBatch', $this->batchMock);
        $this->expectReturn($this->queueMock, 'getId', 1);
        $this->expectSelf($this->queueMock, ['setBatch', 'setData', 'save']);
    }

    public function testProcessProductCallback()
    {
        $args = [
            'row' => [
                'type_id' => 'simple',
                'entity_id' => 1,
                'sku' => 'fakeSku'
            ]
        ];

        $this->feedMock->expects($this->any())
            ->method('getConfig')
            ->will($this->onConsecutiveCalls(['simple'], 'path'));
        $this->expectReturn($this->feedMock, 'getId', 1);
        $this->expectReturn($this->feedMock, 'getData', [
            'cell_enclose' => '"',
            'cell_enclose_escape' => '\"'
        ]);
        $this->expectReturn($this->feedMock, 'getColumnsMap', [
            ['column' => 'columnName'],
            ['column' => 'columnEmptyName']
        ]);

        $this->expectSelf($this->productFactoryMock, 'create');
        $this->expectReturn($this->productFactoryMock, 'load', $this->productMock);

        $this->expectReturn($this->adapterFactoryMock, 'create', $this->adapterMock);
        $this->expectReturn($this->adapterMock, 'isSkipped', false);
        $this->expectReturn($this->adapterMock, 'map', [['columnName' => 'columnValue']]);
        $this->expectReturn($this->adapterMock, 'getSkipProduct', false);

        $this->expectReturn($this->queueMock, 'getBatch', $this->batchMock);
        $this->expectSelf($this->queueMock, ['setBatch', 'setData', 'save']);

        $this->createModel();
        $this->model->setData('total_items', 100);

        $this->assertEquals(null, $this->model->processProductCallback($args));
    }

    public function testProcessProductCallbackAdapterError()
    {
        $args = [
            'row' => [
                'type_id' => 'simple',
                'entity_id' => 1,
                'sku' => 'fakeSku'
            ]
        ];

        $this->feedMock->expects($this->any())
            ->method('getConfig')
            ->will($this->onConsecutiveCalls(['simple'], 'path'));
        $this->expectReturn($this->feedMock, 'getId', 1);

        $this->expectReturn($this->productMock, 'toFlatArray', []);
        $this->expectSelf($this->productFactoryMock, 'create');
        $this->expectReturn($this->productFactoryMock, 'load', $this->productMock);

        $this->expectReturn($this->adapterFactoryMock, 'create', false);

        $this->createModel();
        $this->model->setData('total_items', 100);

        $this->assertEquals(false, $this->model->processProductCallback($args));
    }

    public function testProcessProductCallbackProductTypeError()
    {
        $args = [
            'row' => [
                'type_id' => 'unknown',
            ]
        ];

        $this->expectReturn($this->feedMock, 'getConfig',['simple']);
        $this->expectReturn($this->feedMock, 'getId', 1);

        $this->createModel();

        $this->assertEquals(false, $this->model->processProductCallback($args));
    }

    public function testProcessProductCallbackTestMode()
    {
        $args = [
            'row' => [
                'type_id' => 'simple',
                'entity_id' => 1,
                'sku' => 'fakeSku'
            ]
        ];

        $this->feedMock->expects($this->any())
            ->method('getConfig')
            ->will($this->onConsecutiveCalls(['simple'], 'path'));
        $this->expectReturn($this->feedMock, 'getId', 1);
        $this->expectReturn($this->feedMock, 'getData', [
            'cell_enclose' => '"',
            'cell_enclose_escape' => '\"'
        ]);
        $this->expectReturn($this->feedMock, 'getColumnsMap', [
            ['column' => 'columnName'],
            ['column' => 'columnEmptyName']
        ]);

        $this->expectSelf($this->productFactoryMock, 'create');
        $this->expectReturn($this->productFactoryMock, 'load', $this->productMock);

        $this->expectReturn($this->adapterFactoryMock, 'create', $this->adapterMock);
        $this->expectReturn($this->adapterMock, 'isSkipped', false);
        $this->expectReturn($this->adapterMock, 'map', [['columnName' => 'columnValue']]);
        $this->expectReturn($this->adapterMock, 'getSkipProduct', false);

        $this->expectReturn($this->queueMock, 'getBatch', $this->batchMock);
        $this->expectSelf($this->queueMock, ['setBatch', 'setData', 'save']);

        $this->createModel();
        $this->model->setData('total_items', 100);
        $this->model->setTestSku('test-sku');
        $this->model->processProductCallback($args);

        $output = $this->model->getTestOutput();
        $expected = [[
            [
                'label' => 'columnName',
                'value' => 'columnValue'
            ],[
                'label' => 'columnEmptyName',
                'value' => null
            ]
        ]];

        $this->assertEquals($expected, $output);
    }

    public function testRunWithBatchFinished()
    {
        $this->setTestRunMocks();
        $this->expectReturn($this->feedMock, 'getId', 1);

        $this->expectReturn($this->batchMock, 'isEnabled', true);
        $this->expectReturn($this->batchMock, 'getOffset', 20);

        $this->expectReturn($this->queueMock, 'getBatch', $this->batchMock);
        $this->expectReturn($this->queueMock, 'getId', 5);

        $this->objectData['queue'] = $this->queueMock;

        $this->createModel();
        $this->model->setTestSku('testSku');
        $this->model->setData('total_items', 10);

        $this->assertInstanceOf('RocketWeb\ShoppingFeeds\Model\Generator', $this->model->run());
        $this->assertEquals([], $this->model->getTestOutput());
    }

    public function testRunWithBatch()
    {
        $this->setTestRunMocks();
        $this->expectReturn($this->feedMock, 'getId', 1);
        $this->expectReturn($this->batchMock, 'isEnabled', true);

        $this->createModel();
        $this->model->setData('total_items', 10);

        $this->assertInstanceOf('RocketWeb\ShoppingFeeds\Model\Generator', $this->model->run());
    }

    public function testRunHeader()
    {
        $this->expectReturn($this->feedMock, 'getColumnsMap', [
            ['column' => 'columnName'],
            ['column' => 'columnName'],
            ['column' => 'columnName']
        ]);
        $this->setTestRunMocks();
        $this->expectReturn($this->feedMock, 'getId', 1);
        $this->expectReturn($this->batchMock, 'isEnabled', true);

        $this->createModel();
        $this->model->setData('total_items', 10);

        $this->assertInstanceOf('RocketWeb\ShoppingFeeds\Model\Generator', $this->model->run());
    }

    public function testRun()
    {
        $this->setTestRunMocks();
        $this->expectAdvencedReturn($this->fileDriverMock, 'isExists', $this->onConsecutiveCalls());
        $this->expectReturn($this->feedMock, 'getId', 1);
        $this->expectReturn($this->feedMock, 'getUploadCollection', []);
        $this->expectReturn($this->batchMock, 'isEnabled', false);

        $this->objectData['queue'] = $this->queueMock;
        $this->createModel();

        $this->assertInstanceOf('RocketWeb\ShoppingFeeds\Model\Generator', $this->model->run());
    }

    public function testRunException()
    {
        $this->setTestRunMocks();

        $this->expectReturn($this->batchMock, 'isEnabled', false);

        $this->createModel();
        $this->setExpectedException('RocketWeb\ShoppingFeeds\Model\Exception', 'Generator must be created using existing feed - no Feed Id found!');

        $this->assertInstanceOf('RocketWeb\ShoppingFeeds\Model\Generator', $this->model->run());
    }

    protected function setTestRunMocks()
    {
        $this->expectReturn($this->dateTimeMock, 'getTimestamp', 1000000);
        $this->expectSelf($this->productCollectionProviderMock,
            ['getCollection', 'setStoreId', 'addStoreFilter', 'getSelect']
        );

        $this->expectReturn($this->feedMock, 'getColumnsMap', []);
        $this->feedMock->expects($this->any())
            ->method('getConfig')
            ->will($this->returnCallback(function($key, $default = '') {
                switch($key) {
                    case 'filters_product_types':
                        return ['simple','configurable','bundle','virtual','downloadable'];
                    case 'filters_add_out_of_stock':
                    case 'categories_include_all_products':
                        return true;
                    case 'filters_attribute_sets':
                    case 'categories_provider_taxonomy_by_category':
                        return [];
                    default:
                        break;
                }
                return $default;
            }));
        $this->feedMock->expects($this->any())
            ->method('getFile')
            ->will($this->returnCallback(function($key) {
                switch($key) {
                    case 'feed':
                        return 'feed_%s.txt';
                    default:
                        return 'log_%s.txt';
                }
            }));

        $this->expectSelf($this->iteratorMock, 'walk');

        $this->objectData['queue'] = null;
    }

    public function testDesctruct()
    {
        $this->expectReturn($this->queueMock, 'getBatch', $this->batchMock);
        $this->createModel();

        $this->model = null;
        $this->assertEmpty($this->model);
    }
}
