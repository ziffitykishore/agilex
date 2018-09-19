<?php

/**
 * Product:       Xtento_OrderExport (2.6.6)
 * ID:            lXPdgIcrkYrqAkkYfQmiNUpRqDD5NOHfZ3XuYtzPwbA=
 * Packaged:      2018-09-18T14:52:22+00:00
 * Last Modified: 2016-02-25T18:39:20+00:00
 * File:          app/code/Xtento/OrderExport/Block/Adminhtml/Destination/Edit/Tab/Type/AbstractType.php
 * Copyright:     Copyright (c) 2018 XTENTO GmbH & Co. KG <info@xtento.com> / All rights reserved.
 */

namespace Xtento\OrderExport\Block\Adminhtml\Destination\Edit\Tab\Type;

use Magento\Config\Model\Config\Source\Yesno;

abstract class AbstractType extends \Magento\Framework\View\Element\AbstractBlock
{
    /**
     * @var \Magento\Framework\Registry
     */
    protected $registry;

    /**
     * @var Yesno
     */
    protected $yesNo;

    /**
     * AbstractType constructor.
     * @param \Magento\Framework\View\Element\Context $context
     * @param \Magento\Framework\Registry $registry
     * @param Yesno $yesNo
     * @param array $data
     */
    public function __construct(
        \Magento\Framework\View\Element\Context $context,
        \Magento\Framework\Registry $registry,
        Yesno $yesNo,
        array $data = []
    ) {
        parent::__construct($context, $data);
        $this->registry = $registry;
        $this->yesNo = $yesNo;
    }
}