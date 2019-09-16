<?php

namespace Creatuity\Nav\Model\Map;

use Magento\Framework\Data\Collection;
use Creatuity\Nav\Model\Map\Provider\CollectionProviderInterface;
use Creatuity\Nav\Exception\InvalidPageIndexException;
use Creatuity\Nav\Exception\MapEntryNotFoundException;

class CollectionMap
{
    protected $keyField;

    protected $pageSize;
    protected $pageCount;
    protected $pageIndices;

    protected $currentPage;

    protected $collection;
    protected $data;

    protected $collectionProvider;

    public function __construct(
        $keyField,
        $pageSize,
        CollectionProviderInterface $collectionProvider
    ) {
        $this->keyField = $keyField;
        $this->pageSize = $pageSize;
        $this->collectionProvider = $collectionProvider;
    }

    public function get($key)
    {
        $this->rebuildData();

        if (!isset($this->data[$key])) {
            throw new MapEntryNotFoundException("Map entry with '{$this->keyField}' field value '{$key}' does not exist");
        }

        return $this->data[$key];
    }

    public function setPage($page)
    {
        if ($page < 1 || $page > $this->getPageCount()) {
            throw new InvalidPageIndexException("Page index '{$page}' is outside valid range");
        }

        $this->currentPage = $page;
        $this->collection = $this->getCurrentPage();
        unset($this->data);
    }

    public function getKeys()
    {
        $this->rebuildData();

        return array_keys($this->data);
    }

    public function getPageIndices()
    {
        if (!isset($this->pageIndices)) {
            $this->pageIndices = range(1, $this->getPageCount());
        }

        return $this->pageIndices;
    }

    public function getPageCount()
    {
        if (!isset($this->pageCount)) {
            $this->pageCount = $this->collectionProvider->getCollection()
                ->setPageSize($this->pageSize)
                ->getLastPageNumber()
            ;
        }

        return $this->pageCount;
    }

    protected function rebuildData()
    {
        if (!isset($this->data)) {
            $this->data = array_combine(
                $this->getNormalizedKeys($this->collection),
                $this->collection->getItems()
            );
        }
    }

    protected function getNormalizedKeys(Collection $collection)
    {
        return array_map(
            function($key) {
                return trim($key);
            },
            $collection->getColumnValues($this->keyField)
        );
    }

    protected function getCurrentPage()
    {
        $productCollection = $this->collectionProvider->getCollection()
            ->setPage($this->currentPage, $this->pageSize)
        ;

        return $productCollection;
    }
}
