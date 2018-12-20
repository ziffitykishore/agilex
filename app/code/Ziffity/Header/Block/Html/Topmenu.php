<?php

namespace Ziffity\Header\Block\Html;

use Magento\Framework\DataObject;

class Topmenu extends \Magento\Theme\Block\Html\Topmenu
{
    protected function _getHtml(
        \Magento\Framework\Data\Tree\Node $menuTree,
        $childrenWrapClass,
        $limit,
        $colBrakes = []
    ) {

        $html = parent::_getHtml($menuTree, $childrenWrapClass, $limit, $colBrakes = []);
        $transportObject = new DataObject(['html' => $html, 'menu_tree' => $menuTree]);

        $this->_eventManager->dispatch(
            'topmenu_node_gethtml_after',
            [
                'menu' => $this->_menu,
                'transport' => $transportObject
            ]
        );

        $html = $transportObject->getHtml();

        return $html;
    }
}
