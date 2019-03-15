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

use RocketWeb\ShoppingFeeds\Model\Exception as FeedException;
use RocketWeb\ShoppingFeeds\Model\Logger;

class Process
{
    const XML_PATH_ENABLED = 'shoppingfeeds/general/cron_enabled';

    /**
     * @var \Magento\Framework\App\Filesystem\DirectoryList
     */
    protected $directoryList;

    /**
     * @var \Magento\Framework\Filesystem\Io\File
     */
    protected $file;

    /**
     * @var resource
     */
    protected $lockFile;

    /**
     * @var Logger
     */
    protected $logger;

    /**
     * @var \RocketWeb\ShoppingFeeds\Model\ResourceModel\Generator\Queue\Collection
     */
    protected $queueCollection;

    /**
     * @var int feedId to force generation of that feed.
     */
    protected $feedId = null;

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
        \Magento\Framework\App\Filesystem\DirectoryList $directoryList,
        \Magento\Framework\Filesystem\Io\File $file,
        \RocketWeb\ShoppingFeeds\Model\Logger $logger,
        \RocketWeb\ShoppingFeeds\Model\ResourceModel\Generator\Queue\Collection $queueCollection,
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig
    )
    {
        $this->directoryList = $directoryList;
        $this->file = $file;
        $this->logger = $logger;
        $this->queueCollection = $queueCollection;
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
     * Process feeds from queue
     *
     * @return void
     */
    public function execute()
    {
        if (!$this->isEnabled()) return;

        if (is_null($this->feedId)) {
            $queue = $this->queueCollection->getQueue();
        } else {
            $queue = $this->queueCollection->getQueue($this->feedId);
            if (!$queue->getFeedId()) {
                $queue->setFeedId($this->feedId);
            }
        }

        if ($queue->getFeedId()) {
            $this->feedId = $queue->getFeedId();

            if ($this->acquireLock()) {
                $queue->setRunning();
                $generator = $queue->getGenerator();
                try {
                    $generator->run();
                } catch (FeedException $e) {
                    // Limits reached, batch has been set, do nothing here.
                    $this->logger->error($e->getMessage());
                } catch (\Exception $e) {
                    $generator->updateStatus(\RocketWeb\ShoppingFeeds\Model\Feed\Source\Status::STATUS_ERROR);
                    $this->logger->error($e->getMessage());
                }
                $this->releaseLock();
            }
        } else {
            $this->logger->debug('Nothing in queue.');
        }
    }

    /**
     * Fore feed generation for $feedId
     *
     * @param $feedId
     */
    public function setFeedId($feedId)
    {
        $this->feedId = $feedId;
        return $this;
    }

    public function setDetached()
    {
        $this->detached = true;
        return $this;
    }

    protected function getLockFile()
    {
        $id = !is_null($this->feedId) ? $this->feedId : 0;
        return $this->directoryList->getPath('tmp') . '/rsf_feed_'. $id . '.lock';
    }

    /**
     * Uses flock to lock generation of a feed in one process
     *
     * @return bool
     */
    public function acquireLock()
    {
        $lockFile = $this->getLockFile();
        $this->lockFile = @fopen($lockFile, "w");

        if (!$this->file->fileExists($lockFile)) {
            $this->logger->error(sprintf('Can\'t create lock file %s, grant write permissions!', $lockFile));
            return false;
        }

        // If the location is not writable, flock() does not work and it doesn't mean another script instance is running
        if (!$this->file->isWriteable($lockFile)) {
            $this->logger->error(sprintf('Path %s is not writable, grant write permissions!', $lockFile));
            return false;
        }

        if (empty($this->lockFile) || !flock($this->lockFile, LOCK_EX | LOCK_NB)) {
            $this->logger->debug(sprintf('Another process is generating the feed! Remove %s to continue.', $lockFile));
            return false;
        }

        ftruncate($this->lockFile, 0); // truncate file
        fwrite($this->lockFile, date('Y-m-d H:i:s'));
        fflush($this->lockFile); // flush output before releasing the lock
        return true;
    }

    /**
     * @return $this
     */
    public function releaseLock()
    {
        flock($this->lockFile, LOCK_UN);
        return $this;
    }
}
