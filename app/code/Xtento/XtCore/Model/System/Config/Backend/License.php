<?php

/**
 * Product:       Xtento_XtCore (2.3.0)
 * ID:            NZWbKguR/Yb8QYk68QaZWfj7V5pl/BlDdubJ/+3MKvg=
 * Packaged:      2018-09-18T14:51:41+00:00
 * Last Modified: 2017-08-16T08:52:13+00:00
 * File:          app/code/Xtento/XtCore/Model/System/Config/Backend/License.php
 * Copyright:     Copyright (c) 2018 XTENTO GmbH & Co. KG <info@xtento.com> / All rights reserved.
 */

namespace Xtento\XtCore\Model\System\Config\Backend;

class License extends \Magento\Framework\App\Config\Value
{
    public function beforeSave()
    {
        $this->_registry->register('xtento_configuration_license_key', $this->getValue(), true);
    }
}
