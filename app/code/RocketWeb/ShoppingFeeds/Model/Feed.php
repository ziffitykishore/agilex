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


namespace RocketWeb\ShoppingFeeds\Model;

use Magento\Framework\DataObject;
use Magento\Framework\Model\AbstractModel;
use RocketWeb\ShoppingFeeds\Model\ResourceModel\Feed\Schedule\CollectionFactory as ScheduleFactory;
use RocketWeb\ShoppingFeeds\Model\ResourceModel\Feed\Upload\CollectionFactory as UploadCollectionFactory;
use RocketWeb\ShoppingFeeds\Model\ResourceModel\Feed\Config\CollectionFactory as ConfigCollectionFactory;
use RocketWeb\ShoppingFeeds\Model\ResourceModel\Feed\ConfigFactory;

/**
 * Class Feed
 *
 * @package RocketWeb\ShoppingFeeds\Model
 *
 * @method  int getStoreId()
 */
class Feed extends AbstractModel
{
    /**
     * Event prefix for observer
     *
     * @var string
     */
    protected $_eventPrefix = 'shoppingfeeds_feed';

    /**
     * @var ScheduleFactory
     */
    protected $scheduleCollectionFactory;

    /**
     * @var UploadCollectionFactory
     */
    protected $uploadCollectionFactory;

    /**
     * @var ConfigCollectionFactory
     */
    protected $configCollectionFactory;

    /**
     * @var \RocketWeb\ShoppingFeeds\Model\Feed\ConfigFactory
     */
    protected $configFactory;

    /**
     * @var \RocketWeb\ShoppingFeeds\Model\Feed\ScheduleFactory
     */
    protected $scheduleFactory;

    /**
     * @var \RocketWeb\ShoppingFeeds\Model\Feed\UploadFactory
     */
    protected $uploadFactory;

    /**
     * @var \RocketWeb\ShoppingFeeds\Model\FeedTypes\Config
     */
    protected $feedTypesConfig;

    /**
     * Columns map cache
     *
     * @var array
     */
    protected $columnsMap;

    /**
     * @var \Magento\Framework\Pricing\PriceCurrencyInterface
     */
    protected $priceCurrency;

    /**
     * Store object
     *
     * @var null|\Magento\Store\Model\Store
     */
    protected $storeObject = null;

    /**
     * @var \RocketWeb\ShoppingFeeds\Model\Generator\Cache\ShippingProvider
     */
    protected $shippingProvider;

    /**
     * @var \Magento\Framework\App\Config\ScopeConfigInterface
     */
    protected $scopeConfig;

    /**
     * @var \Magento\Framework\Stdlib\DateTime\TimezoneInterface
     */
    protected $localeDate;

