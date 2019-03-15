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


namespace RocketWeb\ShoppingFeeds\Test\Unit\Model\Logger;

use Magento\Framework\TestFramework\Unit\Helper\ObjectManager as ObjectManagerHelper;

/**
 * Class HandlerFactoryTest
 */
class HandlerFactoryTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \RocketWeb\ShoppingFeeds\Model\Logger\HandlerFactory
     */
    protected $model;

    /**
     * @var \Magento\Framework\ObjectManagerInterface|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $objectManagerMock;

    /**
     * @var ObjectManagerHelper
     */
    protected $objectManagerHelper;

    public function setUp()
    {
        $this->objectManagerHelper = new ObjectManagerHelper($this);

        $this->objectManagerMock = $this->getMockBuilder('Magento\Framework\ObjectManagerInterface')
            ->disableOriginalConstructor()
            ->setMethods(['create', 'get', 'configure'])
            ->getMock();

        $this->model = $this->objectManagerHelper->getObject(
            'RocketWeb\ShoppingFeeds\Model\Logger\HandlerFactory', [
                'objectManager' => $this->objectManagerMock
            ]
        );
    }

    public function testCreate()
    {
        $this->objectManagerMock->expects($this->any())
            ->method('create')
            ->will($this->returnCallback(
                function($className, $data) { return new \Magento\Framework\DataObject($data);}
            ));
        $filePath = '/test.txt';

        $expected = [
            'filePath' => BP . $filePath,
            'level' => \Monolog\Logger::INFO,
            'formatter' => new \RocketWeb\ShoppingFeeds\Model\Logger\Formatter\DefaultLog()
        ];

        /** @var \Magento\Framework\DataObject $actual */
        $actual = $this->model->create($filePath);

        $this->assertEquals($expected, $actual->toArray());
    }
}