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


namespace RocketWeb\ShoppingFeeds\Test\Integration\Model;

/**
 * Class FeedTest
 */
class FeedTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var int
     */
    protected $id;

    /**
     * @magentoDbIsolation enabled
     */
    protected function setUp()
    {
        $storeId = \Magento\TestFramework\Helper\Bootstrap::getObjectManager()
            ->get('Magento\Store\Model\StoreManagerInterface')
            ->getStore()->getId();

        $feed = \Magento\TestFramework\Helper\Bootstrap::getObjectManager()
            ->create('RocketWeb\ShoppingFeeds\Model\Feed');
        $feed->setData([
            'store_id' => $storeId,
            'name' => 'Test Feed',
            'type' => 'generic',
            'status' => 1,
            'messages' => ['message1' => 'Message 1', 'message2' => 'Message 2'],
        ]);
        $feed->save();
        $this->id = $feed->getId();
    }

    /**
     * @magentoDbIsolation enabled
     */
    public function testFeedLoad()
    {
        $feed = \Magento\TestFramework\Helper\Bootstrap::getObjectManager()
            ->create('RocketWeb\ShoppingFeeds\Model\Feed');
        $feed->load($this->id);

        $this->assertEquals('Test Feed', $feed->getName());
    }

    /**
     * @magentoDbIsolation enabled
     */
    public function testFeedLoadMessages()
    {
        $feed = \Magento\TestFramework\Helper\Bootstrap::getObjectManager()
            ->create('RocketWeb\ShoppingFeeds\Model\Feed');
        $feed->load($this->id);

        $this->assertEquals(['message1' => 'Message 1', 'message2' => 'Message 2'], $feed->getMessages());
    }
}
