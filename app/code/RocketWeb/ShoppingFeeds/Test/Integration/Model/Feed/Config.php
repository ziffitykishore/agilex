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


namespace RocketWeb\ShoppingFeeds\Test\Integration\Model\Feed;

/**
 * Class FeedTest
 */
class ConfigTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var int
     */
    protected $configId;

    /**
     * @var array
     */
    protected $configValue = array('some' => 'array');

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
            'messages' => '',
        ]);
        $feed->save();

        $config = \Magento\TestFramework\Helper\Bootstrap::getObjectManager()
            ->create('RocketWeb\ShoppingFeeds\Model\Feed\Config');
        $config->setData([
            'feed_id' => $feed->getId(),
            'path' => 'config_path',
            'value' => $this->configValue
        ]);
        $config->save();
        $this->configId = $config->getId();
    }

    /**
     * @magentoDbIsolation enabled
     */
    public function testConfigLoad()
    {
        $config = \Magento\TestFramework\Helper\Bootstrap::getObjectManager()
            ->create('RocketWeb\ShoppingFeeds\Model\Feed\Config');
        $config->load($this->configId);

        $this->assertEquals('config_path', $config->getPath());
        $this->assertEquals($this->configValue, $config->getValue());
    }
}