    /**
     * Feed constructor.
     * @param \Magento\Framework\Model\Context $context
     * @param \Magento\Framework\Registry $registry
     * @param ScheduleFactory $scheduleCollectionFactory
     * @param UploadCollectionFactory $uploadCollectionFactory
     * @param ConfigCollectionFactory $configCollectionFactory
     * @param Feed\ConfigFactory $configFactory
     * @param \RocketWeb\ShoppingFeeds\Model\Feed\ScheduleFactory $scheduleFactory
     * @param \RocketWeb\ShoppingFeeds\Model\Feed\UploadFactory $uploadFactory
     * @param FeedTypes\Config $feedTypesConfig
     * @param \RocketWeb\ShoppingFeeds\Model\Generator\Cache\ShippingProvider $shippingProvider
     * @param \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig
     * @param \Magento\Framework\Stdlib\DateTime\TimezoneInterface $localeDate
     * @param \Magento\Framework\Model\ResourceModel\AbstractResource|null $resource
     * @param \Magento\Framework\Data\Collection\AbstractDb|null $resourceCollection
     * @param string $feedType
     * @param array $data
     */
    public function __construct(
        \Magento\Framework\Model\Context $context,
        \Magento\Framework\Registry $registry,
        \Magento\Framework\Pricing\PriceCurrencyInterface $priceCurrency,
        \Magento\Store\Model\Store $storeObject,
        ScheduleFactory $scheduleCollectionFactory,
        UploadCollectionFactory $uploadCollectionFactory,
        ConfigCollectionFactory $configCollectionFactory,
        \RocketWeb\ShoppingFeeds\Model\Feed\ConfigFactory $configFactory,
        \RocketWeb\ShoppingFeeds\Model\Feed\ScheduleFactory $scheduleFactory,
        \RocketWeb\ShoppingFeeds\Model\Feed\UploadFactory $uploadFactory,
        \RocketWeb\ShoppingFeeds\Model\FeedTypes\Config $feedTypesConfig,
        \RocketWeb\ShoppingFeeds\Model\Generator\Cache\ShippingProvider $shippingProvider,
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig,
        \Magento\Framework\Stdlib\DateTime\TimezoneInterface $localeDate,
        \Magento\Framework\Model\ResourceModel\AbstractResource $resource = null,
        \Magento\Framework\Data\Collection\AbstractDb $resourceCollection = null,
        $feedType = '',
        array $data = []
    ) {
        $this->scheduleCollectionFactory = $scheduleCollectionFactory;
        $this->uploadCollectionFactory = $uploadCollectionFactory;
        $this->configCollectionFactory = $configCollectionFactory;
        $this->configFactory = $configFactory;
        $this->scheduleFactory = $scheduleFactory;
        $this->uploadFactory = $uploadFactory;
        $this->feedTypesConfig = $feedTypesConfig;
        $this->storeObject = $storeObject;
        $this->priceCurrency = $priceCurrency;
        $this->shippingProvider = $shippingProvider;
        $this->scopeConfig = $scopeConfig;
        $this->localeDate = $localeDate;

        parent::__construct($context, $registry, $resource, $resourceCollection, $data);

        if (!empty($feedType)) {
            $this->setDefaultConfig($feedType);
        }
    }

    /**
     * Initialize resource model
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init('RocketWeb\ShoppingFeeds\Model\ResourceModel\Feed');
    }

    /**
     * Returns columns map in asc order.
     * Skips columns with attributes that doesn't exist.
     * Caches eav attributes model used.
     *
     *  [column] =>
     *            [column]
     *            [attribute code or directive code]
     *            [default_value]
     *            [order]
     *
     * @return array
     */
    public function getColumnsMap()
    {
        if (!is_null($this->columnsMap) && is_array($this->columnsMap)) {
            return $this->columnsMap;
        }

        $this->columnsMap = array();
        $order = array();

        $counter = 0;
        foreach ($this->getConfig()->getData('columns_product_columns') as $arr) {
            $this->columnsMap[] = $arr;
            $order[sprintf('%s_%s', $arr['column'], $counter)] = $arr['order'];
            $counter++;
        }
        array_multisort($order, $this->columnsMap);

        return $this->columnsMap;
    }

    public function setColumnsMap($columnsMap = array())
    {
        $this->columnsMap = $columnsMap;
        return $this;
    }

    public function setType($type)
    {
        $this->setData('type', $type);
        if (!$this->hasData('config')) {
            $this->setData('config', new \Magento\Framework\DataObject());
            $this->setDefaultConfig($type);
        }
        return $this;
    }

    /**
     * Serialize messages before save if needed
     *
     * @return $this
     */
    public function beforeSave()
    {
        //TODO: if schedule is adjusted, set the processed_at date one day back so schedule can be picked up today.
        if (is_array($this->getData('messages'))) {
            $this->setData('messages', serialize($this->getData('messages')));
        }

        return parent::beforeSave();
    }

