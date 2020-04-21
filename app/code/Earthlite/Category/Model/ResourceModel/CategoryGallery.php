<?php
declare(strict_types=1);

namespace Earthlite\Category\Model\ResourceModel;

/**
 * class CategoryGallery
 * 
 */
class CategoryGallery extends \Magento\Framework\Model\ResourceModel\Db\AbstractDb
{
    /**
     * @return void
     */
    protected function _construct()
    {
        $this->_init('earthlite_catalog_category_entity_media_gallery','value_id');
    }
}
