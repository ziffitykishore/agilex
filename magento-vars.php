<?php

// Master
if (isHttpHost("master-7rqtwti-kzhstxzybfg6w.us-3.magentosite.cloud")) {
    $_SERVER["MAGE_RUN_CODE"] = "base";
    $_SERVER["MAGE_RUN_TYPE"] = "website";
}

// Production
if (isHttpHost("mcprod.earthlite.com")) {
    $_SERVER["MAGE_RUN_CODE"] = "base";
    $_SERVER["MAGE_RUN_TYPE"] = "website";
}

if (isHttpHost("mcprod.continuumpedicure.com")) {
    $_SERVER["MAGE_RUN_CODE"] = "continuumpedicure";
    $_SERVER["MAGE_RUN_TYPE"] = "website";
}

if (isHttpHost("mcprod.livingearthcrafts.com")) {
    $_SERVER["MAGE_RUN_CODE"] = "livingearthcraft";
    $_SERVER["MAGE_RUN_TYPE"] = "website";
}

if (isHttpHost("mcprod.taraspatherapy.com")) {
    $_SERVER["MAGE_RUN_CODE"] = "taraspatherapy";
    $_SERVER["MAGE_RUN_TYPE"] = "website";
}

/*
if (isHttpHost("earthlite.com")) {
    $_SERVER["MAGE_RUN_CODE"] = "base";
    $_SERVER["MAGE_RUN_TYPE"] = "website";
}

if (isHttpHost("continuumpedicure.com")) {
    $_SERVER["MAGE_RUN_CODE"] = "continuumpedicure";
    $_SERVER["MAGE_RUN_TYPE"] = "website";
}

if (isHttpHost("livingearthcrafts.com")) {
    $_SERVER["MAGE_RUN_CODE"] = "livingearthcraft";
    $_SERVER["MAGE_RUN_TYPE"] = "website";
}

if (isHttpHost("taraspatherapy.com")) {
    $_SERVER["MAGE_RUN_CODE"] = "taraspatherapy";
    $_SERVER["MAGE_RUN_TYPE"] = "website";
}

if (isHttpHost("www.earthlite.com")) {
    $_SERVER["MAGE_RUN_CODE"] = "base";
    $_SERVER["MAGE_RUN_TYPE"] = "website";
}

if (isHttpHost("www.continuumpedicure.com")) {
    $_SERVER["MAGE_RUN_CODE"] = "continuumpedicure";
    $_SERVER["MAGE_RUN_TYPE"] = "website";
}

if (isHttpHost("www.livingearthcrafts.com")) {
    $_SERVER["MAGE_RUN_CODE"] = "livingearthcraft";
    $_SERVER["MAGE_RUN_TYPE"] = "website";
}

if (isHttpHost("www.taraspatherapy.com")) {
    $_SERVER["MAGE_RUN_CODE"] = "taraspatherapy";
    $_SERVER["MAGE_RUN_TYPE"] = "website";
}

*/

// Staging
if (isHttpHost("mcstaging.earthlite.com")) {
    $_SERVER["MAGE_RUN_CODE"] = "base";
    $_SERVER["MAGE_RUN_TYPE"] = "website";
}

if (isHttpHost("mcstaging.continuumpedicure.com")) {
    $_SERVER["MAGE_RUN_CODE"] = "continuumpedicure";
    $_SERVER["MAGE_RUN_TYPE"] = "website";
}

if (isHttpHost("mcstaging.livingearthcrafts.com")) {
    $_SERVER["MAGE_RUN_CODE"] = "livingearthcraft";
    $_SERVER["MAGE_RUN_TYPE"] = "website";
}

if (isHttpHost("mcstaging.taraspatherapy.com")) {
    $_SERVER["MAGE_RUN_CODE"] = "taraspatherapy";
    $_SERVER["MAGE_RUN_TYPE"] = "website";
}

// Integration
if (isHttpHost("integration-5ojmyuq-kzhstxzybfg6w.us-3.magentosite.cloud")) {
    $_SERVER["MAGE_RUN_CODE"] = "base";
    $_SERVER["MAGE_RUN_TYPE"] = "website";
}

if (isHttpHost("livingearthcraft.integration-5ojmyuq-kzhstxzybfg6w.us-3.magentosite.cloud")) {
    $_SERVER["MAGE_RUN_CODE"] = "livingearthcraft";
    $_SERVER["MAGE_RUN_TYPE"] = "website";
}

if (isHttpHost("continuumpedicure.integration-5ojmyuq-kzhstxzybfg6w.us-3.magentosite.cloud")) {
    $_SERVER["MAGE_RUN_CODE"] = "continuumpedicure";
    $_SERVER["MAGE_RUN_TYPE"] = "website";
}

if (isHttpHost("taraspatherapy.integration-5ojmyuq-kzhstxzybfg6w.us-3.magentosite.cloud")) {
    $_SERVER["MAGE_RUN_CODE"] = "taraspatherapy";
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