    /**
     * Use resource to save the feed so that config is not saved.
     *
     * @param $value
     * @return $this
     */
    public function saveMessages($value)
    {
        $messages = $this->getMessages();
        if (!$messages) {
            $messages = [];
        }
        $data = array_merge($messages, $value);
        $this->setData('messages', $data);

        $this->beforeSave();
        $this->getResource()->save($this);
        return $this;
    }

    /**
     * Use resource to save the feed so that config is not saved.
     *
     * @param $value
     * @return $this
     */
    public function saveStatus($status)
    {
        $this->setStatus($status);
        $this->beforeSave();

        $this->getResource()->unsUpdatedAt()
            ->save($this);

        return $this;
    }

    /**
     * Save object data
     *
     * @return $this
     * @throws \Exception
     */
    public function save()
    {
        $this->validate();

        return parent::save();
    }

    /**
     * Save all config data
     *
     * @return $this
     */
    public function afterSave()
    {
        $config = $this->getConfig();
        if ($config) {
            $config = $config->toArray();
        } else {
            $config = [];
        }
        if (count($config)) {
            /** @var |RocketWeb\ShoppingFeeds\Model\ResourceModel\Feed\Config\Collection $configCollection */
            $configCollection = $this->getConfigCollection();
            foreach ($configCollection as $configObject) {
                /** @var \RocketWeb\ShoppingFeeds\Model\Feed\Config $configObject */
                $path = $configObject->getData('path');
                $value = $configObject->getData('value');
                if (array_key_exists($path, $config)) {
                    if ($config[$path] != $value) {
                        $configObject->setData('value', $config[$path]);
                        $configObject->save();
                    }
                    unset($config[$path]);
                }
            }

            if (count($config) > 0) {
                foreach ($config as $path => $value) {
                    $configObject = $this->configFactory->create();
                    $configObject->setData('path', $path);
                    $configObject->setData('value', $value);
                    $configObject->setData('feed_id', $this->getId());
                    $configObject->save();
                }
            }
            // Clear shipping cache on feed save
            if ($this->getConfig('shipping_cache_enabled') &&
                !$this->scopeConfig->getValue(\Magento\Directory\Model\Observer::IMPORT_ENABLE)
            ) {
                $this->shippingProvider->clearCache($this);
            }
        }

        $this->saveSchedules();

        $this->saveUploads();

        return parent::afterSave();
    }

    /**
     * Save schedules after feed save
     *
     * @return void
     */
    public function saveSchedules()
    {
        $schedules = $this->getData('schedules');

        if (count($schedules)) {
            // Force elements with ID setm to save first. Prevent order changed in
            // RocketWeb\ShoppingFeeds\Block\Adminhtml\Feed\Edit\Tab\Schedule::sortValuesCallback
            usort($schedules, [$this, 'sortByIdCallback']);

            foreach ($schedules as $schedule) {
                $scheduleObject = $this->scheduleFactory->create();
                
                // test if this should be a new schedule entry (for example when cloning a feed)
                if (isset($schedule['id'])) {
                    $scheduleObject->load($schedule['id']);
                }

                if (isset($schedule['delete']) && $schedule['delete']) {
                    if ($scheduleObject->getId()) {
                        $scheduleObject->delete();
                    }
                    continue;
                }

                $scheduleObject->setData('start_at', isset($schedule['start_at']) ? $schedule['start_at'] : 1);

                if (array_key_exists('batch_mode', $schedule)) {
                    $scheduleObject->setData('batch_mode', $schedule['batch_mode']);
                }
                if (array_key_exists('batch_limit', $schedule)) {
                    $scheduleObject->setData('batch_limit', $schedule['batch_limit']);
                }

		if (!$scheduleObject->hasData('processed_at')) {
                    $scheduleObject->setData('processed_at', $this->localeDate->date()->format(\Magento\Framework\DB\Adapter\Pdo\Mysql::DATETIME_FORMAT));
                }

                if (!$scheduleObject->getId()) {
                    $scheduleObject->setData('feed_id', $this->getId());
                }

                try {
                    $scheduleObject->save();
                } catch (\Exception $e) {
                    if ($e->getCode() === \Magento\UrlRewrite\Model\Storage\DbStorage::ERROR_CODE_DUPLICATE_ENTRY
                        && preg_match('#SQLSTATE\[23000\]: [^:]+: 1062[^\d]#', $e->getMessage())
                    ) {
                        throw new \Magento\Framework\Exception\LocalizedException(
                            __('Only one schedule can be added for each Start At hour.')
                        );
                    }
                    throw $e;
                }
            }
        }
    }

