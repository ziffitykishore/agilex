<?php

namespace SomethingDigital\CatalogUrlStructure\Model\System\Config\Backend\Catalog\Url\Rewrite;

use Magento\CatalogUrlRewrite\Model\CategoryUrlPathGenerator;
use Magento\CatalogUrlRewrite\Model\CategoryUrlRewriteGenerator;
use Magento\CatalogUrlRewrite\Model\ProductUrlPathGenerator;
use Magento\CatalogUrlRewrite\Model\ProductUrlRewriteGenerator;
use Magento\Framework\App\ResourceConnection;
use Magento\Store\Model\ScopeInterface;
use Magento\UrlRewrite\Model\Storage\DbStorage;
use Magento\UrlRewrite\Service\V1\Data\UrlRewrite;

class Prefix extends \Magento\Framework\App\Config\Value
{
    const XML_PATH_CATEGORY_URL_PREFIX = 'catalog/seo/category_url_prefix';
    const XML_PATH_PRODUCT_URL_PREFIX = 'catalog/seo/product_url_prefix';

    /**
     * @var \Magento\UrlRewrite\Helper\UrlRewrite
     */
    protected $urlRewriteHelper;

    /**
     * @var \Magento\Store\Model\StoreManagerInterface
     */
    protected $storeManager;

    /**
     * @var \Magento\UrlRewrite\Model\UrlFinderInterface
     */
    protected $urlFinder;

    /**
     * @var \Magento\Framework\DB\Adapter\AdapterInterface
     */
    protected $connection;

    /**
     * @var \Magento\Framework\App\ResourceConnection
     * @since 101.0.0
     */
    protected $resource;

    /**
     * @var \Magento\Framework\App\Config
     */
    private $appConfig;

    /**
     * @param \Magento\Framework\Model\Context $context
     * @param \Magento\Framework\Registry $registry
     * @param \Magento\Framework\App\Config\ScopeConfigInterface $config
     * @param \Magento\Framework\App\Cache\TypeListInterface $cacheTypeList
     * @param \Magento\UrlRewrite\Helper\UrlRewrite $urlRewriteHelper
     * @param \Magento\Store\Model\StoreManagerInterface $storeManager
     * @param ResourceConnection $appResource
     * @param \Magento\UrlRewrite\Model\UrlFinderInterface $urlFinder
     * @param \Magento\Framework\Model\ResourceModel\AbstractResource|null $resource
     * @param \Magento\Framework\Data\Collection\AbstractDb|null $resourceCollection
     * @param array $data
     * @SuppressWarnings(PHPMD.ExcessiveParameterList)
     */
    public function __construct(
        \Magento\Framework\Model\Context $context,
        \Magento\Framework\Registry $registry,
        \Magento\Framework\App\Config\ScopeConfigInterface $config,
        \Magento\Framework\App\Cache\TypeListInterface $cacheTypeList,
        \Magento\UrlRewrite\Helper\UrlRewrite $urlRewriteHelper,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Magento\Framework\App\ResourceConnection $appResource,
        \Magento\UrlRewrite\Model\UrlFinderInterface $urlFinder,
        \Magento\Framework\Model\ResourceModel\AbstractResource $resource = null,
        \Magento\Framework\Data\Collection\AbstractDb $resourceCollection = null,
        array $data = []
    ) {
        parent::__construct($context, $registry, $config, $cacheTypeList, $resource, $resourceCollection, $data);
        $this->urlRewriteHelper = $urlRewriteHelper;
        $this->connection = $appResource->getConnection();
        $this->urlFinder = $urlFinder;
        $this->storeManager = $storeManager;
        $this->resource = $appResource;
    }

    /**
     * Get instance of ScopePool
     *
     * @return \Magento\Framework\App\Config
     * @deprecated 102.0.0
     */
    private function getAppConfig()
    {
        if ($this->appConfig === null) {
            $this->appConfig = \Magento\Framework\App\ObjectManager::getInstance()->get(
                \Magento\Framework\App\Config::class
            );
        }
        return $this->appConfig;
    }

    /**
     * Check url rewrite prefix - whether we can support it
     *
     * @return $this
     */
    public function beforeSave()
    {
        $this->urlRewriteHelper->validateSuffix($this->getValue());
        return $this;
    }

