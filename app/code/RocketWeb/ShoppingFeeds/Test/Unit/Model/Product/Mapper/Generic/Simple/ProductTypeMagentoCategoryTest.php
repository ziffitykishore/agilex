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


namespace RocketWeb\ShoppingFeeds\Test\Unit\Model\Product\Mapper\Generic\Simple;

use Magento\Framework\TestFramework\Unit\Helper\ObjectManager as ObjectManagerHelper;
use RocketWeb\ShoppingFeeds\Test\Unit\Model\ModelFramework;

/**
 * Class ProductTypeMagentoCategory
 */
class ProductTypeMagentoCategoryTest extends ModelFramework
{
    /**
     * @var \RocketWeb\ShoppingFeeds\Model\Product\Mapper\Generic\Simple\ProductTypeMagentoCategory
     */
    protected $model;


    /**
     * @inheritdoc
     */
    protected function setUp()
    {
        parent::setUp();
        $this->objectManagerHelper = new ObjectManagerHelper($this);

        $this->expectSelf($this->cacheMock, 'setCache');

        $this->model = $this->objectManagerHelper->getObject(
            'RocketWeb\ShoppingFeeds\Model\Product\Mapper\Generic\Simple\ProductTypeMagentoCategory',
            [
                'categoryFactory' => $this->categoryFactoryMock,
                'cache' => $this->cacheMock
            ]
        );
    }

    public function testMapEmpty()
    {
        $this->expectReturn($this->adapterMock, 'getFeed', $this->feedMock);
        $this->expectReturn($this->adapterMock, 'getProduct', $this->productMock);
        $this->expectReturn($this->feedMock, 'getConfig', []);
        $this->expectReturn($this->categoryCollectionProvider, 'exportToArray', []);
        $this->expectReturn($this->productMock, 'getCategoryCollection', $this->categoryCollectionProvider);
        $this->expectReturn($this->productMock, 'getCategoryIds', array());
        $this->expectSelf($this->categoryCollectionProvider, 'addFieldToFilter');

        $this->model->addAdapter($this->adapterMock);

        $expected = '';
        $cell = $this->model->map();
        $this->assertEquals($expected, $cell);

    }

    public function testMap()
    {
        $this->expectReturn($this->productMock, 'getCategoryIds', [1,5,6]);
        $this->expectReturn($this->adapterMock, 'getProduct', $this->productMock);
        $this->expectReturn($this->adapterMock, 'getStore', $this->storeMock);
        $this->expectReturn($this->adapterMock, 'getFeed', $this->feedMock);
        $this->expectReturn($this->productMock, 'getCategoryCollection', $this->categoryCollectionProvider);
        $this->expectReturn($this->feedMock, 'getConfig', [['p' => 5, 'id' => 2, 'ty' => 'value'], ['p' => 10, 'id' => 1, 'ty' => 'value 2']]);
        $this->expectSelf($this->categoryFactoryMock, ['create', 'setStoreId', 'load']);
        $this->expectSelf($this->categoryCollectionProvider, 'addFieldToFilter');
        $this->expectReturn($this->categoryCollectionProvider, 'exportToArray', [
            ['path' => '1/2/4'],
            ['path' => '1/2/5'],
            ['path' => '1/3/6'],
            ['path' => '1/3/4']
        ]);

        $this->expectAdvencedReturn($this->cacheMock, 'getCache',
            $this->returnCallback(function($key, $default = array()) {
                switch ($key) {
                    case ['row', 'map', 'category', 1, 'path']:
                        return array();
                    case ['row', 'map', 'category', 5, 'path']:
                        return array('root', 'category_tst');
                    default:
                        return $key;
                }
                return $default;
            })
        );

        $this->model->addAdapter($this->adapterMock);

        $expected = 'row > map > category > 6 > path, root > category_tst';
        $cell = $this->model->map();
        $this->assertEquals($expected, $cell);
    }

}