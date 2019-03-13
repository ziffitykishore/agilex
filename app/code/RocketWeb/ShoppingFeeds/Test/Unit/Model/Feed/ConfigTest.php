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


namespace RocketWeb\ShoppingFeeds\Test\Unit\Model\Feed;

use Magento\Framework\TestFramework\Unit\Helper\ObjectManager as ObjectManagerHelper;

/**
 * Class ConfigTest
 */
class ConfigTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \RocketWeb\ShoppingFeeds\Model\Feed\Config
     */
    protected $model;

    /**
     * @var \Magento\Framework\Json\EncoderInterface|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $jsonEncoderMock;

    /**
     * @var \Magento\Framework\Json\DecoderInterface|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $jsonDecoderMock;

    /**
     * @var ObjectManagerHelper
     */
    protected $objectManagerHelper;

    public function setUp()
    {
        $eventManager = $this->getMock('Magento\Framework\Event\ManagerInterface', [], [], '', false);
        $eventManager->expects($this->any())
            ->method('dispatch')
            ->will($this->returnSelf());

        $contextMock = $this->getMock('Magento\Framework\Model\Context', [], [], '', false);
        $contextMock->expects($this->any())
            ->method('getEventDispatcher')
            ->will($this->returnValue($eventManager));
        $registryMock = $this->getMock('Magento\Framework\Registry');
        $resource = $this->getMock('Magento\Review\Model\ResourceModel\Review', [], [], '', false);
        $resourceCollection = $this->getMock('Magento\Framework\Data\Collection\AbstractDb', [], [], '', false);

        $this->jsonEncoderMock = $this->getMockBuilder('Magento\Framework\Json\EncoderInterface')
            ->disableOriginalConstructor()
            ->setMethods(['encode'])
            ->getMock();

        $this->jsonDecoderMock = $this->getMockBuilder('Magento\Framework\Json\DecoderInterface')
            ->disableOriginalConstructor()
            ->setMethods(['decode'])
            ->getMock();

        $this->objectManagerHelper = new ObjectManagerHelper($this);
        $this->model = $this->objectManagerHelper->getObject(
            'RocketWeb\ShoppingFeeds\Model\Feed\Config', [
                'context' => $contextMock,
                'registry' => $registryMock,
                'jsonEncoder' => $this->jsonEncoderMock,
                'jsonDecoder' => $this->jsonDecoderMock,
                'resource' => $resource,
                'resourceCollection' => $resourceCollection
            ]
        );
    }


    public function testBeforeSave()
    {
        $this->model->setData('value', ['test' => 'value']);

        $this->jsonEncoderMock->expects($this->any())
            ->method('encode')
            ->will($this->returnValue('new_value'));

        $expected = 'new_value';
        $this->model->beforeSave();
        $this->assertEquals($expected, $this->model->getData('value'));
    }

    public function testAfterLoad()
    {
        $this->model->setData('value', '[encodedStuff]');

        $this->jsonDecoderMock->expects($this->any())
            ->method('decode')
            ->will($this->returnValue('new_value'));

        $expected = 'new_value';
        $this->model->afterLoad();
        $this->assertEquals($expected, $this->model->getData('value'));
    }
}
