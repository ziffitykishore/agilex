<?php

namespace Wyomind\PointOfSale\Controller\Adminhtml\Attributes;

class Index extends \Wyomind\PointOfSale\Controller\Adminhtml\Attributes
{
    public function execute()
    {
        $this->_initAction(__('Attributes'));
        $this->_view->renderLayout();
    }
}