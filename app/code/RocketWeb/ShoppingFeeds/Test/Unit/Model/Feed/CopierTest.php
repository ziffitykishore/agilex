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


namespace RocketWeb\ShoppingFeeds\Test\Unit\Model;

use Magento\Framework\TestFramework\Unit\Helper\ObjectManager as ObjectManagerHelper;

/**
 * Class CopierTest
 */
class CopierTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \RocketWeb\ShoppingFeeds\Model\Feed\Copier
     */
    protected $_model;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $feedFactoryMock;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $feedMock;

    /** 
     * @var ObjectManagerHelper 
     */
    protected $objectManagerHelper;

    /**
     * @var array 
     */
    protected $feedData = [
        'id'        => 100, 
        'name'      => 'Foo Feed', 
        'store_id'  => 2,
        'type'      => 'generic',
    ];

    /**
     * @var array 
     */
    protected $feedConfigData = [
        'config_path1'  => 'value1',
        'config_path2'  => 'value2',
    ];

    protected function setUp()
    {
        $this->objectManagerHelper = new ObjectManagerHelper($this);

        // Prepare feed factory mock
        $this->feedFactoryMock = $this->getMock(
            '\RocketWeb\ShoppingFeeds\Model\FeedFactory',
            ['create'],
            [],
            '',
            false
        );

        // Prepare feed mock
        $this->feedMock = $this->getMock('\RocketWeb\ShoppingFeeds\Model\Feed', [], [], '', false);
        $this->feedMock->expects($this->any())
            ->method('getData')
            ->will($this->returnValue($this->feedData));
        $this->feedMock->expects($this->any())
            ->method('getConfig')
            ->will($this->returnValue($this->feedConfigData));

        $this->_model = $this->objectManagerHelper->getObject(
            'RocketWeb\ShoppingFeeds\Model\Feed\Copier',
            ['feedFactory' => $this->feedFactoryMock]
        );
    }

    /**
     * Test copy method
     */
    public function testCopy()
    {
        $duplicateMock = $this->getMock(
            '\RocketWeb\ShoppingFeeds\Model\Feed',
            [
                '__wakeup',
                'setData',
                'setName',
                'getName',
                'setId',
                'setCreatedAt',
                'setUpdatedAt',
                'setSchedule',
                'setStatus',
                'setConfig',
                'setUploads',
                'save',
            ],
            [],
            '',
            false
        );
        $duplicateMock->expects($this->any())
            ->method('save')
            ->will($this->returnSelf());

        $duplicateMock->expects($this->any())
            ->method('getName')
            ->will($this->returnValue($this->feedData['name']));

        $this->feedMock->expects($this->atLeastOnce())->method('getData');
        $this->feedFactoryMock->expects($this->once())->method('create')->will($this->returnValue($duplicateMock));

        $duplicateMock->expects($this->once())->method('setName')->with($this->feedData['name'] . '_clone');
        $duplicateMock->expects($this->once())->method('setId')->with(null);
        $duplicateMock->expects($this->once())->method('setCreatedAt')->with(null);
        $duplicateMock->expects($this->once())->method('setUpdatedAt')->with(null);
        $duplicateMock->expects($this->once())->method('setStatus')
            ->with(\RocketWeb\ShoppingFeeds\Model\Feed\Source\Status::STATUS_DISABLED);
        $duplicateMock->expects($this->once())->method('setConfig')->with($this->feedConfigData);

        $this->assertEquals($duplicateMock, $this->_model->copy($this->feedMock));
    }
}
