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

namespace RocketWeb\ShoppingFeeds\Model\Feed;

use RocketWeb\ShoppingFeeds\Model\Feed\Source\Status;

class Copier
{
    /**
     * @var \RocketWeb\ShoppingFeeds\Model\FeedFactory
     */
    protected $feedFactory;

    /**
     * @param \RocketWeb\ShoppingFeeds\Model\FeedFactory $feedFactory
     */
    public function __construct(
        \RocketWeb\ShoppingFeeds\Model\FeedFactory $feedFactory
    ){
        $this->feedFactory = $feedFactory;
    }

    /**
     * Create feed duplicate
     *
     * @param \RocketWeb\ShoppingFeeds\Model\Feed $feed
     * @return \RocketWeb\ShoppingFeeds\Model\Feed
     */
    public function copy(\RocketWeb\ShoppingFeeds\Model\Feed $feed)
    {
        $duplicate = $this->feedFactory->create();
        $duplicate->setData($feed->getData());
        // clear messages for newly cloned feed
        $duplicate->setData('messages','');
        $duplicate->setName($duplicate->getName() . '_clone');
        $duplicate->setId(null);
        $duplicate->setCreatedAt(null);
        $duplicate->setUpdatedAt(null);

        $schedules = $feed->getSchedules();
        if (!empty($schedules)) {
            foreach ($schedules as $index => &$schedule) {
                // remove 'id' key so that the schedule is recognized as a new entity in Feed::saveSchedules()
                unset($schedule['id']);
            }
            $duplicate->setData('schedules', $schedules);
        }

        $uploads = $feed->getUploads();
        if (!empty($uploads)) {
            foreach ($uploads as $index => &$upload) {
                unset($upload['id']);
            }
            $duplicate->setData('uploads', $uploads);
        }

        $duplicate->setStatus(Status::STATUS_DISABLED);
        if ($feed->getConfig()) {
            $duplicate->setConfig($feed->getConfig());
        }

        $duplicate->save();
        return $duplicate;
    }
}
