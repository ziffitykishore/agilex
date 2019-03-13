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

namespace RocketWeb\ShoppingFeeds\Test\Unit\Model\Product\Mapper\Generic\Configurable\Associated;

use Magento\Framework\TestFramework\Unit\Helper\ObjectManager as ObjectManagerHelper;
use RocketWeb\ShoppingFeeds\Test\Unit\Model\ModelFramework;

/**
 * Class UrlTest
 * @package RocketWeb\ShoppingFeeds\Test\Unit\Model\Product\Mapper\Generic\Simple
 */
class UrlTest extends ModelFramework
{
    /**
     * @var \RocketWeb\ShoppingFeeds\Model\Product\Mapper\Generic\Simple\Url
     */
    protected $model;

    /**
     * @inheritdoc
     */
    protected function setUp()
    {
        parent::setUp();
        $this->objectManagerHelper = new ObjectManagerHelper($this);

        $this->expectReturn($this->storeMock, 'getBaseUrl', 'http://base.url/');
        $this->expectReturn($this->productMock, 'getStore', $this->storeMock);
        $this->expectReturn($this->adapterMock, 'hasParentAdapter', true);
        $this->expectReturn($this->adapterMock, 'getProduct', $this->productMock);

        $this->model = $this->objectManagerHelper->getObject(
            'RocketWeb\ShoppingFeeds\Model\Product\Mapper\Generic\Configurable\Associated\Url',
            []
        );
    }

    public function testMap()
    {
        $this->expectReturn($this->productMock, 'getProductUrl', 'some/product/url/');
        $this->expectReturn($this->adapterMock, 'getUrlOptions', ['param' => 'value']);

        $this->model->addAdapter($this->adapterMock);

        $params = ['column' => 'test', 'param' => '?passed=in'];
        $cell = $this->model->map($params);
        $this->assertEquals('http://base.urlsome/product/url/?passed=in#param=value', $cell);
    }

    public function testMapWithBaseUrl()
    {
        $this->expectReturn($this->productMock, 'getProductUrl', 'https://base.url/some/product/url/');

        $this->model->addAdapter($this->adapterMock);

        $params = ['column' => 'test'];
        $cell = $this->model->map($params);
        $this->assertEquals('https://base.url/some/product/url/', $cell);
    }
}