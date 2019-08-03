<?php

namespace Wyomind\PointOfSale\Controller\Adminhtml\Attributes;

class NewAction extends \Wyomind\PointOfSale\Controller\Adminhtml\Attributes
{
    public function execute()
    {
        $this->_forward('edit');
    }
}