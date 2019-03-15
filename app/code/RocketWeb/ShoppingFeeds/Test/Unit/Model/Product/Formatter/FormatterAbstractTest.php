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


namespace RocketWeb\ShoppingFeeds\Test\Unit\Model\Product\Formatter;

use Magento\Framework\TestFramework\Unit\Helper\ObjectManager as ObjectManagerHelper;
use RocketWeb\ShoppingFeeds\Test\Unit\Model\ModelFramework;

/**
 * Class MapperAbstractTest
 */
class FormatterAbstractTest extends ModelFramework
{
    /**
     * @var \RocketWeb\ShoppingFeeds\Model\Product\Formatter\FormatterAbstract
     */
    protected $model;

    /**
     * @var ObjectManagerHelper
     */
    protected $objectManagerHelper;

    public function setUp()
    {
        parent::setUp();
        $this->objectManagerHelper = new ObjectManagerHelper($this);

        $this->model = $this->objectManagerHelper->getObject(
            'RocketWeb\ShoppingFeeds\Model\Product\Formatter\Price\NoCurrency', []
        );
    }

    public function testFormatter()
    {
        $this->expectReturn($this->adapterMock, 'getData', 'my_adapter');

        $this->model->setAdapter($this->adapterMock);
        $this->assertEquals('my_adapter', $this->model->getAdapter()->getData());

        $this->assertEmpty($this->model->run(''));
        $this->assertEmpty($this->model->run('something'));
        $this->assertEquals(6.40, $this->model->run('6.4'));
        $this->assertEquals(1.00, $this->model->run(1));
    }
}