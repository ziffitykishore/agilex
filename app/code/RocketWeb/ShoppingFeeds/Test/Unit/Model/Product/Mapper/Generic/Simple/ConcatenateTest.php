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
 * Class ConcatenateTest
 */
class ConcatenateTest extends ModelFramework
{
    /**
     * @var \RocketWeb\ShoppingFeeds\Model\Product\Mapper\Generic\Simple\Concatenate
     */
    protected $model;

    /**
     * @inheritdoc
     */
    protected function setUp()
    {
        parent::setUp();
        $this->objectManagerHelper = new ObjectManagerHelper($this);

        $this->expectReturn($this->adapterMock, 'getFeed', $this->feedMock);

        $this->model = $this->objectManagerHelper->getObject(
            'RocketWeb\ShoppingFeeds\Model\Product\Mapper\Generic\Simple\Concatenate',
            []
        );
    }

    public function testMap()
    {
        $this->expectReturn($this->productMock, 'getId', 1);
        $this->expectReturn($this->adapterMock, 'getProduct', $this->productMock);
        $this->expectReturn($this->adapterMock, 'getData', 'store_code');
        $this->expectReturn($this->feedMock, 'getColumnsMap', [
            ['column' => 'link', 'attribute' => 'directive_url'],
            ['column' => 'image_link', 'attribute' => 'image_link'],
            ['column' => 'test_attribute', 'attribute' => 'directive_concatenate']
        ]);
        $this->expectReturn($this->adapterMock, 'getFeed', $this->feedMock);
        $this->expectAdvencedReturn($this->adapterMock, 'getMapValue',
            $this->returnCallback(function($arg) {
                return $arg['attribute'] . "_value";
            })
        );

        $this->model->addAdapter($this->adapterMock);

        $params = ['param' => '{{link}} - {{image_link}} - {{test_attribute}}'];
        $cell = $this->model->map($params);
        $this->assertEquals('directive_url_value - image_link_value - test_attribute_value', $cell);
    }

    public function testMapEmpty()
    {
        $this->expectReturn($this->feedMock, 'getColumnsMap', []);
        $this->expectReturn($this->productMock, 'getId', 1);
        $this->expectReturn($this->adapterMock, 'getProduct', $this->productMock);
        $this->expectReturn($this->adapterMock, 'getData', 'store_code');
        $this->expectAdvencedReturn($this->adapterMock, 'getMapValue',
            $this->returnCallback(function($arg) {
                return '';
            })
        );
        $this->expectReturn($this->adapterMock, 'mapEmptyValues', '');

        $this->model->addAdapter($this->adapterMock);

        $params = ['param' => '{{link}} - {{image_link}} - {{test_attribute}}'];
        $cell = $this->model->map($params);
        $this->assertEquals('', $cell);
    }

    public function testMapException()
    {
        $this->expectReturn($this->feedMock, 'getColumnsMap', []);
        $this->expectReturn($this->productMock, 'getId', 1);
        $this->expectReturn($this->adapterMock, 'getProduct', $this->productMock);
        $this->expectReturn($this->adapterMock, 'getData', 'store_code');
        $this->expectAdvencedReturn($this->adapterMock, 'getMapValue',
            $this->throwException(new \Exception())
        );

        $this->model->addAdapter($this->adapterMock);

        $params = ['param' => '{{link}} - {{image_link}} - {{test_attribute}}'];
        $cell = $this->model->map($params);
        $this->assertEquals('link - image_link - test_attribute', $cell);
    }

    public function testMapInvalidExpression()
    {
        $this->model->addAdapter($this->adapterMock);

        $params = ['param' => 'some param'];
        $cell = $this->model->map($params);
        $this->assertEquals('some param', $cell);
    }

}