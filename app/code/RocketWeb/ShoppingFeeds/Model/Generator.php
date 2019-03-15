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
 * @author    RocketWeb
 */

namespace RocketWeb\ShoppingFeeds\Model;

use Magento\Framework\DataObject;
use RocketWeb\ShoppingFeeds\Model\Exception as FeedException;
use RocketWeb\ShoppingFeeds\Model\Product\Adapter\AdapterFactory;
use RocketWeb\ShoppingFeeds\Model\Product\CollectionProvider;

class Generator extends DataObject
{
    /**
     * @var AdapterFactory
     */
    protected $adapterFactory;

    /**
     * @var Generator\Batch
     */
    protected $batch;

    /**
     * @var \RocketWeb\ShoppingFeeds\Model\Generator\Cache
     */
    protected $cache;

    protected $countProductsExported = 0;
    protected $countProductsSkipped = 0;
    protected $currentIteration = 0;

    /**
     * @var \Magento\Catalog\Model\ResourceModel\Product\Collection
     */
    protected $collection;

    /**
     * @var CollectionProvider
     */
    protected $collectionProvider;

    /**
     * @var \Magento\Framework\Stdlib\DateTime\TimezoneInterface
     */
    protected $dateTime;

    /**
     * @var \Magento\Framework\Filesystem\Driver\File
     */
    protected $fileDriver;

    /**
     * @var Feed
     */
    protected $feed;

    /**
     * @var \Magento\Framework\Model\ResourceModel\Iterator
     */
    protected $iterator;

    /**
     * @var Logger
     */
    protected $logger;

    /**
     * @var Generator\Memory
     */
    protected $memory;

    /**
     * @var \Magento\Catalog\Model\ProductFactory
     */
    protected $productFactory;

    /**
     * @var \RocketWeb\ShoppingFeeds\Model\Uploader\UploaderFactory
     */
    protected $uploaderFactory;

    /**
     * @var \RocketWeb\ShoppingFeeds\Model\ResourceModel\Generator\Process\CollectionFactory
     */
    protected $processCollectionFactory;

    /**
     * @var null
     */
    protected $testSku = null;

    /**
     * @var array
     */
    protected $testOutput = [];

    /**
     * @var Generator\Queue
     */
    protected $queue;

    /**
     * @var Feed\Schedule
     */
    protected $scheduleFactory;

    /**
     * @var \Magento\Framework\Event\ManagerInterface
     */
    protected $eventManager;

    /**
     * @var \Magento\Framework\Filesystem\DirectoryList
     */
    protected $directoryList;

    /**
     * Generator constructor.
     * @param \Magento\Catalog\Model\ProductFactory $productFactory
     * @param \RocketWeb\ShoppingFeeds\Model\Uploader\UploaderFactory $uploaderFactory
     * @param \Magento\Framework\Filesystem\Driver\File $fileDriver
     * @param \Magento\Framework\Model\ResourceModel\Iterator $iterator
     * @param Generator\Cache $cache
     * @param \Magento\Framework\Stdlib\DateTime\TimezoneInterface $dateTime
     * @param CollectionProvider $collectionProvider
     * @param Logger $logger
     * @param AdapterFactory $adapterFactory
     * @param Generator\Batch $batch
     * @param Generator\Memory $memory
     * @param Feed $feed
     * @param Generator\Queue|null $queue
     * @param null $testSku
     * @param array $data
     */
    public function __construct(
        \Magento\Framework\Event\ManagerInterface $eventManager,
        \Magento\Catalog\Model\ProductFactory $productFactory,
        \RocketWeb\ShoppingFeeds\Model\Uploader\UploaderFactory $uploaderFactory,
        \Magento\Framework\Filesystem\Driver\File $fileDriver,
        \Magento\Framework\Model\ResourceModel\Iterator $iterator,
        \RocketWeb\ShoppingFeeds\Model\Generator\Cache $cache,
        \Magento\Framework\Stdlib\DateTime\TimezoneInterface $dateTime,
        \RocketWeb\ShoppingFeeds\Model\Product\CollectionProvider $collectionProvider,
        \RocketWeb\ShoppingFeeds\Model\Logger $logger,
        \RocketWeb\ShoppingFeeds\Model\Product\Adapter\AdapterFactory $adapterFactory,
        \RocketWeb\ShoppingFeeds\Model\Generator\Batch $batch,
        \RocketWeb\ShoppingFeeds\Model\Generator\Memory $memory,
        \RocketWeb\ShoppingFeeds\Model\ResourceModel\Generator\Process\CollectionFactory $processCollectionFactory,
        \RocketWeb\ShoppingFeeds\Model\Feed $feed,
        \RocketWeb\ShoppingFeeds\Model\Generator\Queue $queue = null,
        \RocketWeb\ShoppingFeeds\Model\Feed\ScheduleFactory $scheduleFactory,
        \Magento\Framework\Filesystem\DirectoryList $directoryList,
        $testSku = null,
        $data = []
    )
    {
        $this->eventManager = $eventManager;
        $this->adapterFactory = $adapterFactory;
        $this->batch = $batch;
        $this->cache = $cache;
        $this->collectionProvider = $collectionProvider;
        $this->dateTime = $dateTime;
        $this->feed = $feed;
        $this->fileDriver = $fileDriver;
        $this->iterator = $iterator;
        $this->logger = $logger;
        $this->memory = $memory;
        $this->processCollectionFactory = $processCollectionFactory;
        $this->productFactory = $productFactory;
        $this->uploaderFactory = $uploaderFactory;
        $this->setTestSku($testSku);
        $this->queue = $queue;
        $this->directoryList = $directoryList;
        if (!is_null($queue) && !is_null($queue->getBatch())) {
            $this->batch = $queue->getBatch();
            $this->currentIteration = $this->batch->getOffset();
        }
        $this->scheduleFactory = $scheduleFactory;

        parent::__construct($data);
    }

