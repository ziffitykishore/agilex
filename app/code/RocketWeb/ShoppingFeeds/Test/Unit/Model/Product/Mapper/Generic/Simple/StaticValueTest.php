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
 * Class StaticValueTest
 * @package RocketWeb\ShoppingFeeds\Test\Unit\Model\Product\Mapper\Generic\Simple
 */
class StaticValueTest extends ModelFramework
{
    /**
     * @var \RocketWeb\ShoppingFeeds\Model\Product\Mapper\Generic\Simple\StaticValue
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
            'RocketWeb\ShoppingFeeds\Model\Product\Mapper\Generic\Simple\StaticValue',
            []
        );
        $this->model->addAdapter($this->adapterMock);
    }

    public function testMap()
    {
        $params = ['param' => 'Static Value'];
        $cell = $this->model->map($params);
        $this->assertEquals('Static Value', $cell);
    }

}