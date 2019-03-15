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
namespace RocketWeb\ShoppingFeeds\Cron;

use RocketWeb\ShoppingFeeds\Model\FeedFactory;
use RocketWeb\ShoppingFeeds\Model\Generator\BatchFactory;
use RocketWeb\ShoppingFeeds\Model\ResourceModel\Feed\Schedule\CollectionFactory as ScheduleCollectionFactory;
use RocketWeb\ShoppingFeeds\Model\ResourceModel\Generator\Queue\CollectionFactory as QueueCollectionFactory;

class Schedule
{
    const XML_PATH_ENABLED = 'shoppingfeeds/general/cron_enabled';

    /**
     * @var \RocketWeb\ShoppingFeeds\Model\Generator\BatchFactory
     */
    protected $batchFactory;

    /**
     * @var int
     */
    protected $counter = 0;

    /**
     * @var FeedFactory
     */
    protected $feedFactory;

    /**
     * @var ScheduleCollectionFactory
     */
    protected $scheduleCollectionFactory;

    /**
     * @var QueueCollectionFactory
     */
    protected $queueCollectionFactory;

    /**
     * @var \Magento\Framework\App\Config\ScopeConfigInterface
     */
    protected $scopeConfig;

    /**
     * @var bool.
     * Is set to you when process is initiated through console and not magento's cron
     */
    protected $detached = false;


    public function __construct(
        \RocketWeb\ShoppingFeeds\Model\Generator\BatchFactory $batchFactory,
        \RocketWeb\ShoppingFeeds\Model\FeedFactory $feedFactory,
        \RocketWeb\ShoppingFeeds\Model\ResourceModel\Feed\Schedule\CollectionFactory $scheduleCollectionFactory,
        \RocketWeb\ShoppingFeeds\Model\ResourceModel\Generator\Queue\CollectionFactory $queueCollectionFactory,
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig
    )
    {
        $this->batchFactory = $batchFactory;
        $this->feedFactory = $feedFactory;
        $this->scheduleCollectionFactory = $scheduleCollectionFactory;
        $this->queueCollectionFactory = $queueCollectionFactory;
        $this->scopeConfig = $scopeConfig;
    }

    /**
     * @return bool
     */
    public function isEnabled()
    {
        return (bool) ($this->scopeConfig->getValue(self::XML_PATH_ENABLED)) || $this->detached;
    }

    /**
     * Add feeds which should be generated to queue
     *
     * @return void
     */
    public function execute()
    {
        if (!$this->isEnabled()) return;

        $queueCollection = $this->queueCollectionFactory->create();

        // Get Timezone directly from ObjectManager, as injection dependency fails with magento EE console installation
        $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
        $localeDate = $objectManager->create('\Magento\Framework\Stdlib\DateTime\TimezoneInterface');
        /** @var \DateTime $dateObject */
        $dateObject = $localeDate->date();

        $yesterday = clone $dateObject;
        $yesterday->setTime(0,0);
        $dateTimeFormat = \Magento\Framework\DB\Adapter\Pdo\Mysql::DATETIME_FORMAT;

        $scheduleCollection = $this->scheduleCollectionFactory->create();
        $scheduleCollection->setHourFilter($dateObject->format('H'))
            ->setDateFilter($yesterday->format($dateTimeFormat));

        /** @var \RocketWeb\ShoppingFeeds\Model\Feed\Schedule $schedule */
        foreach ($scheduleCollection as $schedule) {
            $queue = $queueCollection->getQueue($schedule->getFeedId());
            if (!$queue->getId()) {
                $feed = $this->feedFactory->create()
                    ->load($schedule->getFeedId());
                $queue->setFeedId($schedule->getFeedId());
                if ($schedule->getBatchMode()) {
                    $batch = $this->batchFactory->create();
                    $batch->setEnabled(true)
                        ->setLimit($schedule->getBatchLimit())
                        ->setOffset(0);
                    $queue->setBatch($batch);
                }
                // Add new queue for process
                $queue->save();
                $feed->saveStatus(\RocketWeb\ShoppingFeeds\Model\Feed\Source\Status::STATUS_PENDING);
                $schedule->setProcessedAt($dateObject->format($dateTimeFormat));
                $schedule->save();
                $this->counter++;
            }
        }
    }

    public function getCounter()
    {
        return $this->counter;
    }

    public function setDetached()
    {
        $this->detached = true;
        return $this;
    }
}
