<?php

/**
 * Product:       Xtento_OrderExport (2.6.6)
 * ID:            lXPdgIcrkYrqAkkYfQmiNUpRqDD5NOHfZ3XuYtzPwbA=
 * Packaged:      2018-09-18T14:52:22+00:00
 * Last Modified: 2016-02-26T12:34:52+00:00
 * File:          app/code/Xtento/OrderExport/Block/Adminhtml/Destination/Edit/Tab/Type/Custom.php
 * Copyright:     Copyright (c) 2018 XTENTO GmbH & Co. KG <info@xtento.com> / All rights reserved.
 */

namespace Xtento\OrderExport\Block\Adminhtml\Destination\Edit\Tab\Type;

class Custom extends AbstractType
{
    // Custom Type Configuration
    public function getFields(\Magento\Framework\Data\Form $form)
    {
        $fieldset = $form->addFieldset('config_fieldset', [
            'legend' => __('Custom Type Configuration'),
            'class' => 'fieldset-wide'
        ]
        );

        $fieldset->addField('custom_class', 'text', [
            'label' => __('Custom Class Identifier'),
            'name' => 'custom_class',
            'note' => __('You can set up an own class in our (or another) module which gets called when exporting. The saveFiles($fileArray ($filename => $contents)) function would be called in your class. If your class is called \Xtento\OrderExport\Model\Destination\Myclass then the identifier to enter here would be \Xtento\OrderExport\Model\Destination\Myclass'),
            'required' => true
        ]
        );
    }
}