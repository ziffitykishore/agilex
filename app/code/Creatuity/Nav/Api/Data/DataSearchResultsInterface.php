<?php

namespace Creatuity\Nav\Api\Data;

use Magento\Framework\Api\SearchResultsInterface;

interface DataSearchResultsInterface extends SearchResultsInterface
{
    /**
     * Get data list.
     *
     * @return \Creatuity\Nav\Api\Data\DataInterface[]
     */
    public function getItems();

    /**
     * Set data list.
     *
     * @param \Creatuity\Nav\Api\Data\DataInterface[] $items
     * @return $this
     */
    public function setItems(array $items);
}