    /**
     * @return $this
     */
    public function run()
    {
        //ini_set('memory_limit','83M');

        if (!$this->feed->getId()) {
            throw new FeedException(
                new \Magento\Framework\Phrase('Generator must be created using existing feed - no Feed Id found!')
            );
        }

        $this->eventManager->dispatch(sprintf('shoppingfeeds_before_generate_%s', $this->feed->getData('type')), [
            'generator' => $this,
            'feed' => $this->feed
        ]);

        $time = $this->dateTime->date()->getTimestamp();
        $this->setData('started_at', $time);
        $this->memory->setStartUsage();
        $this->updateStatus(\RocketWeb\ShoppingFeeds\Model\Feed\Source\Status::STATUS_PROCESSING);

        $this->getLogger()->info(sprintf('START %s FEED #%s', strtoupper($this->feed->getType()), $this->feed->getId()));
        // Run any custom pre-generation processes
        //$this->runPreHook();


        /** @var \Magento\Catalog\Model\ResourceModel\Product\Collection $collection */
        $collection = $this->getCollection();
        if (!$this->isTestMode() && (!$this->batch->isEnabled() || $this->batch->isNew())){
            /** @var \RocketWeb\ShoppingFeeds\Model\ResourceModel\Generator\Process\Collection $processCollection */
            $processCollection = $this->processCollectionFactory->create();
            $processCollection->truncate($this->feed);
            $this->writeFeed($this->getHeader(), false);
        }

        $this->iterator->walk(
            $collection->getSelect(), [[$this, 'processProductCallback']]
        );

        $addedItems = $this->currentIteration;
        if ($this->batch->isEnabled()) {
            $addedItems = $addedItems - (int)$this->batch->getOffset();
        }

        $this->closeTemporaryHandle()
            ->copyDataFromTemporaryFeedFile();

        $this->getLogger()->debug("---------------------------------------------------------------------");
        $this->getLogger()->info(sprintf('Processed %d products (%s/%s) | Added %d rows, %d skipped | in file %s(.tmp)',
            $addedItems,
            $this->currentIteration,
            $this->getTotalItems(),
            $this->getCountProductsExported(),
            $this->getCountProductsSkipped(),
            $this->getFeedFile()
        ));

        $t = round($this->dateTime->date()->getTimestamp() - $time);
        $this->getLogger()->info(sprintf('FINISHED | MEMORY USAGE: %s | TIME SPENT: %s',
            $this->memory->format(true),
            sprintf('%02d:%02d:%02d', ($t/3600),($t/60%60), $t%60)
        ));
        return $this;
    }

