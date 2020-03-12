<?php
// enable, adjust and copy this code for each store you run
// Store #0, default one
//if (isHttpHost("example.com")) {
//    $_SERVER["MAGE_RUN_CODE"] = "default";
//    $_SERVER["MAGE_RUN_TYPE"] = "store";
//}
//function isHttpHost($host)
//{
//    if (!isset($_SERVER['HTTP_HOST'])) {
//        return false;
//    }
//    return strpos(str_replace('---', '.', $_SERVER['HTTP_HOST']), $host) === 0;
//}

// Master
if (isHttpHost("master-7rqtwti-kzhstxzybfg6w.us-3.magentosite.cloud")) {
    $_SERVER["MAGE_RUN_CODE"] = "base";
    $_SERVER["MAGE_RUN_TYPE"] = "website";
}

// Production
if (isHttpHost("earthlite.com.c.kzhstxzybfg6w.ent.magento.cloud")) {
    $_SERVER["MAGE_RUN_CODE"] = "base";
    $_SERVER["MAGE_RUN_TYPE"] = "website";
}

if (isHttpHost("mcprod.earthlite.com")) {
    $_SERVER["MAGE_RUN_CODE"] = "base";
    $_SERVER["MAGE_RUN_TYPE"] = "website";
}

// Staging
if (isHttpHost("mcstaging.earthlite.com.c.kzhstxzybfg6w.dev.ent.magento.cloud")) {
    $_SERVER["MAGE_RUN_CODE"] = "base";
    $_SERVER["MAGE_RUN_TYPE"] = "website";
}

if (isHttpHost("mcstaging.earthlite.com")) {
    $_SERVER["MAGE_RUN_CODE"] = "base";
    $_SERVER["MAGE_RUN_TYPE"] = "website";
}

// Integration
if (isHttpHost("integration-5ojmyuq-kzhstxzybfg6w.us-3.magentosite.cloud")) {
    $_SERVER["MAGE_RUN_CODE"] = "base";
    $_SERVER["MAGE_RUN_TYPE"] = "website";
}

function isHttpHost($host)
{
    if (!isset($_SERVER['HTTP_HOST'])) {
        return false;
    }
    // return strpos(str_replace('---', '.', $_SERVER['HTTP_HOST']), $host) === 0;
    return $_SERVER['HTTP_HOST'] ===  $host;
}

