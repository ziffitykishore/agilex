<?php
declare(strict_types=1);

namespace Earthlite\Category\Model\ResourceModel\CategoryGallery;

/**
 * class Collection
 */
class Collection extends \Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection
{
    /**
     * @return void
     */
    protected function _construct()
    {
        $this->_init(
                \Earthlite\Category\Model\CategoryGallery::class,
                \Earthlite\Category\Model\ResourceModel\CategoryGallery::class
        );
    }
}