    /**
     * Sort array by id key descendent values
     *
     * @param array $a
     * @param array $b
     * @return int
     * @SuppressWarnings(PHPMD.NPathComplexity)
     */
    protected function sortByIdCallback($a, $b)
    {
        return $b['id'] - $a['id'];
    }

    /**
     * Save uploads after feed save
     *
     * @return void
     */
    public function saveUploads()
    {
        $uploads = $this->getData('uploads');

        if (count($uploads)) {
            /** @var |RocketWeb\ShoppingFeeds\Model\ResourceModel\Feed\Upload\Collection $uploadCollection */
            $uploadCollection = $this->getUploadCollection();

            foreach ($uploads as $upload) {
                $uploadObject = $this->uploadFactory->create();

                if (isset($upload['id'])) {
                    $uploadObject->load($upload['id']);
                }

                if ($uploadObject->getId() && $upload['delete']) {
                    $uploadObject->delete();
                    continue;
                }

                $uploadObject->setData('mode', $upload['mode']);
                $uploadObject->setData('host', $upload['host']);
                $uploadObject->setData('port', $upload['port']);
                $uploadObject->setData('username', $upload['username']);
                $uploadObject->setData('password', $upload['password']);
                $uploadObject->setData('gzip', $upload['gzip']);
                $uploadObject->setData('path', $upload['path']);

                if (!$uploadObject->getId()) {
                    $uploadObject->setData('feed_id', $this->getId());
                }

                $uploadObject->save();
            }
        }
    }

    /**
     * Method which can be used to add custom validation.
     *
     * In case of any error \Magento\Framework\Exception\LocalizedException 
     * should be thrown.
     *
     * @return $this
     */
    protected function validate()
    {
        return $this;
    }

    /**
     * Unserialize messages after load if needed
     * Loads all saved config into getConfig && adds default ones if any missing
     *
     * @return $this
     */
    protected function _afterLoad()
    {
        if (!is_array($this->getData('messages'))) {
            $messages = $this->getData('messages');
            $this->setData('messages', empty($messages) ? false : unserialize($messages));
        }

        if (!$this->hasData('config') || !($this->getData('config') instanceof \Magento\Framework\DataObject)) {
            $this->setData('config', new \Magento\Framework\DataObject());
        }

        //Set all the configs into $config dataObject
        $configCollection = $this->getConfigCollection();
        foreach ($configCollection as $config) {
            /** @var \RocketWeb\ShoppingFeeds\Model\Feed\Config $config */
            $path = $config->getData('path');
            if (!empty($path)) {
                $this->getConfig()->setData($path, $config->getData('value'));
            }
        }
        
        $this->setDefaultConfig($this->getType());
        return parent::_afterLoad();
    }

    /**
     * Set default feed config into getConfig object
     * Its aware of already existing data and it will fill in only missing ones!
     * It also adds the feed output parameters (delimiter, default cell value, ...)
     *
     * @param $type
     * @return $this
     */
    protected function setDefaultConfig($type)
    {
        $defaultFeedConfig = $this->feedTypesConfig->getFeed($type);
        if (count($defaultFeedConfig)) {
            foreach ($defaultFeedConfig['default_feed_config'] as $group => $items) {
                foreach ($items as $key => $value) {
                    $path = sprintf('%s_%s', $group, $key);
                    if ($this->getConfig()->hasData($path)) {
                        continue;
                    }
                    $this->getConfig()->setData($path, $value);
                }
            }
        }
        return $this;
    }

