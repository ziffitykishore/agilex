<?php

namespace Earthlite\LayerNavigation\Model\ResourceModel\Fulltext;

use Exception;
use Magento\Catalog\Model\Category;
use Magento\Catalog\Model\Indexer\Product\Flat\State;
use Magento\Catalog\Model\Layer\Filter\Dynamic\AlgorithmFactory;
use Magento\Catalog\Model\Product\OptionFactory;
use Magento\Catalog\Model\ResourceModel\Helper;
use Magento\Catalog\Model\ResourceModel\Url;
use Magento\CatalogSearch\Model\ResourceModel\Fulltext\Collection\DefaultFilterStrategyApplyChecker;
use Magento\CatalogSearch\Model\ResourceModel\Fulltext\Collection\DefaultFilterStrategyApplyCheckerInterface;
use Magento\CatalogSearch\Model\Search\RequestGenerator;
use Magento\Config\Model\Config\Backend\Admin\Custom;
use Magento\Customer\Api\GroupManagementInterface;
use Magento\Customer\Model\Session;
use Magento\Eav\Model\Config;
use Magento\Eav\Model\EntityFactory as EavEntityFactory;
use Magento\Framework\Api\FilterBuilder;
use Magento\Framework\Api\Search\SearchResultInterface;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\App\ObjectManager;
use Magento\Framework\App\Request\Http;
use Magento\Framework\App\ResourceConnection;
use Magento\Framework\Data\Collection\Db\FetchStrategyInterface;
use Magento\Framework\Data\Collection\EntityFactory;
use Magento\Framework\DB\Adapter\AdapterInterface;
use Magento\Framework\DB\Select;
use Magento\Framework\Event\ManagerInterface;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Exception\StateException;
use Magento\Framework\Module\Manager;
use Magento\Framework\Search\Adapter\Mysql\TemporaryStorage;
use Magento\Framework\Search\Adapter\Mysql\TemporaryStorageFactory;
use Magento\Framework\Stdlib\DateTime;
use Magento\Framework\Stdlib\DateTime\TimezoneInterface;
use Magento\Framework\Validator\UniversalFactory;
use Magento\Search\Api\SearchInterface;
use Magento\Store\Model\ScopeInterface;
use Magento\Store\Model\StoreManagerInterface;
use Earthlite\LayerNavigation\Model\Search\SearchCriteriaBuilder;
use Psr\Log\LoggerInterface;
use RuntimeException;
use Zend_Db_Exception;
use Magento\Framework\Registry;


class Collection extends \Magento\Catalog\Model\ResourceModel\Product\Collection
{
    const PART_FINDER_ENABLED = 'partfinder/general/enable';

    public $collectionClone = null;

    private $queryText;

    private $order = null;

    private $searchRequestName;

    private $temporaryStorageFactory;

    private $search;

    private $searchCriteriaBuilder;

    private $searchResult;

    private $filterBuilder;

    protected $request;

    private $searchOrders;

    private $defaultFilterStrategyApplyChecker;

    protected $_registry;

    public function __construct(
        EntityFactory $entityFactory,
        LoggerInterface $logger,
        FetchStrategyInterface $fetchStrategy,
        ManagerInterface $eventManager,
        Config $eavConfig,
        ResourceConnection $resource,
        EavEntityFactory $eavEntityFactory,
        Helper $resourceHelper,
        UniversalFactory $universalFactory,
        StoreManagerInterface $storeManager,
        Manager $moduleManager,
        State $productFlatState,
        ScopeConfigInterface $scopeConfig,
        OptionFactory $productOptionFactory,
        Url $catalogUrl,
        TimezoneInterface $localeDate,
        Session $customerSession,
        DateTime $dateTime,
        GroupManagementInterface $groupManagement,
        TemporaryStorageFactory $tempStorageFactory,
        Http $request,
        Registry $registry,
        AdapterInterface $connection = null,
        DefaultFilterStrategyApplyCheckerInterface $defaultFilterStrategyApplyChecker = null,
        $searchRequestName = 'catalog_view_container'
    ) {
        parent::__construct(
            $entityFactory,
            $logger,
            $fetchStrategy,
            $eventManager,
            $eavConfig,
            $resource,
            $eavEntityFactory,
            $resourceHelper,
            $universalFactory,
            $storeManager,
            $moduleManager,
            $productFlatState,
            $scopeConfig,
            $productOptionFactory,
            $catalogUrl,
            $localeDate,
            $customerSession,
            $dateTime,
            $groupManagement,
            $connection
        );

        $this->temporaryStorageFactory           = $tempStorageFactory;
        $this->searchRequestName                 = $searchRequestName;
        $this->request                           = $request;
        $this->_registry                         = $registry;
        $this->defaultFilterStrategyApplyChecker = $defaultFilterStrategyApplyChecker ?: ObjectManager::getInstance()
            ->get(DefaultFilterStrategyApplyChecker::class);
    }