    public function processProductCallback($args)
    {
        $row = $args['row'];

        // Skip if product type is not enabled
        if (!$this->feed->isProductTypeEnabled($row['type_id'])) {
            return false;
        }

        // Prepare product and map object
        try {
            $product = $this->productFactory->create()
                ->setStoreId($this->feed->getStoreId())
                ->load($row['entity_id']);
        } catch (\Exception $e) {
            $this->logger->error(sprintf('Product ID %s - %s', $row['entity_id'], $e->getMessage()));
            return;
        }

        $adapter = $this->adapterFactory->create($product, $this->feed, false);

        if ($adapter === false) {
            $this->updateCountSkip();

            $this->getLogger()->warning(sprintf('Adapter creation failed for type "%s", SKU #%s.',
                $row['type_id'],
                $row['sku']
            ));
        } else {
            $adapter->setData('generator', $this);
            $this->addProductToFeed($adapter);

            $this->getLogger()->debug(sprintf('%s | product added - SKU #%s, ID %s',
                $this->memory->format(),
                $row['sku'],
                $row['entity_id']
                ));
        }
        $this->currentIteration++;
        $this->logProgress();



        if ($this->memory->isCloseToPhpLimit($this->getData('started_at'))) {

            // Automatically swicth to batch mode
            $newLimit = $this->currentIteration - $this->batch->getOffset();

            if (!$this->batch->isEnabled()) {
                $this->getLogger()->info('Automatic switch to batch mode.');

                // Persist batch state in the schedule
                $schedules = $this->feed->getSchedules();
                $schedules[0]['batch_mode'] = 1;
                $schedules[0]['batch_limit'] = $newLimit;
                $schedule = $this->scheduleFactory->create()->load($schedules[0]['id']);
                $schedule->setData($schedules[0]);
                $schedule->save();

                // Persist batch state, will be saved in the queue messages
                $this->batch->setData(array('offset' => $this->currentIteration,
                    'limit' => $newLimit,
                    'enabled' => true));
            }

            $this->updateBatchQueue();

            // Exit the iterator but don't set the feed as failed
            throw new FeedException(
                new \Magento\Framework\Phrase('EARLY END / PHP Limits reached')
            );
        }

        unset($product, $productAdapter, $row);
    }

    /**
     * @return \Magento\Catalog\Model\ResourceModel\Product\Collection
     */
    public function getCollection()
    {
        if (is_null($this->collection)) {
            if ($this->isTestMode()) {
                $this->collectionProvider->setTestSku($this->testSku);
            }
            $this->collection = $this->collectionProvider
                ->getCollection($this->feed);

            if ($this->batch->isEnabled()) {
                $limit = $this->batch->getLimit();
                $offset = $this->batch->getOffset();
                $this->collection->getSelect()->limit($limit, $offset);
            }
        }
        return $this->collection;
    }

    /**
     * @return int
     */
    public function getTotalItems()
    {
        if (!$this->hasData('total_items')) {
            if ($this->isTestMode()) {
                $this->collectionProvider->setTestSku($this->testSku);
            }
            $countCollection = $this->collectionProvider->getCollection($this->feed);
            $countCollection->getSelect()->reset(\Magento\Framework\DB\Select::GROUP);
            $this->setData('total_items', $countCollection->getSize());
        }
        return $this->getData('total_items');
    }

    /**
     * @param \RocketWeb\ShoppingFeeds\Model\Product\Adapter\AdapterInterface|\RocketWeb\ShoppingFeeds\Model\Product\Adapter\AdapterAbstract $productAdapter
     * @return $this
     */
    protected function addProductToFeed($productAdapter)
    {
        $rows = [];
        if ($this->isTestMode()) {
            $productAdapter->setTestMode();
        }

        if (!$productAdapter->isSkipped()) {
            $rows = $productAdapter->map();
        }

        if ($productAdapter->getSkipProduct() === true) {
            $this->getLogger()->info(sprintf('Product skipped: %s', $productAdapter->getSkipMessage()));
            $this->updateCountSkip(count($rows));
            return $this;
        }

        foreach ($rows as $row) {
            $this->writeFeed($row);
        }
        return $this;
    }

    /**
     * @param $fields
     * @param bool|true $addNewLine
     * @return Generator
     */
    protected function writeFeed($fields, $addNewLine = true)
    {
        $params = $this->getWriteFeedParams();
        /**
         * @var $defaultValue
         * @var $delimiter
         * @var $encloseCell
         * @var $encloseEscape
         */
        extract($params);
        $row = array();

        // google error: "Too many column delimiters"
        foreach ($this->feed->getColumnsMap() as $arr) {
            $column = $arr['column'];
            $values = isset($fields[$column]) ? $fields[$column] : '';
            if (!is_array($values)) {
                $values = array($values);
            }

            foreach ($values as $value) {
                if (is_null($value) || $value == "") {
                    $value = $defaultValue;
                }
                if (!$this->isTestMode()) {
                    if ($encloseCell !== false) {
                        $value = str_replace($encloseCell, $encloseEscape . $encloseCell, $value);
                        $value = sprintf('%s%s%s', $encloseCell, $value, $encloseCell);
                    }
                    $row[] = $value;
                } else {
                    $row[] = array('label' => $column, 'value' => $value);
                }
            }
        }

        if (!$this->isTestMode()) {
            $this->fileDriver->fileWrite($this->getTemporaryHandle(), ($addNewLine ? PHP_EOL : '') . implode($delimiter, $row));
        } else {
            $this->testOutput[] = $row;
        }
        if ($addNewLine) {
            $this->countProductsExported++;
        }

        return $this;
    }

