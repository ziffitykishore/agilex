<?php

namespace RocketWeb\ShoppingFeeds\Model\Generator;

use Magento\Framework\Model\AbstractModel;
use RocketWeb\ShoppingFeeds\Model\Feed;
use RocketWeb\ShoppingFeeds\Model\FeedFactory;
use RocketWeb\ShoppingFeeds\Model\Generator;

/**
 * Class Queue
 * @package RocketWeb\ShoppingFeeds\Model\Generator
 *
 * @method  $this   setFeedId(int $feedId)
 * @method  int     getFeedId()
 * @method  $this   setIsRead(boolean $isRead)
 */
class Queue extends AbstractModel
{
    /**
     * @var \RocketWeb\ShoppingFeeds\Model\Generator\Batch
     */
    protected $batch;

    /**
     * @var \RocketWeb\ShoppingFeeds\Model\Generator\Factory
     */
    protected $generatorFactory;

    protected $feedFactory;

    public function __construct(
        \Magento\Framework\Model\Context $context,
        \Magento\Framework\Registry $registry,
        \RocketWeb\ShoppingFeeds\Model\Generator\BatchFactory $batchFactory,
        \RocketWeb\ShoppingFeeds\Model\FeedFactory $feedFactory,
        \RocketWeb\ShoppingFeeds\Model\Generator\Factory $generatorFactory,
        \Magento\Framework\Model\ResourceModel\AbstractResource $resource = null,
        \Magento\Framework\Data\Collection\AbstractDb $resourceCollection = null,
        array $data = []
    )
    {
        $this->batch = $batchFactory->create();
        $this->generatorFactory = $generatorFactory;
        $this->feedFactory = $feedFactory;

        parent::__construct($context, $registry, $resource, $resourceCollection, $data);
    }

    /**
     * Initialize resource model
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init('RocketWeb\ShoppingFeeds\Model\ResourceModel\Generator\Queue');
    }

    /**
     * @return \RocketWeb\ShoppingFeeds\Model\Generator
     */
    public function getGenerator()
    {
        $feed = $this->feedFactory->create()->load($this->getFeedId());
        return $this->generatorFactory->create($feed, $this);
    }

    public function setRunning()
    {
        if ($this->getId()) {
            $this->setIsRead(true);
            $this->save();
        }
        return $this;
    }

    /**
     * @param \RocketWeb\ShoppingFeeds\Model\Feed|int $feed
     * @param \RocketWeb\ShoppingFeeds\Model\
     * @return $this
     */
    public function add($feed, $schedule = null) {
        if ($feed instanceof \RocketWeb\ShoppingFeeds\Model\Feed) {
            $feed = $feed->getId();
        }

        if ($this->getId()) {
            $this->setId(null);
        }

        $data = [
            'feed_id'       => $feed,
            'is_read'       => 0
        ];
        if (!is_null($schedule) && !is_null($schedule->getId())) {
            $data['schedule_id'] = $schedule->getId();
        }

        $this->addData($data);
        $this->save();

        return $this;
    }
    
    protected function _afterLoad()
    {
        $message = $this->getMessage();
        if (!empty($message) && !is_array($message)) {
            $message = unserialize($message);
            $this->setMessage($message);
        }

        if (is_array($message)) {
            $this->batch->addData($message);
        }

        return parent::_afterLoad();
    }

    public function beforeSave()
    {
        $message = $this->getMessage();
        $batch = $this->batch->getData();
        if (is_array($batch)) {
            $message = $batch;
        }

        if (is_array($message)) {
            $message = serialize($message);
            $this->setMessage($message);
        }

        return parent::beforeSave();
    }

    /**
     * @return Batch
     */
    public function getBatch()
    {
        return $this->batch;
    }

    /**
     * @param Batch $batch
     * @return $this
     */
    public function setBatch(Batch $batch)
    {
        $this->batch = $batch;
        return $this;
    }
}