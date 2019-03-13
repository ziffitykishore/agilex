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


namespace RocketWeb\ShoppingFeedsGoogle\Test\Unit\Model;

use \Magento\Framework\TestFramework\Unit\Helper\ObjectManager as ObjectManagerHelper;
use \Magento\ConfigurableProduct\Model\Product\Type\Configurable as ConfigurableType;
use \Magento\GroupedProduct\Model\Product\Type\Grouped as GroupedType;
use \RocketWeb\ShoppingFeeds\Model\Product\Mapper\Generic\Configurable\Availability;
/**
 * Class MicrodataTest
 */
class MicrodataTest extends \PHPUnit_Framework_TestCase
{
    const MAPPED_PROD_SKU = 'SKU-729';
    const MAPPED_PROD_NAME = 'test Product';
    const MAPPED_PROD_PRICE = '29.95';
    const MAPPED_PROD_SALE_PRICE = '19.95';
    const MAPPED_PROD_AVAILABILITY = Availability::IN_STOCK;
    const CONDITION_ATTR_CODE = 'condition';
    const CONDITION_SCHEMA = 'http://schema.org/NewCondition';
    const AVAILABILITY_SCHEMA = 'http://schema.org/InStock';
    const STORE_CURRENCY = 'USD';

    /**
     * @var \RocketWeb\ShoppingFeedsGoogle\Model\Microdata
     */
    protected $model;

    /**
     * @var ObjectManagerHelper
     */
    protected $objectManagerHelper;

    /**
     * @var \Magento\Framework\Model\Context|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $contextMock;

    /**
     * @var \Magento\Framework\Registry|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $registryMock;

    /**
     * @var \RocketWeb\ShoppingFeeds\Model\FeedFactory|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $feedFactoryMock;

    /**
     * @var \RocketWeb\ShoppingFeeds\Model\Feed|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $feedMock;

    /**
     * @var \Magento\Catalog\Model\Product|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $productMock;

    /**
     * @var \Magento\Store\Model\Store|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $storeMock;

    /**
     * @var \Magento\Framework\App\Request\Http|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $requestMock;

    /**
     * @var \RocketWeb\ShoppingFeeds\Model\Product\Adapter\Type\Configurable|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $adapterMock;

    /**
     * @var \RocketWeb\ShoppingFeeds\Model\Product\Adapter\AdapterFactory|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $adapterFactoryMock;

    /**
     * @inheritdoc
     */
    protected function setUp()
    {
        $this->objectManagerHelper = new ObjectManagerHelper($this);

        /** Product Param Mock */
        $this->setupProductMock();

        /** Feed Mock */
        $this->setupFeedMock();

        /** Store Mock */
        $this->setupStoreMock();

        /** Request Mock */
        //$this->setupRequestMock();

        /** Request Mock */
        $this->setupAdapterMock();

        $this->contextMock = $this->getMock('Magento\Framework\Model\Context', [], [], '', false);
        $this->registryMock = $this->getMock('Magento\Framework\Registry');

        $this->model = $this->objectManagerHelper->getObject(
            'RocketWeb\ShoppingFeedsGoogle\Model\Microdata',
            [
                'context'           => $this->contextMock,
                'registry'          => $this->registryMock,
                'feedFactory'       => $this->feedFactoryMock,
                'adapterFactory'       => $this->adapterFactoryMock,
                'data' => [
                    'product'             => $this->productMock,
                    'block_product'       => null,
                    'store'               => $this->storeMock,
                    'condition_attribute' => '',
                    'include_tax'         => false,
                    'assoc_id'            => false,
                    'request_params'      => []
                ]
            ]
        );
    }

    /**
     * Test getMicrodata method when product has sale_price.
     */
    public function testGetMicrodataWithSalePrice()
    {
        $this->productMock->expects($this->any())
            ->method('getOptions')
            ->will($this->returnValue([]));

        $this->setupAdapterMockWithSalePrice();

        $expected = $this->getExpectedMicrodataWithSalePrice();

        $result = $this->model->getMicrodata();
        $result = $result->toArray();
        $this->assertEquals($expected, $result);
    }

    /**
     * Test getMicrodata method when product doesn't have sale_price.
     */
    public function testGetMicdodataNoSalePrice()
    {
        $this->productMock->expects($this->any())
            ->method('getOptions')
            ->will($this->returnValue([]));

        $this->setupAdapterMockNoSalePrice();
        $expected = $this->getExpectedMicrodataNoSalePrice();

        $result = $this->model->getMicrodata();
        $result = $result->toArray();
        $this->assertEquals($expected, $result);
    }

