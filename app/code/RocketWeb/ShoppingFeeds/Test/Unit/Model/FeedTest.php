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
 * Class FeedTest
 */
class FeedTest extends ModelFramework
{
    /**
     * @var \RocketWeb\ShoppingFeeds\Model\Feed
     */
    protected $model;

    /** 
     * @var int
     */
    protected $feedId = 8;

    /**
     * @inheritdoc
     */
    protected function setUp()
    {
        $this->objectManagerHelper = new ObjectManagerHelper($this);
        parent::setUp();
        
        $this->scheduleCollectionMock = $this->objectManagerHelper->getCollectionMock(
            'RocketWeb\ShoppingFeeds\Model\ResourceModel\Feed\Schedule\Collection',
            [$this->scheduleMock]
        );
        
        $this->expectSelf($this->scheduleCollectionMock, 'setFeedFilter');
        $this->expectReturn($this->scheduleCollectionFactoryMock, 'create', $this->scheduleCollectionMock);


        $this->configCollectionMock = $this->objectManagerHelper->getCollectionMock(
            'RocketWeb\ShoppingFeeds\Model\ResourceModel\Feed\Config\Collection',
            [$this->configMock]
        );
        
        $this->expectSelf($this->configCollectionMock, 'setFeedFilter');
        $this->expectReturn($this->configCollectionFactoryMock, 'create', $this->configCollectionMock);

        $this->feed = $this->objectManagerHelper->getObject(
            'RocketWeb\ShoppingFeeds\Model\Feed',
            [
                'context' => $this->contextMock,
                'registry' => $this->registryMock,
                'scheduleCollectionFactory' => $this->scheduleCollectionFactoryMock,
                'scheduleFactory' => $this->scheduleFactoryMock,
                'configCollectionFactory' => $this->configCollectionFactoryMock,
                'configFactory' => $this->configFactoryMock,
                'feedTypesConfig' => $this->feedTypesConfigMock,
                'localeDate' => $this->localeDateMock,
                'storeObject' => $this->storeMock,
                'priceCurrency' => $this->priceCurrencyMock,
                'resource' => $this->resource,
                'resourceCollection' => $this->resourceCollection,
                'data' => [
                    'id' => $this->feedId, 
                    'name' => 'Foo', 
                    'store_id' => 2,
                    'type' => 'generic'
                ]
            ]
        );
    }

    /**
     * Test columns map
     */
    public function testColumnsMap()
    {
        $returnValue = array(
            'default_feed_config' => array(
                'columns' => array(
                    'product_columns' => array(
                        array(
                            'order' => 20,
                            'column' => 'test2'
                        ),
                        array(
                            'order' => 10,
                            'column' => 'test1'
                        )
                    )
                )
            ),
            'output_params' => '',
            'file' => ''
        );

        $this->expectReturn($this->feedTypesConfigMock, 'getFeed', $returnValue);

        $this->feed->afterLoad();

        $expectedColumnsMap = array(
            array(
                'order' => 10,
                'column' => 'test1',
            ),
            array(
                'order' => 20,
                'column' => 'test2',
            )
        );

        $this->assertEquals($expectedColumnsMap, $this->feed->getColumnsMap());
    }

    public function testSetColumnsMap()
    {
        $excpected = ['fake array'];
        $this->feed->setColumnsMap($excpected);

        $this->assertEquals($excpected, $this->feed->getColumnsMap());
    }

    /**
     * Test afterLoad method in connection to messages
     */
    public function testAfterLoadConfig()
    {
        $returnValue = array(
            'default_feed_config' => array('section' => array('path' => 'value')),
            'output_params' => '',
            'file' => ''
        );

        $expectedConfig = new \Magento\Framework\DataObject();
        $expectedConfig->addData(array(
            'section_path' => 'value'
        ));

        $this->expectReturn($this->feedTypesConfigMock, 'getFeed', $returnValue);

        $this->feed->afterLoad();
        $this->assertEquals($expectedConfig, $this->feed->getData('config'));
    }

    /**
     * Test getConfigCollection method
     */
    public function testGetConfigCollection()
    {
        $this->assertInstanceOf(
            '\RocketWeb\ShoppingFeeds\Model\ResourceModel\Feed\Config\Collection',
            $this->feed->getConfigCollection()
        );
    }

    /**
     * Test getMessages method
     *
     * * @dataProvider getMessagesDataProvider
     */
    public function testGetMessages($messages, $expected)
    {
        $this->feed->setData('messages', $messages);

        $this->assertEquals($expected, $this->feed->getMessages());
    }

    /**
     * @return array
     */
    public function getMessagesDataProvider()
    {
        return [
            [
                'messages' => serialize(['foo' => 'bar']),
                'expected' => ['foo' => 'bar'],
            ],
            [
                'messages' => ['foo' => 'bar'],
                'expected' => ['foo' => 'bar'],
            ],
        ];
    }

    /**
     * Test getScheduleCollection method
     */
    public function testGetScheduleCollection()
    {
        $this->assertInstanceOf(
            '\RocketWeb\ShoppingFeeds\Model\ResourceModel\Feed\Schedule\Collection',
            $this->feed->getScheduleCollection()
        );
    }

    public function testGetSchedulesFromCache()
    {
        $schedule = ['fake array'];
        $this->feed->setData('schedules', $schedule);

        $this->assertEquals($schedule, $this->feed->getSchedules());
    }

