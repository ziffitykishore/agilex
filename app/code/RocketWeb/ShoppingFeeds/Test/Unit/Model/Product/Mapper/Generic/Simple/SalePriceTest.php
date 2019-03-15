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
 * Class SalePriceTest
 * @package RocketWeb\ShoppingFeeds\Test\Unit\Model\Product\Mapper\Generic\Simple
 */
class SalePriceTest extends ModelFramework
{
    /**
     * @var \RocketWeb\ShoppingFeeds\Model\Product\Mapper\Generic\Simple\SalePrice
     */
    protected $model;

    /**
     * @inheritdoc
     */
    protected function setUp()
    {
        parent::setUp();
        $this->objectManagerHelper = new ObjectManagerHelper($this);

        $this->expectReturn($this->adapterMock, 'getData', 'USD');

        $this->model = $this->objectManagerHelper->getObject(
            'RocketWeb\ShoppingFeeds\Model\Product\Mapper\Generic\Simple\SalePrice',
            []
        );
    }

    public function testMap()
    {
        $this->expectReturn($this->adapterMock, 'hasSpecialPrice', true);
        $this->expectReturn($this->adapterMock, 'getPrices', [
                'sp_excl_tax' => 100,
                'sp_incl_tax' => 122
            ]);

        $this->model->addAdapter($this->adapterMock);

        $params = ['param' => true];
        $cell = $this->model->map($params);
        $this->assertEquals(122, $cell);

        $params = ['param' => false];
        $cell = $this->model->map($params);
        $this->assertEquals(100, $cell);
    }

    public function testMapHasSpecialPrice()
    {
        $this->expectReturn($this->adapterMock, 'hasSpecialPrice', false);

        $this->model->addAdapter($this->adapterMock);

        $params = [];
        $cell = $this->model->map($params);
        $this->assertEquals(0, $cell);
    }
}