    protected function getExpectedMicrodata($salePrice = '')
    {
        return [
            'sku'          => self::MAPPED_PROD_SKU,
            'name'         => self::MAPPED_PROD_NAME,
            'price'        => $salePrice ? $salePrice : self::MAPPED_PROD_PRICE,
            'availability' => self::AVAILABILITY_SCHEMA,
            'condition'    => self::CONDITION_SCHEMA,
            'currency'     => self::STORE_CURRENCY
        ];
    }

    public function getExpectedMicrodataNoSalePrice()
    {
        return $this->getExpectedMicrodata();
    }

    public function getExpectedMicrodataWithSalePrice()
    {
        return $this->getExpectedMicrodata(self::MAPPED_PROD_SALE_PRICE);
    }

    protected function setupProductMock()
    {
        /** Product Mock */
        $this->productMock = $this->getMock('Magento\Catalog\Model\Product', [], [], '', false);
    }

    protected function setupStoreMock()
    {
        /** Product Mock */
        $this->storeMock = $this->getMock('Magento\Store\Model\Store', [], [], '', false);
        $this->storeMock->expects($this->any())
            ->method('getId')
            ->will($this->returnValue(1));
    }

    protected function setupFeedMock()
    {
        $this->feedMock = $this->getMockBuilder(
            'RocketWeb\ShoppingFeeds\Model\Feed'
        )->disableOriginalConstructor()
            ->setMethods(['load', 'getId', 'getConfig'])
            ->getMock();

        $this->feedMock->expects($this->any())
            ->method('load')
            ->will($this->returnSelf());

        $this->feedMock->expects($this->any())
            ->method('getId')
            ->will($this->returnValue(true));

        $this->feedMock->expects($this->once())
            ->method('getConfig')
            ->with('general_currency')
            ->will($this->returnValue('USD'));

        $this->feedFactoryMock = $this->getMockBuilder(
            'RocketWeb\ShoppingFeeds\Model\FeedFactory'
        )->disableOriginalConstructor()
            ->setMethods(['create'])
            ->getMock();

        $this->feedFactoryMock->expects($this->any())
            ->method('create')
            ->will($this->returnValue($this->feedMock));
    }

    protected function setupAdapterMock()
    {
        $this->adapterMock = $this->getMockBuilder(
            'RocketWeb\ShoppingFeeds\Model\Product\Adapter\Type\Configurable'
        )->disableOriginalConstructor()
            ->setMethods(['beforeMap', 'getMapValue', 'getFeed'])
            ->getMock();

        $this->adapterMock->expects($this->any())
            ->method('beforeMap')
            ->willReturn($this->returnValue(true));

        $this->adapterMock->expects($this->any())
            ->method('getFeed')
            ->willReturn($this->feedMock);

        $this->adapterFactoryMock = $this->getMock('RocketWeb\ShoppingFeeds\Model\Product\Adapter\AdapterFactory', [], [], '', false);
        $this->adapterFactoryMock->expects($this->any())
            ->method('create')
            ->will($this->returnValue($this->adapterMock));
    }

    protected function setupAdapterMockWithSalePrice()
    {
        $valueMap = [
            [['attribute' => 'sku', 'column' => 'sku', 'param' => ''], self::MAPPED_PROD_SKU],
            [['attribute' => 'name', 'column' => 'title', 'param' => ''], self::MAPPED_PROD_NAME],
            [['attribute' => 'directive_price', 'column' => 'price', 'param' => false], self::MAPPED_PROD_PRICE],
            [['attribute' => 'directive_sale_price', 'column' => 'sale_price', 'param' => false], self::MAPPED_PROD_SALE_PRICE],
            [['attribute' => 'directive_availability', 'column' => 'availability', 'param' => ''], self::MAPPED_PROD_AVAILABILITY]
        ];

        $this->adapterMock->expects($this->any())
            ->method('getMapValue')
            ->will($this->returnValueMap($valueMap));


    }

    protected function setupAdapterMockNoSalePrice()
    {
        $valueMap = [
            [['attribute' => 'sku', 'column' => 'sku', 'param' => ''], self::MAPPED_PROD_SKU],
            [['attribute' => 'name', 'column' => 'title', 'param' => ''], self::MAPPED_PROD_NAME],
            [['attribute' => 'directive_price', 'column' => 'price', 'param' => false], self::MAPPED_PROD_PRICE],
            [['attribute' => 'directive_sale_price', 'column' => 'sale_price', 'param' => false], ''],
            [['attribute' => 'directive_availability', 'column' => 'availability', 'param' => ''], self::MAPPED_PROD_AVAILABILITY]
        ];

        $this->adapterMock->expects($this->any())
            ->method('getMapValue')
            ->will($this->returnValueMap($valueMap));
    }
}