    /**
     * Unserialize messages if needed
     *
     * @return $this
     */
    public function getMessages()
    {
        if (!is_array($this->getData('messages'))) {
            $messages = $this->getData('messages');
            $this->setData('messages', empty($messages) ? false : unserialize($messages));
        }

        return $this->getData('messages');
    }

    /**
     * @return |RocketWeb\ShoppingFeeds\Model\ResourceModel\Feed\Config\Collection
     */
    public function getConfigCollection()
    {
        return $this->configCollectionFactory->create()->setFeedFilter($this);
    }

    /**
     * Return schedule collection for a current feed.
     *
     * @return \RocketWeb\ShoppingFeeds\Model\ResourceModel\Feed\Schedule\Collection
     */
    public function getScheduleCollection()
    {
        return $this->scheduleCollectionFactory->create()->setFeedFilter($this);
    }

    /**
     * Return upload collection for a current feed.
     *
     * @return \RocketWeb\ShoppingFeeds\Model\ResourceModel\Feed\Upload\Collection
     */
    public function getUploadCollection()
    {
        return $this->uploadCollectionFactory->create()->setFeedFilter($this);
    }

    /**
     * Return feed schedules formatted in human readable way.
     *
     * @return array
     */
    public function getFormattedSchedules()
    {
        $schedules = [];
        $scheduleCollection = $this->getScheduleCollection();

        if (!$scheduleCollection->getSize()) {
            return [__('None')];
        }

        foreach ($scheduleCollection as $schedule) {
            $schedules[] = $schedule->getFormattedSchedule();
        }

        return $schedules;
    }

    /**
     * @param $type
     * @return bool
     */
    public function isProductTypeEnabled($type)
    {
        return in_array($type, $this->getConfig('filters_product_types', []));
    }

    /**
     * @return bool
     */
    public function isTaxonomyAutocompleteEnabled()
    {
        return $this->getConfig('categories_taxonomy_autocomplete_enabled') == 1 ? true : false;
    }

    /**
     * @param null|string $param
     * @param null|boolean|mixed $default
     *
     * @return \Magento\Framework\DataObject|mixed|null
     */
    public function getConfig($param = null, $default = null, $notFoundUseDefault = null)
    {
        $data = $this->getData('config', $param);
        if (is_null($data) || $data == $notFoundUseDefault) {
            $data = $default;
        }
        return $data;
    }

    /**
     * Get array containing schedules and cache them in feed object.
     *
     * @return array
     */
    public function getSchedules()
    {
        if ($this->hasData('schedules') && is_array($this->getData('schedules'))) {
            return $this->getData('schedules');
        }

        $schedules = [];

        $scheduleCollection = $this->getScheduleCollection();
        foreach ($scheduleCollection as $schedule) {
            $schedules[] = $schedule->getData();
        }

        $this->setData('schedules', $schedules);

        return $schedules;
    }

    /**
     * Get array containing Uploads and cache them in feed object.
     *
     * @return array
     */
    public function getUploads()
    {
        if ($this->hasData('uploads') && is_array($this->getData('uploads'))) {
            return $this->getData('uploads');
        }

        $uploads = [];

        $uploadCollection = $this->getUploadCollection();
        foreach ($uploadCollection as $upload) {
            $uploads[] = $upload->getData();
        }

        $this->setData('uploads', $uploads);

        return $uploads;
    }

    /**
     * Returns store instance
     *
     * @return \Magento\Store\Model\Store
     */
    public function getStore()
    {
        if ($this->storeObject->getStoreId() != $this->getStoreId()) {
            $this->storeObject->load($this->getStoreId());
            $currency = $this->priceCurrency->getCurrency($this->storeObject, $this->getConfig('general_currency'));
            $this->storeObject->setData('current_currency', $currency);
        }
        return $this->storeObject;
    }
}
