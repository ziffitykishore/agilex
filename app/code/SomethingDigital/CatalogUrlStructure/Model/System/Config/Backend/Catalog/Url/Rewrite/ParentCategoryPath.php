<?php

namespace SomethingDigital\CatalogUrlStructure\Model\System\Config\Backend\Catalog\Url\Rewrite;

use Magento\CatalogUrlRewrite\Model\CategoryUrlPathGenerator;
use Magento\CatalogUrlRewrite\Model\CategoryUrlRewriteGenerator;
use Magento\Framework\App\ResourceConnection;
use Magento\Store\Model\ScopeInterface;
use Magento\UrlRewrite\Model\Storage\DbStorage;
use Magento\UrlRewrite\Service\V1\Data\UrlRewrite;
use SomethingDigital\CatalogUrlStructure\Model\System\Config\Backend\Catalog\Url\Rewrite\Prefix;

class ParentCategoryPath extends \Magento\Framework\App\Config\Value
{

    const XML_USE_PARENT_CATEGORY_PATH = 'catalog/seo/use_parent_category_path_for_category_urls';

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
     * @var \Magento\Framework\App\Config\ScopeConfigInterface
     */
    protected $scopeConfig;

    /**
     * @var \Magento\Framework\App\Config
     */
    private $appConfig;

    /**
     * @param \Magento\Framework\Model\Context $context
     * @param \Magento\Framework\Registry $registry
     * @param \Magento\Framework\App\Config\ScopeConfigInterface $config
     * @param \Magento\Framework\App\Cache\TypeListInterface $cacheTypeList
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
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Magento\Framework\App\ResourceConnection $appResource,
        \Magento\UrlRewrite\Model\UrlFinderInterface $urlFinder,
        \Magento\Framework\Model\ResourceModel\AbstractResource $resource = null,
        \Magento\Framework\Data\Collection\AbstractDb $resourceCollection = null,
        array $data = []
    ) {
        parent::__construct($context, $registry, $config, $cacheTypeList, $resource, $resourceCollection, $data);
        $this->connection = $appResource->getConnection();
        $this->urlFinder = $urlFinder;
        $this->storeManager = $storeManager;
        $this->resource = $appResource;
        $this->scopeConfig = $config;
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
     * @return $this
     */
    public function afterSave()
    {
        if ($this->isValueChanged()) {
            $this->updateUrl();
            if ($this->isUseCategoryPathChanged()) {
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
            $this->updateUrl();
            if ($this->isUseCategoryPathChanged()) {
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
    private function isUseCategoryPathChanged()
    {
        return $this->isValueChanged()
            && ($this->getPath() == self::XML_USE_PARENT_CATEGORY_PATH);
    }

    /**
     * Update url
     *
     * @return $this
     */
    protected function updateUrl()
    {
        $storeScope = ScopeInterface::SCOPE_STORES;
        if ($this->getValue()) {
            return $this;
        }
        $map = [
            self::XML_USE_PARENT_CATEGORY_PATH => CategoryUrlRewriteGenerator::ENTITY_TYPE,
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
        
        foreach ($entities as $urlRewrite) {
            if ($urlRewrite->getEntityType() == 'category') {
                if ($urlRewrite->getIsAutogenerated()) {

                    $category_suffix = $this->scopeConfig->getValue(CategoryUrlPathGenerator::XML_PATH_CATEGORY_URL_SUFFIX, $storeScope);
                    $category_prefix = $this->scopeConfig->getValue(Prefix::XML_PATH_CATEGORY_URL_PREFIX, $storeScope);

                    $path = $urlRewrite->getRequestPath();

                    if ($this->endsWith($urlRewrite->getRequestPath(), $category_suffix)) {
                        $path = substr($urlRewrite->getRequestPath(), 0, -strlen($category_suffix));
                    }

                    if (strpos($urlRewrite->getRequestPath(), $category_prefix) === 0) {
                        $path = substr($path, strlen($category_prefix));
                    }

                    $pathArray = explode('/', $path);
                    $categoryKey = end($pathArray);

                    $newurl = $this->generateUrl($category_prefix, $category_suffix, $categoryKey, $urlRewrite);

                    $bind = [UrlRewrite::REQUEST_PATH => $newurl];
                    $where = ['url_rewrite_id = ?' => $urlRewrite->getUrlRewriteId()];
                    $this->connection->update(
                        $this->resource->getTableName(DbStorage::TABLE_NAME),
                        $bind,
                        $where
                    );
                }
            }
        }
        return $this;
    }

    /**
     * @return boolean
     */
    public function endsWith($haystack, $needle) {
        return substr($haystack,-strlen($needle))===$needle;
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

    protected function generateUrl($category_prefix, $category_suffix, $categoryKey, $urlRewrite, $categoryKeySufix = '')
    {
        $newurl = $category_prefix . $categoryKey;
        if ($categoryKeySufix) {
            $newurl .= '-' . $categoryKeySufix;
        }
        $newurl .= $category_suffix;

        if ($this->isUrlAlreadyExist($newurl, $urlRewrite)) {
            if (!$categoryKeySufix) {
                $categoryKeySufix = 2;
            } else {
                $categoryKeySufix++;
            }
            $newurl = $this->generateUrl($category_prefix, $category_suffix, $categoryKey, $urlRewrite, $categoryKeySufix);
        }

        return $newurl;
    }

    protected function isUrlAlreadyExist($newurl, $urlRewrite)
    {
        $select = $this->connection->select()
            ->from(
                ['ur' => $this->resource->getTableName('url_rewrite')],
                'ur.url_rewrite_id'
            )->where(
                "ur.request_path = '" . $newurl . "'
                AND store_id = " . $urlRewrite->getStoreId() . "
                AND url_rewrite_id != " . $urlRewrite->getUrlRewriteId()
            );
        $data = $this->connection->fetchOne($select);

        return $data;
    }
}
