<?php

/**
 * Product:       Xtento_OrderExport (2.6.6)
 * ID:            lXPdgIcrkYrqAkkYfQmiNUpRqDD5NOHfZ3XuYtzPwbA=
 * Packaged:      2018-09-18T14:52:22+00:00
 * Last Modified: 2016-02-25T18:46:04+00:00
 * File:          app/code/Xtento/OrderExport/Model/Export/Condition/Action.php
 * Copyright:     Copyright (c) 2018 XTENTO GmbH & Co. KG <info@xtento.com> / All rights reserved.
 */

namespace Xtento\OrderExport\Model\Export\Condition;

class Action extends \Magento\Rule\Model\Condition\Combine
{
    /**
     * @var \Magento\Framework\Event\ManagerInterface
     */
    protected $_eventManager;

    /**
     * Action constructor.
     * @param \Magento\Rule\Model\Condition\Context $context
     * @param \Magento\Framework\Event\ManagerInterface $eventManager
     * @param array $data
     */
    public function __construct(
        \Magento\Rule\Model\Condition\Context $context,
        \Magento\Framework\Event\ManagerInterface $eventManager,
        array $data = []
    ) {
        $this->_eventManager = $eventManager;
        parent::__construct($context, $data);
        $this->setType('Xtento\OrderExport\Model\Export\Condition\Action');
    }

    /**
     * Get new child select options
     *
     * @return array
     */
    public function getNewChildSelectOptions()
    {
        return [];
    }
}
