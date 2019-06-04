<?php

// If multistore, this is where MAGE_RUN_CODE and MAGE_RUN_TYPE need to be set.
// Even if not multistore, this file must exist on Magento Cloud.

if (isset($_SERVER['HTTP_HOST'])) {
    // $_SERVER['MAGE_RUN_CODE'] = '???';
    // $_SERVER['MAGE_RUN_TYPE'] = 'store';
}
