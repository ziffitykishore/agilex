<?php

namespace Ziffity\Blockcustomers\Api\Data;

use Magento\Framework\Api\SearchResultsInterface;

interface DataSearchResultsInterface extends SearchResultsInterface
{
    /**
     * Get data list.
     *
     * @return \Ziffity\Blockcustomers\Api\Data\DataInterface[]
     */
    public function getItems();

    /**
     * Set data list.
     *
     * @param \Ziffity\Blockcustomers\Api\Data\DataInterface[] $items
     * @return $this
     */
    public function setItems(array $items);
}
