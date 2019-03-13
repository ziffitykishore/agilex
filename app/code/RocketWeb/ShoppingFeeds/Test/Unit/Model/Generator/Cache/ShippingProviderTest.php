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


namespace RocketWeb\ShoppingFeeds\Test\Unit\Model\Generator\Cache;

use Magento\Framework\TestFramework\Unit\Helper\ObjectManager as ObjectManagerHelper;
use RocketWeb\ShoppingFeeds\Test\Unit\Model\ModelFramework;

/**
 * Class ShippingProviderTest
 */
class ShippingProviderTest extends ModelFramework
{
    /**
     * @var \RocketWeb\ShoppingFeeds\Model\Generator\Cache\ShippingProvider
     */
    protected $model;

    /**
     * @var \RocketWeb\ShoppingFeeds\Model\ResourceModel\Shipping\CollectionFactory|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $shippingCollectionFactoryMock;

    /**
     * @var \RocketWeb\ShoppingFeeds\Model\ResourceModel\Shipping\Collection|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $shippingCollectionMock;

    /**
     * @var \RocketWeb\ShoppingFeeds\Model\Product\ShippingFactory|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $shippingFactoryMock;

    public function setUp()
    {
        parent::setUp();
        $this->objectManagerHelper = new ObjectManagerHelper($this);

        $this->expectSelf($this->cacheMock, 'setCache');

        $this->shippingCollectionMock = $this->getModelMock('RocketWeb\ShoppingFeeds\Model\Product\Shipping', [
                    'filterByFeed', 'filterByProduct', 'filterByStore',
                    'filterByCurrencyCode', 'filterByDate', 'count',
                    'getFirstItem', 'getValue', 'setPageSize', 'getSize'
                ]);

        $this->shippingCollectionFactoryMock = $this->getModelMock(
            'RocketWeb\ShoppingFeeds\Model\ResourceModel\Shipping\CollectionFactory', ['create']);

        $this->shippingFactoryMock = $this->getModelMock(
            'RocketWeb\ShoppingFeeds\Model\Product\ShippingFactory', ['create']);

        $this->model = $this->objectManagerHelper->getObject(
            'RocketWeb\ShoppingFeeds\Model\Generator\Cache\ShippingProvider', [
                'cache' => $this->cacheMock,
                'shippingCollectionFactory' => $this->shippingCollectionFactoryMock,
                'shippingFactory' => $this->shippingFactoryMock
            ]
        );
    }

    public function testGetShipping()
    {
        $this->expectReturn($this->cacheMock, 'getCache', false);
        $this->expectReturn($this->shippingFactoryMock, 'create', 'return');

        $expected = 'return';
        $this->assertEquals($expected, $this->model->getShipping($this->adapterMock));
    }

    public function testGetCache()
    {
        $this->_testGetCache();
        $this->expectReturn($this->shippingCollectionMock, 'getSize', 1);
        $this->expectReturn($this->shippingCollectionFactoryMock, 'create', $this->shippingCollectionMock);
        $this->expectSelf($this->shippingCollectionMock, ['setPageSize']);

        $expected = 'cache value';
        $this->model->prepareCache($this->adapterMock, $this->productMock, 100);
        $this->model->setCache($expected);

        $this->assertEquals($expected, $this->model->getCache());
    }

    public function testGetCacheNoCache()
    {
        $this->_testGetCache();
        $this->shippingCollectionMock->expects($this->any())
            ->method('getSize')
            ->will($this->returnValue(0));

        $this->shippingCollectionFactoryMock->expects($this->any())
            ->method('create')
            ->will($this->returnValue($this->shippingCollectionMock));

        $productMock = $this->getMockBuilder('Magento\Catalog\Model\Product')
            ->disableOriginalConstructor()
            ->getMock();

        $this->model->prepareCache($this->adapterMock, $productMock);
        $expected = false;
        $this->assertEquals($expected, $this->model->getCache());
    }

    protected function _testGetCache()
    {
        $this->expectSelf($this->shippingCollectionMock,
            ['filterByFeed', 'filterByProduct', 'filterByStore', 'filterByCurrencyCode', 'filterByDate']
        );

        $firstItemMock = $this->getModelMock('RocketWeb\ShoppingFeeds\Model\Shipping',
            ['getId', 'setUpdatedAt', 'save']);
        $this->expectReturn($firstItemMock, 'getId', false);
        $this->expectSelf($firstItemMock, ['setUpdatedAt', 'save']);

        $this->expectReturn($this->shippingCollectionMock, 'getFirstItem', $firstItemMock);

        $this->adapterMock = $this->getModelMock('RocketWeb\ShoppingFeeds\Model\Product\Adapter\Type\Simple',
            ['getTimezone', 'date', 'getStore', 'getCurrentCurrency', 'getCode', 'getFeed', 'format', 'sub']);
        $this->expectSelf($this->adapterMock,
            ['getTimezone', 'date', 'getStore', 'getFeed', 'getCurrentCurrency', 'getCode', 'format', 'sub']
        );
    }
}
