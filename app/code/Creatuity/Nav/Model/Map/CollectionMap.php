<?php

/**
 * CollectionMap class
 */
namespace Creatuity\Nav\Model\Map;

use Magento\Framework\Data\Collection;
use Creatuity\Nav\Model\Map\Provider\CollectionProviderInterface;
use Creatuity\Nav\Exception\InvalidPageIndexException;
use Creatuity\Nav\Exception\MapEntryNotFoundException;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Store\Model\ScopeInterface;

/**
 * CollectionMap
 */
class CollectionMap
{
    /**
     * @var string
     */
    protected $keyField;

    /**
     * @var string
     */
    protected $pageSize;

    /**
     * @var int
     */
    protected $pageCount;

    /**
     * @var array
     */
    protected $pageIndices;

    /**
     * @var int
     */
    protected $currentPage;

    /**
     * @var Collection
     */
    protected $collection;

    /**
     * @var array
     */
    protected $data;

    /**
     * @var CollectionProviderInterface
     */
    protected $collectionProvider;

    /**
     * @var ScopeConfigInterface
     */
    protected $scopeConfig;

    /**
     *
     * @param string                      $keyField
     * @param string                      $pageSize
     * @param CollectionProviderInterface $collectionProvider
     * @param ScopeConfigInterface        $scopeConfig
     */
    public function __construct(
        $keyField,
        $pageSize,
        CollectionProviderInterface $collectionProvider,
        ScopeConfigInterface $scopeConfig
    ) {
        $this->keyField = $keyField;
        $this->pageSize = $pageSize;
        $this->collectionProvider = $collectionProvider;
        $this->scopeConfig = $scopeConfig;
    }

    /**
     *
     * @param string $key
     *
     * @return array
     * @throws MapEntryNotFoundException
     */
    public function get($key)
    {
        $this->rebuildData();

        if (!isset($this->data[$key])) {
            throw new MapEntryNotFoundException(
                "Map entry with '{$this->keyField}' field value '{$key}' does not exist"
            );
        }

        return $this->data[$key];
    }

    /**
     *
     * @param int $page
     *
     * @return void
     * @throws InvalidPageIndexException
     */
    public function setPage($page)
    {
        if ($page < 1 || $page > $this->getPageCount()) {
            throw new InvalidPageIndexException(
                "Page index '{$page}' is outside valid range"
            );
        }

        $this->currentPage = $page;
        $this->collection = $this->getCurrentPage();
        unset($this->data);
    }

    /**
     *
     * @return array
     */
    public function getKeys()
    {
        $this->rebuildData();

        return array_keys($this->data);
    }

    /**
     *
     * @return array
     */
    public function getPageIndices()
    {
        if (!isset($this->pageIndices)) {
            $this->pageIndices = range(1, $this->getPageCount());
        }

        return $this->pageIndices;
    }

    /**
     *
     * @return int
     */
    public function getPageCount()
    {
        if (!isset($this->pageCount)) {
            $this->pageCount = $this->collectionProvider->getCollection()
                ->setPageSize(
                    $this->scopeConfig->getValue(
                        $this->pageSize, ScopeInterface::SCOPE_STORE
                    )
                )
                ->getLastPageNumber();
        }

        return $this->pageCount;
    }

    /**
     *
     * @return void
     */
    protected function rebuildData()
    {
        if (!isset($this->data)) {
            $this->data = array_combine(
                $this->getNormalizedKeys($this->collection),
                $this->collection->getItems()
            );
        }
    }

    /**
     *
     * @param Collection $collection
     *
     * @return array
     */
    protected function getNormalizedKeys(Collection $collection)
    {
        return array_map(
            function ($key) {
                return trim($key);
            },
            $collection->getColumnValues($this->keyField)
        );
    }

    /**
     *
     * @return Collection
     */
    protected function getCurrentPage()
    {
        $productCollection = $this->collectionProvider->getCollection()
            ->setPage(
                $this->currentPage,
                $this->scopeConfig->getValue(
                    $this->pageSize, ScopeInterface::SCOPE_STORE
                )
            );

        return $productCollection;
    }
}