    public function getCollectionClone()
    {
        if ($this->collectionClone === null) {
            $this->collectionClone = clone $this;
            $this->collectionClone->setSearchCriteriaBuilder($this->searchCriteriaBuilder->cloneObject());
        }

        $searchCriterialBuilder = $this->collectionClone->getSearchCriteriaBuilder()->cloneObject();

        /** @var Collection $collectionClone */
        $collectionClone = clone $this->collectionClone;
        $collectionClone->setSearchCriteriaBuilder($searchCriterialBuilder);

        return $collectionClone;
    }

    public function addLayerCategoryFilter($categories)
    {
        if (strpos($this->getSearchEngine(), 'elasticsearch') !== false) {
            $this->addFieldToFilter('category_ids', ['in' => $categories]);
        } else {
            $this->addFieldToFilter('category_ids', implode(',', $categories));
        }

        return $this;
    }

    public function removeAttributeSearch($attributeCode)
    {
        if (is_array($attributeCode)) {
            foreach ($attributeCode as $attCode) {
                $this->searchCriteriaBuilder->removeFilter($attCode);
            }
        } else {
            $this->searchCriteriaBuilder->removeFilter($attributeCode);
        }

        $this->_isFiltersRendered = false;

        return $this->loadWithFilter();
    }

    public function getAttributeConditionSql($attribute, $condition, $joinType = 'inner')
    {
        return $this->_getAttributeConditionSql($attribute, $condition, $joinType);
    }

    public function resetTotalRecords()
    {
        $this->_totalRecords = null;

        return $this;
    }

    private function getSearch()
    {
        if ($this->search === null) {
            $this->search = ObjectManager::getInstance()->get(SearchInterface::class);
        }

        return $this->search;
    }

    public function setSearch(SearchInterface $object)
    {
        $this->search = $object;
    }

    public function getSearchCriteriaBuilder()
    {
        if ($this->searchCriteriaBuilder === null) {
            $this->searchCriteriaBuilder = ObjectManager::getInstance()
                ->get(SearchCriteriaBuilder::class);
        }

        return $this->searchCriteriaBuilder;
    }

    public function setSearchCriteriaBuilder(SearchCriteriaBuilder $object)
    {
        $this->searchCriteriaBuilder = $object;
    }

    private function getFilterBuilder()
    {
        if ($this->filterBuilder === null) {
            $this->filterBuilder = ObjectManager::getInstance()->get(FilterBuilder::class);
        }

        return $this->filterBuilder;
    }

    public function setFilterBuilder(FilterBuilder $object)
    {
        $this->filterBuilder = $object;
    }

    public function addFieldToFilter($field, $condition = null)
    {
        if ($this->searchResult !== null) {
            throw new RuntimeException('Illegal state');
        }

        $this->getSearchCriteriaBuilder();
        $this->getFilterBuilder();

        if (isset($condition['in']) && strpos($this->getSearchEngine(), 'elasticsearch') !== false) {
            $this->filterBuilder->setField($field);
            $this->filterBuilder->setValue($condition['in']);
            $this->searchCriteriaBuilder->addFilter($this->filterBuilder->create());
        } elseif (!is_array($condition) || !in_array(key($condition), ['from', 'to'])) {
            $this->filterBuilder->setField($field);
            $this->filterBuilder->setValue($condition);
            $this->searchCriteriaBuilder->addFilter($this->filterBuilder->create());
        } else {
            if (!empty($condition['from'])) {
                $this->filterBuilder->setField("{$field}.from");
                $this->filterBuilder->setValue($condition['from']);
                $this->searchCriteriaBuilder->addFilter($this->filterBuilder->create());
            }
            if (!empty($condition['to'])) {
                $this->filterBuilder->setField("{$field}.to");
                $this->filterBuilder->setValue($condition['to']);
                $this->searchCriteriaBuilder->addFilter($this->filterBuilder->create());
            }
        }

        return $this;
    }

    public function addSearchFilter($query)
    {
        $this->queryText = trim($this->queryText . ' ' . $query);

        return $this;
    }

    public function setOrder($attribute, $dir = Select::SQL_DESC)
    {
        $this->setSearchOrder($attribute, $dir);
        if ($this->defaultFilterStrategyApplyChecker->isApplicable()) {
            $this->order = ['field' => $attribute, 'dir' => $dir];
            if ($attribute !== 'relevance') {
                parent::setOrder($attribute, $dir);
            }
        }

        return $this;
    }

