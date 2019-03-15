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

require_once dirname(dirname(__FILE__)) . '/_files/global_mock_functions.php';
/**
 * Class MemoryTest
 */
class MemoryTest extends \PHPUnit_Framework_TestCase
{
    /**
     * Value to mock ini_get('memory_limit') and memory_get_usage()
     *
     * @var string
     */
    public static $returnNormalLimit = false;
    
    /**
     * @var \RocketWeb\ShoppingFeeds\Model\Generator\Memory
     */
    protected $model;

    /**
     * @var ObjectManagerHelper
     */
    protected $objectManagerHelper;

    /**
     * @var
     */
    protected $dateTimeMock;

    public function setUp()
    {
        $this->dateTimeMock = $this->getMock('Magento\Framework\Stdlib\DateTime\DateTime', ['timestamp'], [], '', false);
        $this->objectManagerHelper = new ObjectManagerHelper($this);

        $this->model = $this->objectManagerHelper->getObject(
            'RocketWeb\ShoppingFeeds\Model\Generator\Memory', [
                'dateTime' => $this->dateTimeMock
            ]
        );
    }

    public function testIsCloseToPhpTimeLimit()
    {
        $this->dateTimeMock->expects($this->any())
            ->method('timestamp')
            ->will($this->returnValue(20));

        $this->assertEquals(true, $this->model->isCloseToPhpLimit(10));
    }

    public function testIsCloseToPhpMemoryLimit()
    {
        $this->assertEquals(true, $this->model->isCloseToPhpLimit(0));
    }

    public function testIsCloseToPhpLimitPass()
    {
        self::$returnNormalLimit = true;
        $this->assertEquals(false, $this->model->isCloseToPhpLimit(0));
        self::$returnNormalLimit = false;
    }
}
