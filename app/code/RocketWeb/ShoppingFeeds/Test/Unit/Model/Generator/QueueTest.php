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
 * Class QueueTest
 */
class QueueTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \RocketWeb\ShoppingFeeds\Model\Generator\Queue
     */
    protected $model;

    /**
     * @var ObjectManagerHelper
     */
    protected $objectManagerHelper;

    protected $generatorMock;

    public function setUp()
    {
        $this->objectManagerHelper = new ObjectManagerHelper($this);

        $this->generatorMock = $this->getMock('RocketWeb\ShoppingFeeds\Model\Generator', [], [], '', false);

        $generatorFactoryMock = $this->getMock('RocketWeb\ShoppingFeeds\Model\Generator\Factory', ['create'], [], '', false);
        $generatorFactoryMock->expects($this->any())
            ->method('create')
            ->will($this->returnValue($this->generatorMock));
        
        $feedFactoryMock = $this->getMock('RocketWeb\ShoppingFeeds\Model\FeedFactory', ['create', 'load'], [], '', false);
        $feedFactoryMock->expects($this->any())
            ->method('create')
            ->will($this->returnSelf());

        $batchFactory = $this->getMock('RocketWeb\ShoppingFeeds\Model\Generator\BatchFactory', ['create'], [], '', false);
        $batchFactory->expects($this->any())
            ->method('create')
            ->will($this->returnSelf());

        $this->model = $this->objectManagerHelper->getObject(
            'RocketWeb\ShoppingFeeds\Model\Generator\Queue', [
                'generatorFactory'  => $generatorFactoryMock,
                'feedFactory'       => $feedFactoryMock,
                'batchFactory'      => $batchFactory
            ]
        );
    }

    public function testGetGenerator()
    {
        $this->assertEquals($this->generatorMock, $this->model->getGenerator());
    }

    public function testSetGetBatch()
    {
        $batch = $this->getMock('RocketWeb\ShoppingFeeds\Model\Generator\Batch', [], [], '', false);

        $this->model->setBatch($batch);
        $this->assertEquals($batch, $this->model->getBatch());
    }
}
