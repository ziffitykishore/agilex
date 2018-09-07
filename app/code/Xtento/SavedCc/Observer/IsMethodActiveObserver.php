<?php

/**
 * Product:       Xtento_SavedCc (1.0.6)
 * ID:            NZWbKguR/Yb8QYk68QaZWfj7V5pl/BlDdubJ/+3MKvg=
 * Packaged:      2018-08-15T13:47:06+00:00
 * Last Modified: 2017-08-12T16:34:06+00:00
 * File:          app/code/Xtento/SavedCc/Observer/IsMethodActiveObserver.php
 * Copyright:     Copyright (c) 2018 XTENTO GmbH & Co. KG <info@xtento.com> / All rights reserved.
 */

namespace Xtento\SavedCc\Observer;

use Magento\Framework\Event\ObserverInterface;
use Magento\Framework\Event\Observer as EventObserver;
use Xtento\SavedCc\Helper\Module;

class IsMethodActiveObserver implements ObserverInterface
{
    /**
     * @var Module
     */
    protected $moduleHelper;

    /**
     * IsMethodActiveObserver constructor.
     *
     * @param Module $moduleHelper
     */
    public function __construct(
        Module $moduleHelper
    ) {
        $this->moduleHelper = $moduleHelper;
    }

    /**
     * Check if module is enabled
     *
     * @param EventObserver $observer
     *
     * @return void
     */
    public function execute(EventObserver $observer)
    {
        $event = $observer->getEvent();
        /** @var \Magento\Payment\Model\Method\Adapter $methodInstance */
        $methodInstance = $event->getMethodInstance();
        if ($methodInstance->getCode() == \Xtento\SavedCc\Model\Ui\ConfigProvider::CODE && !$this->moduleHelper->isModuleEnabled()) {
            /** @var \Magento\Framework\DataObject $result */
            $result = $observer->getEvent()->getResult();
            $result->setData('is_available', false);
        }
    }
}
