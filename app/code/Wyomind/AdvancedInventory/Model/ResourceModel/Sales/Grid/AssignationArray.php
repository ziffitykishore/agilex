<?php
/**
 * Copyright © 2018 Wyomind. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace Wyomind\AdvancedInventory\Model\ResourceModel\Sales\Grid;

class AssignationArray implements \Magento\Framework\Option\ArrayInterface
{
    /**
     * @var null|\Wyomind\AdvancedInventory\Helper\Permissions
     */
    protected $_helperPermissions = null;

    /**
     * @var null|\Wyomind\PointOfSale\Model\PointOfSale
     */
    protected $_posModel = null;

    /**
     * AssignationArray constructor.
     * @param \Wyomind\AdvancedInventory\Helper\Permissions $helperPermissions
     * @param \Wyomind\PointOfSale\Model\PointOfSale $posModel
     */
    public function __construct(
        \Wyomind\AdvancedInventory\Helper\Permissions $helperPermissions,
        \Wyomind\PointOfSale\Model\PointOfSale $posModel
    ) {
        $this->_helperPermissions = $helperPermissions;
        $this->_posModel = $posModel;
    }

    public function toOptionArray()
    {
        $data = [];

        if ($this->_helperPermissions->isAllowed(0)) {
            $data[] = ["label" => __('Unassigned'), "value" => "0"];
        }

        foreach ($this->_posModel->getPlaces() as $p) {
            if ($this->_helperPermissions->isAllowed($p->getPlaceId())) {
                $data[] = ["label" => $p->getName(), "value" => $p->getPlaceId()];
            }
        }
        return $data;
    }
}
