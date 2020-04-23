<?php
declare(strict_types=1);

namespace Earthlite\Category\Controller\Adminhtml\Category\Gallery;

/**
 * Class Upload
 */
class Upload extends \Magento\Catalog\Controller\Adminhtml\Product\Gallery\Upload
{
    /**
     * Authorization level of a basic admin session
     *
     * @see _isAllowed()
     */
    const ADMIN_RESOURCE = 'Magento_Catalog::categories';
    
}