    /**
     * @return Generator
     */
    protected function closeTemporaryHandle()
    {
        if ($this->hasData('temporary_handle')) {
            $this->fileDriver->fileClose($this->getData('temporary_handle'));
            $this->unsetData('temporary_handle');
        }

        return $this;
    }

    /**
     * Only transfer data from temporary feed file if in
     * batch mode and this is the last batch, or if not in batch mode.
     *
     * return RocketWeb_GoogleBaseFeedGenerator_Model_Generator
     */
    protected function copyDataFromTemporaryFeedFile()
    {
        if (!$this->isTestMode()) {
            if ($this->batch->isEnabled()) {
                // if this was the last batch
                if ($this->getTotalItems() <= $this->currentIteration) {
                    $this->moveFeedFile();
                    $this->processUploads();
                } else {
                    $this->updateBatchQueue();
                }
            } else {
                $this->moveFeedFile();
                $this->processUploads();
            }
        }

        return $this;
    }

    /**
     * @return mixed
     * @throws \Exception
     */
    protected function getTemporaryHandle()
    {
        if (!$this->hasData('temporary_handle') || $this->getData('temporary_handle') === null) {
            $mode = "a";
            if (!$this->batch->isEnabled() || ($this->batch->isEnabled() && $this->batch->isNew())) {
                $mode = "w";
            }

            $this->setData('temporary_handle', $this->fileDriver->fileOpen($this->getFeedFile() . '.tmp', $mode));
        }

        return $this->getData('temporary_handle');
    }

    /**
     * Moves the feed file to it's final location after being generated in a temporary location.
     * Removes queue since we are done with generation
     *
     * @return Generator
     */
    protected function moveFeedFile()
    {
        $this->fileDriver->rename($this->getFeedFile() . '.tmp', $this->getFeedFile());
        $this->removeQueue();
        $this->getLogger()->debug(sprintf('Moved %s to %s, queue removed', $this->getFeedFile() . '.tmp', $this->getFeedFile()));
        $this->updateStatus(\RocketWeb\ShoppingFeeds\Model\Feed\Source\Status::STATUS_COMPLETED);

        return $this;
    }

    /**
     * Uploads feed to all configured locations.
     */
    protected function processUploads()
    {
        $uploadCollection = $this->feed->getUploadCollection();

        if (!$this->fileDriver->isExists($this->getFeedFile())) {
            return $this;
        }

        foreach ($uploadCollection as $upload) {
            try {
                $uploader = $this->uploaderFactory->create($upload);
                $file = $this->getFeedFile();
                $result = $uploader->upload($file);

                $this->eventManager->dispatch('shoppingfeeds_ftp_upload_after',[
                    'generator' => $this,
                    'uploader' => $uploader,
                    'upload' => $result
                ]);
                if ($result) {
                    $this->getLogger()->info(sprintf("File %s uploaded to %s:%s", $file, $upload->getHost(), $upload->getPath()));
                } else {
                    $this->getLogger()->info(sprintf('Failed to upload %s', $file));
                }
            } catch (\Exception $e) {
                $this->getLogger()->info(sprintf('Problem with the upload: %s', $e->getMessage()));
            }
        }
    }

    /**
     * @return array
     */
    protected function getHeader()
    {
        $header = [];
        foreach ($this->feed->getColumnsMap() as $map) {
            $column = $map['column'];
            if (isset($header[$column])) {
                if (is_array($header[$column])) {
                    $header[$column][] = $column;
                } else {
                    $header[$column] = [$header[$column], $column];
                }
            } else {
                $header[$column] = $column;
            }
        }
        return $header;
    }

    /**
     * @return array
     */
    protected function getWriteFeedParams()
    {
        $encloseCell = $this->feed->getConfig('output_parameters_enclose_cell', '');
        $cellEncloseEscape = $this->feed->getConfig('output_parameters_enclose_escape', '');
        $params = [
            'defaultValue' => $this->feed->getConfig('output_parameters_default_value', ''),
            'delimiter' => $this->feed->getConfig('output_parameters_delimiter', "\t"),
            'encloseCell' => $encloseCell,
            'encloseEscape' => $encloseCell !== '' ? $cellEncloseEscape : ''
        ];
        return $params;
    }

