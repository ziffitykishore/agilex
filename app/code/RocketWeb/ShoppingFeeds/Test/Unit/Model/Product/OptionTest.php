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


namespace RocketWeb\ShoppingFeeds\Test\Unit\Model\Product;

use Magento\Framework\TestFramework\Unit\Helper\ObjectManager as ObjectManagerHelper;
use RocketWeb\ShoppingFeeds\Test\Unit\Model\ModelFramework;

class OptionTest extends ModelFramework
{
    /**
     * @var \RocketWeb\ShoppingFeeds\Model\Product\Option
     */
    protected $model;

    protected $valueMockArray = [];

    /**
     * @inheritdoc
     */
    protected function setUp()
    {
        parent::setUp();
        $this->objectManagerHelper = new ObjectManagerHelper($this);

        $this->expectReturn($this->productMock, 'getId', 1);
        $this->expectReturn($this->productMock, 'getSku', 'product_sku');

        $this->expectReturn($this->optionMock, 'getTitle', 'Option name');
        $this->expectReturn($this->optionMock, 'getId', 1);
        $this->expectReturn($this->optionMock, 'getGroupByType', 'checkbox');

        $valueMock1 = $this->prepareValueMock(1);
        $valueMock2 = $this->prepareValueMock(2);
        $this->valueMockArray = [$valueMock1, $valueMock2];
        $this->expectReturn($this->optionMock, 'getValues', $this->valueMockArray);
        $this->expectReturn($this->productMock, 'getOptions', [$this->optionMock]);

        $this->expectReturn($this->adapterMock, 'getFeed', $this->feedMock);
        $this->expectReturn($this->feedMock, 'getColumnsMap', [[
                'column' => 'Column',
                'attribute' => 'directive_product_option',
                'param' => 'Option name'
            ],[
                'column' => 'id',
                'attribute' => 'directive_id'
            ],[
                'column' => 'sku',
                'attribute' => 'sku'
            ],[
                'column' => 'item_group_id',
                'attribute' => 'directive_item_group_id'
            ]]
        );
        $this->expectReturn($this->adapterMock, 'getProduct', $this->productMock);

        $pricingHelperMock = $this->getModelMock('Magento\Framework\Pricing\Helper\Data', ['currency']);
        $this->expectAdvencedReturn($pricingHelperMock, 'currency', $this->returnArgument(0));

        $this->model = $this->objectManagerHelper->getObject(
            'RocketWeb\ShoppingFeeds\Model\Product\Option',
            [
                'adapter' => $this->adapterMock,
                'pricingHelper' => $pricingHelperMock
            ]
        );
    }

    public function testProcess()
    {
        $rows = [
            [
                'id' => '1',
                'sku' => 'original_sku',
                'Column' => '',
                'price' => 2000,
                'sale_price' => 1000,
                'item_group_id' => 'item_group_id'
            ]
        ];
        
        $expected = [[
            'id' => '1-option0',
            'sku' => 'sku-1',
            'Column' => 'title 1',
            'price' => 2100,
            'sale_price' => 1100,
            'item_group_id' => 'product_sku',
        ],[
            'id' => '1-option1',
            'sku' => 'sku-2',
            'Column' => 'title 2',
            'price' => 2200,
            'sale_price' => 1200,
            'item_group_id' => 'product_sku'
        ]];
        $output = $this->model->process($rows);
        $this->assertEquals($expected, $output);
    }

    public function testGetOptions()
    {
        $options = $this->model->getOptions();
        $this->assertEquals(['Column' => $this->valueMockArray], $options);
    }

    protected function prepareValueMock($count)
    {
        $valueMock = $this->getModelMock('\Magento\Catalog\Model\Product\Option\Value',
            ['getTitle', 'getPrice', 'getId', 'getSku', 'getProduct']);

        $this->expectReturn($valueMock, 'getTitle', sprintf('title %s', $count));
        $this->expectReturn($valueMock, 'getPrice', (int)sprintf('%s00', $count));
        $this->expectReturn($valueMock, 'getId', $count);
        $this->expectReturn($valueMock, 'getSku', sprintf('sku-%s', $count));
        $this->expectReturn($valueMock, 'getProduct', $this->productMock);

        return $valueMock;
    }
}
