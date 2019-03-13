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


namespace RocketWeb\ShoppingFeeds\Test\Unit\Model\Product\Adapter;

use Magento\Framework\DataObject;
use Magento\Framework\TestFramework\Unit\Helper\ObjectManager as ObjectManagerHelper;

/**
 * Class AdapterFactoryTest
 */
class AdapterFactoryTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \RocketWeb\ShoppingFeeds\Model\Product\Adapter\AdapterFactory
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
            'RocketWeb\ShoppingFeeds\Model\Product\Adapter\AdapterFactory', [
                'objectManager' => $this->objectManagerMock
            ]
        );
    }

    public function testCreate()
    {
        /** @var \Magento\Catalog\Model\Product|\PHPUnit_Framework_MockObject_MockObject $productMock */
        $productMock = $this->getMockBuilder('Magento\Catalog\Model\Product')
            ->disableOriginalConstructor()
            ->setMethods(['getTypeId'])
            ->getMock();

        $productMock->expects($this->any())
            ->method('getTypeId')
            ->will($this->returnValue('simple'));

        /** @var \Rocketweb\ShoppingFeeds\Model\Feed|\PHPUnit_Framework_MockObject_MockObject $feedMock */
        $feedMock = $this->getMock('Rocketweb\ShoppingFeeds\Model\Feed', [], [], '', false);


        $this->objectManagerMock->expects($this->any())
            ->method('create')
            ->will($this->returnCallback(
                function($className, $data) {return new DataObject(['class_name' => $className]);}
            ));

        $adapter = $this->model->create($productMock, $feedMock);
        $this->assertEquals(
            'RocketWeb\ShoppingFeeds\Model\Product\Adapter\Type\Simple',
            $adapter->getClassName()
        );

        $adapter = $this->model->create($productMock, $feedMock);
        $this->assertEquals(
            null,
            $adapter->getClassName()
        );

        $adapter = $this->model->create($productMock, $feedMock, false);
        $this->assertEquals(
            'RocketWeb\ShoppingFeeds\Model\Product\Adapter\Type\Simple',
            $adapter->getClassName()
        );
    }

}