<?php

/**
 * Copyright © 2016 Wyomind. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace Wyomind\AdvancedInventory\Block\Adminhtml\Sales\Order\Items\Renderer;

class DefaultRenderer extends \Magento\Sales\Block\Adminhtml\Items\Renderer\DefaultRenderer
{

    protected $_posFactory = null;
    protected $_permissionsHelper = null;

    public function __construct(
    \Magento\Backend\Block\Template\Context $context,
        \Wyomind\PointOfSale\Model\PointOfSaleFactory $posFactory,
        \Magento\CatalogInventory\Api\StockRegistryInterface $stockRegistry,
        \Magento\CatalogInventory\Api\StockConfigurationInterface $stockConfiguration,
        \Magento\Framework\Registry $registry,
        \Wyomind\AdvancedInventory\Helper\Permissions $permissionsHelper,
        array $data = []
    )
    {
        parent::__construct($context, $stockRegistry, $stockConfiguration, $registry, $data);
        $this->_posFactory = $posFactory;
        $this->_permissionsHelper = $permissionsHelper;
    }

    public function getPos()
    {
        $data = [];
        $places = $this->_posFactory->create()->getCollection();
        foreach ($places as $pos) {
            if ($this->_permissionsHelper->isAllowed($pos->getId())) {
                $data[] = $pos;
            }
        }
        return $data;
    }

}
