<?php
namespace Wyomind\MassStockUpdate\Block\Adminhtml;
 
class Profiles extends \Magento\Backend\Block\Widget\Grid\Container
{
    protected function _construct()
    {
        $this->_controller = 'adminhtml_profiles';
        $this->_blockGroup = 'Wyomind_MassStockUpdate';
        $this->_headerText = __('Manage Profiles');
        parent::_construct();
        
        $this->updateButton('add', 'label', __('Create a new profile'));


        $this->addButton(
            "import", [
                "label" => __("Import profiles"),
                "class" => "add",
                "onclick" => "require(['wyomind_MassImportAndUpdate_import'], function (massproductimportandupdate) { massproductimportandupdate.import(); });"
            ]
        );
        
        
    }
}