    /**
     * @return $this
     */
    protected function logProgress()
    {
        $time = $this->dateTime->date()->getTimestamp();

        if ($time - $this->getProgressTiming() > 15
         || $this->currentIteration <= 1
         || $this->currentIteration == $this->getTotalItems()
         || (!is_null($this->batch) && ($this->batch->isEnabled()
             && ($this->currentIteration % $this->batch->getLimit() == 0
                 || $this->memory->isCloseToPhpLimit($this->getData('started_at'), false)
                )
            ))
        )
        {
            $percent = sprintf('%d', round($this->currentIteration / $this->getTotalItems() * 100));
            if (!$this->isTestMode()) {
                $this->feed->saveMessages([
                    'date' => $this->dateTime->formatDateTime($this->dateTime->date()),
                    'progress' => $percent,
                    'added' => $this->currentIteration,
                    'exported' => $this->getCountProductsExported(),
                    'skipped' => $this->getCountProductsSkipped(),
                    'file' => $this->getFeedFile(),
                    'store_id' => $this->feed->getStoreId()
                ]);
            }

            $this->getLogger()->info(sprintf("Processed %s of %s (%s%%)", $this->currentIteration, $this->getTotalItems(), $percent));
            $this->setProgressTiming($time);
        }
        return $this;
    }

    /**
     * Could take negative value to decrease count
     * @param $val
     * @return Generator
     */
    public function updateCountSkip($val = 1)
    {
        $this->countProductsSkipped = $this->countProductsSkipped + $val;
        return $this;
    }

    /**
     * @return int
     */
    public function getCountProductsExported()
    {
        return $this->countProductsExported;
    }

    /**
     * @return int
     */
    public function getCountProductsSkipped()
    {
        return $this->countProductsSkipped;
    }

    public function isTestMode()
    {
        return !is_null($this->testSku);
    }

    public function setTestSku($testSku)
    {
        $this->testSku = $testSku;
        return $this;
    }

    public function getTestOutput()
    {
        return $this->testOutput;
    }

    /**
     * Sets the logger handler if it doesn't exists yet
     *
     * @return Logger
     */
    public function getLogger()
    {
        if (!$this->hasData('feed_log_file') && !$this->isTestMode()) {
            $mageRootPath = $this->directoryList->getRoot();
            $feedFolder = $mageRootPath . '/' . $this->feed->getConfig('general_feed_dir');
            if (!$this->fileDriver->isExists($feedFolder)) {
                $this->fileDriver->createDirectory($feedFolder, 0777);
            }
            $feedLogFile = $this->feed->getConfig('general_feed_dir') . '/'
                . sprintf($this->feed->getConfig('file_log'), $this->feed->getId());
            $this->logger->addHandler($feedLogFile);
            $this->setData('feed_log_file', $feedLogFile);
        }
        return $this->logger;
    }

    /**
     * Sets the feed file path and returns the data
     *
     * @return string
     */
    public function getFeedFile()
    {
        if (!$this->hasData('feed_file')) {
            $mageRootPath = $this->directoryList->getRoot();
            $feedFolder = $mageRootPath . '/' . $this->feed->getConfig('general_feed_dir');
            if (!$this->fileDriver->isExists($feedFolder)) {
                $this->fileDriver->createDirectory($feedFolder, 0777);
            }
            $feedFile = $feedFolder . '/'
                . sprintf($this->feed->getConfig('file_feed'), $this->feed->getId());
            $this->setData('feed_file', $feedFile);
        }
        return $this->getData('feed_file');
    }

    /**
     * Update queue batch data for next run
     *
     * @return bool
     */
    public function updateBatchQueue()
    {
        $this->batch->setOffset($this->currentIteration);

        if (!is_null($this->queue) && $this->queue->getId()) {
            $this->queue->setBatch($this->batch)
                ->setData('is_read', 0)
                ->save();
            $this->batch = null;
            return true;
        }
         // We unset this so its not re-run in destructor()
        return false;
    }

    public function updateStatus($status)
    {
        if (!$this->isTestMode()) {
            $this->feed->saveStatus($status);
        }
    }

    protected function removeQueue()
    {
        if (!is_null($this->queue)) {
            $this->queue->delete();
            // We unset this so queue is not recreated
            $this->queue = null;
            $this->batch = null;
            return true;
        }
        return false;
    }

    /**
     * Release the lock in case of issues
     */
    public function __destruct()
    {
        // Class can be destroyed (exception), so update queue if$this->batch is set!
        if (!is_null($this->batch)) {
            $this->updateBatchQueue();
        }
    }


}
