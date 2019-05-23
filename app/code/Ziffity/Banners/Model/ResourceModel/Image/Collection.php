<?php

namespace Ziffity\Banners\Model\ResourceModel\Image;

use Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection;

class Collection extends AbstractCollection
{
    /**
     * @var string
     */
    protected $_idFieldName = 'image_id';

    /**
     * Collection initialisation
     */
    protected function _construct()
    {
        $this->_init(
            'Ziffity\Banners\Model\Image',
            'Ziffity\Banners\Model\ResourceModel\Image'
        );
    }
}
