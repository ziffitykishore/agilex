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

namespace RocketWeb\ShoppingFeeds\Test\Unit\Model\Product\Mapper\Generic\Grouped\Associated;

use Magento\Framework\TestFramework\Unit\Helper\ObjectManager as ObjectManagerHelper;
use RocketWeb\ShoppingFeeds\Test\Unit\Model\ModelFramework;

/**
 * Class FeedTest
 */
class AvailabilityTest extends ModelFramework
{
    /**
     * @var \RocketWeb\ShoppingFeeds\Model\Product\Mapper\Generic\Grouped\Associated\Availability
     */
    protected $model;

    /**
     * @inheritdoc
     */
    protected function setUp()
    {
        parent::setUp();
        $this->objectManagerHelper = new ObjectManagerHelper($this);
        $this->parentAdapterMock = clone $this->adapterMock;

        $this->model = $this->objectManagerHelper->getObject(
            'RocketWeb\ShoppingFeeds\Model\Product\Mapper\Generic\Grouped\Associated\Availability',
            ['status' => $this->statusMock]
        );
    }

    public function testMapDefaultStock()
    {
        $this->expectReturn($this->feedMock, 'getConfig', true);
        $this->expectReturn($this->statusMock, 'getStockStatus', 1);

        $this->expectReturn($this->parentAdapterMock, 'getProduct', $this->productMock);
        $this->expectReturn($this->parentAdapterMock, 'getFeed', $this->feedMock);
        $this->expectReturn($this->adapterMock, 'getProduct', $this->productMock);
        $this->expectReturn($this->adapterMock, 'getFeed', $this->feedMock);

        $this->model->addAdapter($this->parentAdapterMock);

        $params = [];
        $cell = $this->model->map($params);
        $this->assertEquals('in stock', $cell);
    }
}