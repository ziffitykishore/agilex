<?php

/**
 * Product:       Xtento_OrderExport (2.6.6)
 * ID:            lXPdgIcrkYrqAkkYfQmiNUpRqDD5NOHfZ3XuYtzPwbA=
 * Packaged:      2018-09-18T14:52:22+00:00
 * Last Modified: 2016-02-25T18:38:44+00:00
 * File:          app/code/Xtento/OrderExport/Model/ResourceModel/Profile.php
 * Copyright:     Copyright (c) 2018 XTENTO GmbH & Co. KG <info@xtento.com> / All rights reserved.
 */

namespace Xtento\OrderExport\Model\ResourceModel;

class Profile extends \Magento\Framework\Model\ResourceModel\Db\AbstractDb
{
    protected function _construct()
    {
        $this->_init('xtento_orderexport_profile', 'profile_id');
    }
}
