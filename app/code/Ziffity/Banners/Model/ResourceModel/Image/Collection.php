<?php
// @codingStandardsIgnoreFile
/*
 * Ziffity_Banners
 */
namespace Ziffity\Banners\Model\ResourceModel\Image;

use Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection;

/**
 * @SuppressWarnings(PHPMD.CamelCasePropertyName)
 */

class Collection extends AbstractCollection
{
    /**
     * @var string
     */
    protected $_idFieldName = 'image_id';

    /**
     * @SuppressWarnings(PHPMD.CamelCaseMethodName)
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
