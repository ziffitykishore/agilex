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
 * Class SalePriceEffectiveDateTest
 * @package RocketWeb\ShoppingFeeds\Test\Unit\Model\Product\Mapper\Generic\Simple
 */
class SalePriceEffectiveDateTest extends ModelFramework
{
    /**
     * @var \RocketWeb\ShoppingFeeds\Model\Product\Mapper\Generic\Simple\ExpirationDate
     */
    protected $model;

    /**
     * @inheritdoc
     */
    protected function setUp()
    {
        parent::setUp();
        $this->objectManagerHelper = new ObjectManagerHelper($this);

        $this->model = $this->objectManagerHelper->getObject(
            'RocketWeb\ShoppingFeeds\Model\Product\Mapper\Generic\Simple\SalePriceEffectiveDate',
            []
        );
    }

    public function testMap()
    {
        $this->model->addAdapter($this->adapterMock);

        $dateMock = new \DateTime(null, new \DateTimeZone('America/Chicago'));
        $dateMock->setTimestamp(1451649600);

        $this->expectReturn($this->adapterMock, 'getSalePriceEffectiveDates',
            ['start' => $dateMock, 'end'   => $dateMock]
        );

        $params = ['column' => 'test'];
        $cell = $this->model->map($params);
        $this->assertEquals('2016-01-01T06:00:00-06:00/2016-01-01T06:00:00-06:00', $cell);
    }

}