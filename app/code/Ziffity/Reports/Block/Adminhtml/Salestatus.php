<?php
namespace Ziffity\Reports\Block\Adminhtml;
class Salestatus extends \Magento\Backend\Block\Widget\Grid\Container
{
    /**
     * Constructor
     *
     * @return void
     */
    protected function _construct()
    {
       $this->_controller = 'adminhtml_salestatus';/*block grid.php directory*/
        $this->_blockGroup = 'Ziffity_Reports';
        $this->_headerText = __('Salestatus');
        $this->buttonList->remove('add');
        parent::_construct();
		
    }
}
