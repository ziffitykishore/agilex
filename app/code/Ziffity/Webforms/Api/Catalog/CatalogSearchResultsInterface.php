<?php

namespace Ziffity\Webforms\Api\Catalog;

use Magento\Framework\Api\SearchResultsInterface;

interface CatalogSearchResultsInterface extends SearchResultsInterface
{
    public function getItems();

    public function setItems(array $items);
}
