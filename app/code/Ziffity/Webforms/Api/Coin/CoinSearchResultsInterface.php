<?php

namespace Ziffity\Webforms\Api\Coin;

use Magento\Framework\Api\SearchResultsInterface;

interface CoinSearchResultsInterface extends SearchResultsInterface
{
    public function getItems();

    public function setItems(array $items);
}
