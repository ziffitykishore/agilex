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


namespace RocketWeb\ShoppingFeeds\Test\Unit\Model\Product\Adapter\Type;

use Magento\Framework\DataObject;
use Magento\Framework\TestFramework\Unit\Helper\ObjectManager as ObjectManagerHelper;
use RocketWeb\ShoppingFeeds\Test\Unit\Model\ModelFramework;

/**
 * Class BundleTest
 */
class BundleTest extends ModelFramework
{
    /**
     * @var \RocketWeb\ShoppingFeeds\Model\Product\Adapter\Type\Bundle
     */
    protected $model;

    /**
     * @var array
     */
    protected $modelArguments = [];

    public function setUp()
    {
        parent::setUp();
        $this->objectManagerHelper = new ObjectManagerHelper($this);

        $this->expectReturn($this->productMock, 'getId', 1);
        $this->expectReturn($this->productMock, 'getSku', 'S-K-U');
        $this->expectReturn($this->feedMock, 'getId', 1);
        $this->expectReturn($this->feedMock, 'getStore', $this->storeMock);
        $this->expectReturn($this->stockStateMock, 'getStockQty', 5);
        $this->expectSelf($this->storeMock, 'getCurrentCurrency');

        $filesystemMock = $this->getMock('Magento\Framework\Filesystem', [], [], '', false);
        $directoryMock = $this->getMock('\Magento\Framework\Filesystem\Directory\ReadInterface', [], [], '', false);
        $this->expectReturn($filesystemMock, 'getDirectoryRead', $directoryMock);

        $this->modelArguments = [
            'filesystem' => $filesystemMock,
            'feed' => $this->feedMock,
            'product' => $this->productMock,
            'feedTypesConfig' => $this->feedTypesConfigMock,
            'mapperFactory' => $this->mapperFactoryMock,
            'helper' => $this->helperMock,
            'weeeData' => $this->weeHelperMock,
            'taxData' => $this->taxHelperMock,
            'catalogHelper' => $this->catalogHelperMock,
            'productTypePrice' => $this->catalogProductPriceMock,
            'optionFactory' => $this->optionFactoryMock,
            'adapterFactory' => $this->adapterFactoryMock,
            'timezone' => $this->dateTimeMock,
            'stockState' => $this->stockStateMock,
            'date' => $this->dateMock,
            'cache' => $this->cacheMock,
            'filter' => $this->filterMock,
            'logger' => $this->loggerMock,
            'data' => []
        ];

        $this->model = $this->objectManagerHelper->getObject(
            'RocketWeb\ShoppingFeeds\Model\Product\Adapter\Type\Bundle',
            $this->modelArguments
        );
    }

    public function testBeforeMap()
    {
        $this->expectSelf($this->productMock, ['getTypeInstance', 'getSelectionsCollection']);
        $this->expectReturn($this->productMock, 'getOptionsIds', ['id']);

        $productMock = $this->getModelMock(
            'Magento\Catalog\Model\Product', ['getId', 'getSku', 'isDisabled', 'getStoreId']);
        $this->expectAdvencedReturn($productMock, 'isDisabled', $this->onConsecutiveCalls(true, false));
        $this->expectReturn($productMock, 'getStoreId', 1);
        $this->expectReturn($this->productMock, 'addAttributeToSelect', [$productMock, $productMock]);

        $this->expectReturn($this->adapterFactoryMock, 'create', $this->adapterMock);

        $this->model->beforeMap();
        $this->assertEquals([$this->adapterMock], $this->model->getData('associated_product_adapters'));
    }

    /**
     * @dataProvider getPricesProvider
     *
     * @param $quantity
     * @param $algorithm
     * @param $expected
     */
    public function testGetPrices($quantity, $algorithm, $expected)
    {
        $configMock = $this->getModelMock('\Magento\Framework\DataObject', ['getAlgorithm']);
        $this->expectReturn($configMock, 'getAlgorithm', $algorithm);
        $this->expectReturn($this->taxHelperMock, 'getConfig', $configMock);
        $this->expectReturn($this->helperMock, 'getQuantityIcrements', $quantity);
        $this->expectReturn($this->weeHelperMock, 'getAmountExclTax', 0);
        $this->expectReturn($this->weeHelperMock, 'isTaxable', true);
        $this->expectReturn($this->weeHelperMock, 'getProductWeeeAttributesForRenderer',[]);
        $this->expectAdvencedReturn($this->catalogHelperMock, 'getTaxPrice',
            $this->returnCallback(function($product, $price, $tax = false) {
                return $tax ? $price * 1.2 : $price * 1.0;
            })
        );

        $this->expectReturn($this->productMock, 'getSpecialPrice', 200);
        $this->expectSelf($this->storeMock, 'getBaseCurrency');
        $this->expectAdvencedReturn($this->productMock, 'getTotalPrices',
            $this->onConsecutiveCalls([200], 100)
        );

        $this->expectAdvencedReturn($this->storeMock, 'convert', $this->returnArgument(0));
        $this->expectReturn($this->catalogProductPriceMock, 'calculatePrice', 250);

        // Testing the method
        $prices = $this->model->getPrices();
        $this->assertEquals($expected, $prices);

        // Testing the cache
        $prices = $this->model->getPrices();
        $this->assertEquals($expected, $prices);

    }

    public function getPricesProvider()
    {
        return [
            [1, '', [
                'p_excl_tax' => 200.0,
                'p_incl_tax' => 240.0,
                'sp_excl_tax' => 100.0,
                'sp_incl_tax' => 120.0
            ]],
            [10, '', [
                'p_excl_tax' => 2000.0,
                'p_incl_tax' => 2400.0,
                'sp_excl_tax' => 1000.0,
                'sp_incl_tax' => 1200.0
            ]],
            [10, 'UNIT_BASE_CALCULATION', [
                'p_excl_tax' => 2000.0,
                'p_incl_tax' => 2400.0,
                'sp_excl_tax' => 1000.0,
                'sp_incl_tax' => 1200.0
            ]],
        ];
    }

    public function testGetAssociatedMapColumnsFromParent()
    {
        $this->expectReturn($this->feedMock, 'getConfig', []);
        $this->assertEquals([], $this->model->getAssociatedMapInheritance());
    }

    public function testGetAssociatedProductsMode()
    {
        $this->assertEquals([], $this->model->getAssociatedMapInheritance());
    }

    public function testGetChildrenCount()
    {
        $this->model->setData('associated_product_adapters', []);
        $this->assertEquals(1, $this->model->getChildrenCount());
    }
}