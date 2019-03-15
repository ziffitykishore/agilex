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


namespace RocketWeb\ShoppingFeeds\Test\Integration\Model\Generator;

/**
 * Class QueueTest
 */
class QueueTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var int
     */
    protected $id;

    protected $feed;

    /**
     * @magentoDbIsolation enabled
     */
    protected function setUp()
    {
        $storeId = \Magento\TestFramework\Helper\Bootstrap::getObjectManager()
            ->get('Magento\Store\Model\StoreManagerInterface')
            ->getStore()->getId();

        $this->feed = \Magento\TestFramework\Helper\Bootstrap::getObjectManager()
            ->create('RocketWeb\ShoppingFeeds\Model\Feed');
        $this->feed->setData([
            'store_id' => $storeId,
            'name' => 'Test Feed',
            'type' => 'generic',
            'status' => 1,
            'message' => ['message1' => 'Message 1', 'message2' => 'Message 2'],
        ]);
        $this->feed->save();
        $feedId = $this->feed->getId();

        $queue = \Magento\TestFramework\Helper\Bootstrap::getObjectManager()
            ->create('RocketWeb\ShoppingFeeds\Model\Generator\Queue');

        $queue->add($this->feed);
        $this->id = $queue->getId();
    }

    /**
     * @magentoDbIsolation enabled
     */
    public function testQueueLoad()
    {
        /** @var \RocketWeb\ShoppingFeeds\Model\Generator\Queue $queue */
        $queue = \Magento\TestFramework\Helper\Bootstrap::getObjectManager()
            ->create('RocketWeb\ShoppingFeeds\Model\Generator\Queue');
        $queue->load($this->id);

        $this->assertEquals($this->id, $queue->getId());
    }

    /**
     * @magentoDbIsolation enabled
     */
    public function testAdd()
    {
        $queue = \Magento\TestFramework\Helper\Bootstrap::getObjectManager()
            ->create('RocketWeb\ShoppingFeeds\Model\Generator\Queue');
        $queue->load($this->id);
        $queue->add($this->feed);

        $this->assertNotEquals($this->id, $queue->getId());
    }
}
