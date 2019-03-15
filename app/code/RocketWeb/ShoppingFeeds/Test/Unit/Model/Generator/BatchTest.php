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


namespace RocketWeb\ShoppingFeeds\Test\Unit\Model\Generator;

use Magento\Framework\TestFramework\Unit\Helper\ObjectManager as ObjectManagerHelper;

/**
 * Class BatchTest
 */
class BatchTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \RocketWeb\ShoppingFeeds\Model\Generator\Batch
     */
    protected $model;

    /**
     * @var ObjectManagerHelper
     */
    protected $objectManagerHelper;

    public function setUp()
    {
        $this->objectManagerHelper = new ObjectManagerHelper($this);
        $this->model = $this->objectManagerHelper->getObject(
            'RocketWeb\ShoppingFeeds\Model\Generator\Batch', []
        );
    }

    public function testGetLimit()
    {
        // Test default value
        $this->assertEquals(1000, $this->model->getLimit());

        $this->model->setData('limit', 500);
        $this->assertEquals(500, $this->model->getLimit());
    }

    public function testIsNew()
    {
        // Test default value
        $this->assertEquals(true, $this->model->isNew());

        $this->model->setData('offset', 0);
        $this->assertEquals(true, $this->model->isNew());

        $this->model->setData('offset', 10);
        $this->assertEquals(false, $this->model->isNew());
    }

    public function testIsEnabled()
    {
        // Test default value
        $this->assertEquals(false, $this->model->isEnabled());

        $this->model->setData('enabled', false);
        $this->assertEquals(false, $this->model->isEnabled());

        $this->model->setData('enabled', true);
        $this->assertEquals(true, $this->model->isEnabled());
    }

    public function testGetOffset()
    {
        // Test default value
        $this->assertEquals(0, $this->model->getOffset());

        $this->model->setData('offset', 10);
        $this->assertEquals(10, $this->model->getOffset());
    }
}
