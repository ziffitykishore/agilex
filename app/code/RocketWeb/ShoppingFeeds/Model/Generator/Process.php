<?php

namespace RocketWeb\ShoppingFeeds\Model\Generator;

use Magento\Framework\Model\AbstractModel;

/**
 * Class Queue
 * @package RocketWeb\ShoppingFeeds\Model\Generator
 *
 * @method $this    setParentItemId(int $parentId)
 * @method $this    setStatus(int $status)
 * @method $this    setFeedId(int $feedId)
 * @method $this    setItemId(int $id)
 * @method int      getStatus()
 * @method int      getParentItemId()
 */
class Process extends AbstractModel
{
    const STATUS_PENDING = 0;
    const STATUS_PROCESSED = 1;

    /**
     * Initialize resource model
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init('RocketWeb\ShoppingFeeds\Model\ResourceModel\Generator\Process');
    }
}