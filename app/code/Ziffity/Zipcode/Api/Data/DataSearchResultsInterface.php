<?php

namespace Ziffity\Zipcode\Api\Data;

use Magento\Framework\Api\SearchResultsInterface;

interface DataSearchResultsInterface extends SearchResultsInterface
{

    public function getItems();

    public function setItems(array $items);
}
