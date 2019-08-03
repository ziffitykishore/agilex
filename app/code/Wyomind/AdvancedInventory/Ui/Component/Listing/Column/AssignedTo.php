<?php
/**
 * Copyright © 2018 Wyomind. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace Wyomind\AdvancedInventory\Ui\Component\Listing\Column;

/**
 * Render column block in the order grid
 */
class AssignedTo extends \Magento\Ui\Component\Listing\Columns\Column
{
    protected $_blockColumn = null;

    /**
     * AssignedTo constructor.
     * @param \Magento\Framework\View\Element\UiComponent\ContextInterface $context
     * @param \Magento\Framework\View\Element\UiComponentFactory $uiComponentFactory
     * @param \Wyomind\AdvancedInventory\Block\Adminhtml\Assignation\Column $blockColumn
     * @param array $components
     * @param array $data
     */
    public function __construct(
        \Magento\Framework\View\Element\UiComponent\ContextInterface $context,
        \Magento\Framework\View\Element\UiComponentFactory $uiComponentFactory,
        \Wyomind\AdvancedInventory\Block\Adminhtml\Assignation\Column $blockColumn,
        array $components = [],
        array $data = []
    )
    {
        $this->_blockColumn = $blockColumn;

        parent::__construct($context, $uiComponentFactory, $components, $data);
    }

    /**
     * @param array $dataSource
     * @return array
     */
    public function prepareDataSource(array $dataSource)
    {
        if (isset($dataSource['data']['items'])) {
            foreach ($dataSource['data']['items'] as & $item) {
                $item[$this->getData('name')] = $this->_blockColumn->getAssignation($item);
            }
        }

        return $dataSource;
    }
}