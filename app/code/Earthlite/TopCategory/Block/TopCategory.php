<?php
namespace Earthlite\TopCategory\Block;

use Earthlite\Core\Helper\Constant;

class TopCategory extends \Magento\Framework\View\Element\Template
{
    
    public function getLable()
    {
        return Constant::$topCategoryLable;
    }
}
