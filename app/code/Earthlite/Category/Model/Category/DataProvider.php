<?php
declare(strict_types=1);
namespace Earthlite\Category\Model\Category;

/**
 * class DataProvider
 */
class DataProvider extends \Magento\Catalog\Model\Category\DataProvider
{
    /**
     * @return []
     */
    protected function getFieldsMap()
    {
        $fields = parent::getFieldsMap();
        $fields['content'][] = 'banner_image';
        return $fields;
    }

}