    public function testGetSchedules()
    {
        $this->expectReturn($this->scheduleMock, 'getData', ['fake array']);

        $expected = [['fake array']];
        $this->assertEquals($expected, $this->feed->getSchedules());
    }

    /**
     * Test formatted schedules empty value
     */
    public function testEmptyGetFormattedSchedules()
    {
        $this->expectReturn($this->scheduleCollectionMock, 'getSize', 0);
        $expectedSchedules = ['None'];

        $this->assertEquals($expectedSchedules, $this->feed->getFormattedSchedules());
    }

    /**
     * Test formatted schedules
     */
    public function testGetFormattedSchedules()
    {
        $this->expectReturn($this->scheduleCollectionMock, 'getSize', 1);
        $this->expectReturn($this->scheduleMock, 'getFormattedSchedule', 'Some Schedule');

        $expectedSchedules = ['Some Schedule'];

        $this->assertEquals($expectedSchedules, $this->feed->getFormattedSchedules());
    }

    public function testSetType()
    {
        $type = 'simple';
        $returnValue = array(
            'default_feed_config' => array('section' => array('path' => 'value')),
            'output_params' => '',
            'file' => ''
        );

        $expectedConfig = new \Magento\Framework\DataObject();
        $expectedConfig->addData(array(
            'section_path' => 'value'
        ));

        $this->expectReturn($this->feedTypesConfigMock, 'getFeed', $returnValue);
        $this->feed->setType($type);

        $this->assertEquals($expectedConfig, $this->feed->getConfig());
    }

    public function testIsProductTypeEnabled()
    {
        $type = 'simple';
        $returnValue = array(
            'default_feed_config' => array('filters' => array('product_types' => ['simple'])),
            'output_params' => '',
            'file' => ''
        );

        $this->expectReturn($this->feedTypesConfigMock, 'getFeed', $returnValue);
        $this->feed->setType($type);

        $this->assertEquals(true, $this->feed->isProductTypeEnabled($type));
    }

    public function testIsTaxonomyAutocompleteEnabled()
    {
        $type = 'simple';
        $returnValue = array(
            'default_feed_config' => array('categories' => array('taxonomy_autocomplete_enabled' => 1)),
            'output_params' => '',
            'file' => ''
        );

        $this->expectReturn($this->feedTypesConfigMock, 'getFeed', $returnValue);
        $this->feed->setType($type);

        $this->assertEquals(true, $this->feed->isTaxonomyAutocompleteEnabled());
    }

    public function testGetConfig()
    {
        $type = 'simple';
        $returnValue = array(
            'default_feed_config' => array('categories' => array('taxonomy_autocomplete_enabled' => 1)),
            'output_params' => '',
            'file' => ''
        );

        $this->expectReturn($this->feedTypesConfigMock, 'getFeed', $returnValue);
        $this->feed->setType($type);

        $this->assertEquals('fail', $this->feed->getConfig('fake path', 'fail'));
    }

    public function testGetStore()
    {
        $this->expectReturn($this->storeMock, 'getStoreId', 0);
        $this->expectReturn($this->priceCurrencyMock, 'getCurrency', 'return value');

        $this->assertEquals('return value', $this->feed->getStore()->getData('current_currency'));
    }
    
    public function testAfterSave()
    {
        $this->feed->setData('schedules', []);
        $this->assertInstanceOf('RocketWeb\ShoppingFeeds\Model\Feed', $this->feed->afterSave());
    }

    public function testAfterSaveWithConfig()
    {
        $config = new \Magento\Framework\DataObject();
        $config->addData(array(
            'section_path' => 'value',
            'shipping_cache_enabled' => true
        ));
        $this->feed->setData('config', $config);
        
        $this->configMock->expects($this->any())
            ->method('getData')
            ->will($this->onConsecutiveCalls('section_path', 'new value'));
        
        $this->feed->setData('schedules', []);
        $this->assertInstanceOf('RocketWeb\ShoppingFeeds\Model\Feed', $this->feed->afterSave());
    }
    
    public function testSaveSchedules()
    {
        $this->expectSelf($this->scheduleMock, 'load');
        $schedules = [
            ['id' => 1, 'start_at' => 1, 'batch_mode' => true, 'batch_limit' => 5000],
            ['id' => '', 'start_at' => 12, 'batch_mode' => false, 'batch_limit' => '']
        ];
        $this->scheduleMock->expects($this->any())
            ->method('getId')
            ->will($this->onConsecutiveCalls(null, null, 1, 1));
        $this->feed->setData('schedules', $schedules);
        $this->feed->saveSchedules();

        $this->assertInstanceOf('RocketWeb\ShoppingFeeds\Model\Feed', $this->feed);
    }

    public function testDeleteSchedules()
    {
        $this->expectSelf($this->scheduleMock, 'load');

        $schedules = [
            ['id' => 1, 'delete' => true],
            ['id' => '', 'start_at' => 12]
        ];

        $this->scheduleMock->expects($this->any())
            ->method('getId')
            ->will($this->onConsecutiveCalls(null, null, 1, 1));

        $this->feed->setData($schedules);
        $this->feed->saveSchedules();

        $this->assertInstanceOf('RocketWeb\ShoppingFeeds\Model\Feed', $this->feed);
    }
}