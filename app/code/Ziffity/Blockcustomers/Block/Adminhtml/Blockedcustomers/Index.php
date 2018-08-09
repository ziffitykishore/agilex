<?php
namespace Ziffity\Blockcustomers\Block\Adminhtml\Blockedcustomers;
/**
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * This is the Summary for this element.
 * 
 * @inheritDoc
 */
class Index extends \Magento\Backend\Block\Widget\Grid\Container
{
    protected function _construct()
    {
        $this->_controller = 'adminhtml_blockedcustomers';/*block grid.php directory*/
        $this->_blockGroup = 'Ziffity_Blockcustomers';
        $this->_headerText = __('Blocked Customers List');
       
        parent::_construct();
         $this->buttonList->remove('add');
    }
}
