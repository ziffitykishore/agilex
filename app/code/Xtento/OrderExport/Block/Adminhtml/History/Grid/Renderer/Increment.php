<?php

/**
 * Product:       Xtento_OrderExport (2.6.2)
 * ID:            lXPdgIcrkYrqAkkYfQmiNUpRqDD5NOHfZ3XuYtzPwbA=
 * Packaged:      2018-08-15T13:45:53+00:00
 * Last Modified: 2016-02-25T14:30:56+00:00
 * File:          app/code/Xtento/OrderExport/Block/Adminhtml/History/Grid/Renderer/Increment.php
 * Copyright:     Copyright (c) 2018 XTENTO GmbH & Co. KG <info@xtento.com> / All rights reserved.
 */

namespace Xtento\OrderExport\Block\Adminhtml\History\Grid\Renderer;

class Increment extends \Magento\Backend\Block\Widget\Grid\Column\Renderer\AbstractRenderer
{
    /**
     * Render increment ID
     *
     * @param \Magento\Framework\DataObject $row
     * @return string
     */
    public function render(\Magento\Framework\DataObject $row)
    {
        $incrementIdFields = ['increment_id', 'order_increment_id', 'invoice_increment_id', 'shipment_increment_id', 'creditmemo_increment_id'];
        foreach ($incrementIdFields as $incrementIdField) {
            if ($row->getData($incrementIdField) !== NULL) {
                return $row->getData($incrementIdField);
            }
        }
        return '';
    }
}