    public function addAttributeToSort($attribute, $dir = self::SORT_ORDER_ASC)
    {
        if ($this->defaultFilterStrategyApplyChecker->isApplicable()) {
            parent::addAttributeToSort($attribute, $dir);
        } else {
            $this->setOrder($attribute, $dir);
        }

        return $this;
    }

    private function setSearchOrder($field, $direction)
    {
        $field     = (string) $this->_getMappedField($field);
        $direction = strtoupper($direction) == self::SORT_ORDER_ASC ? self::SORT_ORDER_ASC : self::SORT_ORDER_DESC;

        $this->searchOrders[$field] = $direction;
    }

    protected function _renderFiltersBefore()
    {
        $this->getCollectionClone();

        $this->getSearchCriteriaBuilder();
        $this->getFilterBuilder();
        $this->getSearch();

        if ($this->queryText) {
            $this->filterBuilder->setField('search_term');
            $this->filterBuilder->setValue($this->queryText);
            $this->searchCriteriaBuilder->addFilter($this->filterBuilder->create());
        }

        $priceRangeCalculation = $this->_scopeConfig->getValue(
            AlgorithmFactory::XML_PATH_RANGE_CALCULATION,
            ScopeInterface::SCOPE_STORE
        );
        if ($priceRangeCalculation) {
            $this->filterBuilder->setField('price_dynamic_algorithm');
            $this->filterBuilder->setValue('auto');
            $this->searchCriteriaBuilder->addFilter($this->filterBuilder->create());
        }

        $isEnabled = $this->_scopeConfig->getValue(
            self::PART_FINDER_ENABLED,
            ScopeInterface::SCOPE_STORE
        );
        if ($this->_registry->registry('partfinder')) {
            $listOfSku = explode(',', $this->_registry->registry('partfinder'));
        }
        if ($isEnabled && isset($listOfSku)) {
            //Filter the product collection by SKU for Part Finder
            $this->filterBuilder->setField('sku');
            $this->filterBuilder->setValue($listOfSku);
            $this->filterBuilder->setConditionType('in');
            $this->searchCriteriaBuilder->addFilter($this->filterBuilder->create());
        }

        $searchCriteria = $this->searchCriteriaBuilder->create();
        if ($this->request->getFullActionName() === 'catalogsearch_result_index') {
            $this->searchRequestName = 'quick_search_container';
        }
        $searchCriteria->setRequestName($this->searchRequestName);
        $searchCriteria->setSortOrders($this->searchOrders);
        $searchCriteria->setCurrentPage((int) $this->_curPage);

        try {
            $this->searchResult = $this->getSearch()->search($searchCriteria);
        } catch (Exception $e) {
            throw new LocalizedException(__('Sorry, something went wrong. You can find out more in the error log.'));
        }

        $temporaryStorage = $this->temporaryStorageFactory->create();
        $table            = $temporaryStorage->storeDocuments($this->searchResult->getItems());

        $this->getSelect()->joinInner(
            [
                'search_result' => $table->getName(),
            ],
            'e.entity_id = search_result.' . TemporaryStorage::FIELD_ENTITY_ID,
            []
        );

        if ($this->order && ('relevance' === $this->order['field'])) {
            $this->getSelect()->order('search_result.' . TemporaryStorage::FIELD_SCORE . ' ' . $this->order['dir']);
        }

        parent::_renderFiltersBefore();
    }

    protected function _renderFilters()
    {
        $this->_filters = [];

        return parent::_renderFilters();
    }

    protected function _beforeLoad()
    {
        $this->setOrder('entity_id');

        return parent::_beforeLoad();
    }

    public function setGeneralDefaultQuery()
    {
        return $this;
    }

    public function getFacetedData($field)
    {
        $this->_renderFilters();
        $result = [];

        $aggregations = $this->searchResult->getAggregations();
        // This behavior is for case with empty object when we got EmptyRequestDataException
        if (null !== $aggregations) {
            $bucket = $aggregations->getBucket($field . RequestGenerator::BUCKET_SUFFIX);
            if ($bucket) {
                foreach ($bucket->getValues() as $value) {
                    $metrics                   = $value->getMetrics();
                    $result[$metrics['value']] = $metrics;
                }
            } else {
                throw new StateException(__('Bucket does not exist'));
            }
        }

        return $result;
    }

    public function addCategoryFilter(Category $category)
    {
        $this->addFieldToFilter('category_ids', $category->getId());

        return parent::addCategoryFilter($category);
    }

    public function setVisibility($visibility)
    {
        $this->addFieldToFilter('visibility', $visibility);

        return parent::setVisibility($visibility);
    }

    public function getSearchEngine()
    {
        return $this->_scopeConfig->getValue(Custom::XML_PATH_CATALOG_SEARCH_ENGINE, ScopeInterface::SCOPE_STORE);
    }
}