    /**
     * @return $this
     */
    public function afterSave()
    {
        if ($this->isValueChanged()) {
            $this->updatePrefixForUrlRewrites();
            if ($this->isCategoryPrefixChanged()) {
                $this->cacheTypeList->invalidate([
                    \Magento\Framework\App\Cache\Type\Block::TYPE_IDENTIFIER,
                    \Magento\Framework\App\Cache\Type\Collection::TYPE_IDENTIFIER
                ]);
            }
        }
        return parent::afterSave();
    }

    /**
     * {@inheritdoc}
     * @since 102.0.0
     */
    public function afterDeleteCommit()
    {
        if ($this->isValueChanged()) {
            $this->updatePrefixForUrlRewrites();
            if ($this->isCategoryPrefixChanged()) {
                $this->cacheTypeList->invalidate([
                    \Magento\Framework\App\Cache\Type\Block::TYPE_IDENTIFIER,
                    \Magento\Framework\App\Cache\Type\Collection::TYPE_IDENTIFIER
                ]);
            }
        }

        return parent::afterDeleteCommit();
    }

    /**
     * Check is category prefix changed
     *
     * @return bool
     */
    private function isCategoryPrefixChanged()
    {
        return $this->isValueChanged()
            && ($this->getPath() == self::XML_PATH_CATEGORY_URL_PREFIX);
    }

    /**
     * Update prefix for url rewrites
     *
     * @return $this
     */
    protected function updatePrefixForUrlRewrites()
    {
        $map = [
            self::XML_PATH_PRODUCT_URL_PREFIX => ProductUrlRewriteGenerator::ENTITY_TYPE,
            self::XML_PATH_CATEGORY_URL_PREFIX => CategoryUrlRewriteGenerator::ENTITY_TYPE,
        ];
        if (!isset($map[$this->getPath()])) {
            return $this;
        }
        $dataFilter = [UrlRewrite::ENTITY_TYPE => $map[$this->getPath()]];
        $storesIds = $this->getStoreIds();
        if ($storesIds) {
            $dataFilter[UrlRewrite::STORE_ID] = $storesIds;
        }
        $entities = $this->urlFinder->findAllByData($dataFilter);
        $oldPrefix = $this->getOldValue();
        if ($this->getValue() !== null) {
            $prefix = $this->getValue();
        } else {
            $this->getAppConfig()->clean();
            $prefix = $this->_config->getValue($this->getPath());
        }
        foreach ($entities as $urlRewrite) {

            if ($oldPrefix && strpos($urlRewrite->getRequestPath(), $oldPrefix) === 0) {
                $newurl = str_replace($oldPrefix, $prefix, $urlRewrite->getRequestPath());
            } else {
                $newurl = $urlRewrite->getIsAutogenerated()
                    ? $prefix . $urlRewrite->getRequestPath()
                    : $prefix . $urlRewrite->getTargetPath();
            }
            $bind = $urlRewrite->getIsAutogenerated()
                ? [UrlRewrite::REQUEST_PATH => $newurl]
                : [UrlRewrite::TARGET_PATH => $newurl];
            $this->connection->update(
                $this->resource->getTableName(DbStorage::TABLE_NAME),
                $bind,
                $this->connection->quoteIdentifier(UrlRewrite::URL_REWRITE_ID) . ' = ' . $urlRewrite->getUrlRewriteId()
            );
        }
        return $this;
    }

    /**
     * @return array|null
     */
    protected function getStoreIds()
    {
        if ($this->getScope() == 'stores') {
            $storeIds = [$this->getScopeId()];
        } elseif ($this->getScope() == 'websites') {
            $website = $this->storeManager->getWebsite($this->getScopeId());
            $storeIds = array_keys($website->getStoreIds());
            $storeIds = array_diff($storeIds, $this->getOverrideStoreIds($storeIds));
        } else {
            $storeIds = array_keys($this->storeManager->getStores());
            $storeIds = array_diff($storeIds, $this->getOverrideStoreIds($storeIds));
        }
        return $storeIds;
    }

    /**
     * @param array $storeIds
     * @return array
     */
    protected function getOverrideStoreIds($storeIds)
    {
        $excludeIds = [];
        foreach ($storeIds as $storeId) {
            $prefix = $this->_config->getValue($this->getPath(), ScopeInterface::SCOPE_STORE, $storeId);
            if ($prefix != $this->getOldValue()) {
                $excludeIds[] = $storeId;
            }
        }
        return $excludeIds;
    }
}
