<?php
declare(strict_types=1);

namespace Earthlite\Category\Model;

/**
 * class CategoryGallery
 * 
 */
class CategoryGallery extends \Magento\Framework\Model\AbstractModel implements \Magento\Framework\DataObject\IdentityInterface
{
    const CACHE_TAG = 'category_media_gallery';
    
    /**
     * CategoryGallery construct that should be used for object initialization
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init('Earthlite\Category\Model\ResourceModel\CategoryGallery');
    }

    /**
     * 
     * @return []
     */
    public function getIdentities()
    {
        return [self::CACHE_TAG . '_' . $this->getId()];
    }
}
