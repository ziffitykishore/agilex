<?php

namespace Ziffity\Bundle\Block\Catalog\Product\View\Type\Bundle\Option;

class Multi extends \Magento\Bundle\Block\Catalog\Product\View\Type\Bundle\Option
{

    protected $_template = 'Ziffity_Bundle::catalog/product/view/type/bundle/option/multi.phtml';

    protected function assignSelection(\Magento\Bundle\Model\Option $option, $selectionId)
    {
        if (is_array($selectionId)) {
            foreach ($selectionId as $id) {
                if ($id && $option->getSelectionById($id)) {
                    $this->_selectedOptions[] = $id;
                }
            }
        } else {
            parent::assignSelection($option, $selectionId);
        }
    }
}
