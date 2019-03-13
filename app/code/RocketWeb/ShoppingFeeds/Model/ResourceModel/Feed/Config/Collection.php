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


namespace RocketWeb\ShoppingFeeds\Model\ResourceModel\Feed\Config;

use Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection;

class Collection extends AbstractCollection
{

    /**
     * Define resource model
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init(
            'RocketWeb\ShoppingFeeds\Model\Feed\Config',
            'RocketWeb\ShoppingFeeds\Model\ResourceModel\Feed\Config'
        );
    }

    /**
     * Add feed filter
     *
     * @param \RocketWeb\ShoppingFeeds\Model\Feed $feed
     * @return $this
     */
    public function setFeedFilter(\RocketWeb\ShoppingFeeds\Model\Feed $feed)
    {
        $this->addFieldToFilter('feed_id', $feed->getId());

        return $this;
    }

    /**
     * Used to emulate after load functionality for each item without loading them
     *
     * @return $this
     */
    protected function _afterLoad()
    {
        $this->walk('afterLoad');

        return $this;
    }
}