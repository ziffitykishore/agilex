<?php

/**
 * Simple check to see if the HTTP_HOST contains
 * a specific "$domain" name
 *
 * Example:
 *
 * - https://www.example.com
 *   - $domain = "example"
 *   - returns: true
 *
 * @return bool
 */
/*
function checkHost($domain)
{
    if (!isset($_SERVER['HTTP_HOST'])) {
        return false;
    }
    return strpos($_SERVER['HTTP_HOST'], $domain) !== false;
}

if (checkHost('traverscanada')) {
    $_SERVER['MAGE_RUN_CODE'] = 'canada';
    $_SERVER['MAGE_RUN_TYPE'] = 'store';
} else if (checkHost('travers')) {
    $_SERVER['MAGE_RUN_CODE'] = 'default';
    $_SERVER['MAGE_RUN_TYPE'] = 'store';
}
*/

function isHttpHost($host)
{
    if (!isset($_SERVER['HTTP_HOST'])) {
        return false;
    }
    return strpos(str_replace('---', '.', $_SERVER['HTTP_HOST']), $host) === 0;
    // return $_SERVER['HTTP_HOST'] ===  $host;
}

// McProd
if (isHttpHost("mcprod.travers.com")) {
    $_SERVER["MAGE_RUN_CODE"] = "base";
    $_SERVER["MAGE_RUN_TYPE"] = "website";
}

if (isHttpHost("mcprod.traverscanada.com")) {
    $_SERVER["MAGE_RUN_CODE"] = "web_canada";
    $_SERVER["MAGE_RUN_TYPE"] = "website";
}

// McStaging
if (isHttpHost("mcstaging.travers.com")) {
    $_SERVER["MAGE_RUN_CODE"] = "base";
    $_SERVER["MAGE_RUN_TYPE"] = "website";
}

if (isHttpHost("mcstaging.traverscanada.com")) {
    $_SERVER["MAGE_RUN_CODE"] = "web_canada";
    $_SERVER["MAGE_RUN_TYPE"] = "website";
}

// McStaging2
if (isHttpHost("mcstaging2.travers.com")) {
    $_SERVER["MAGE_RUN_CODE"] = "base";
    $_SERVER["MAGE_RUN_TYPE"] = "website";
}

if (isHttpHost("mcstaging2.traverscanada.com")) {
    $_SERVER["MAGE_RUN_CODE"] = "web_canada";
    $_SERVER["MAGE_RUN_TYPE"] = "website";
}

