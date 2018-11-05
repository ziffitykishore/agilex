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
function checkHost($domain)
{
    if (!isset($_SERVER['HTTP_HOST'])) {
        return false;
    }
    return strpos($_SERVER['HTTP_HOST'], $domain) !== false;
}

if (checkHost('travers') !== false) {
    $_SERVER['MAGE_RUN_CODE'] = 'default';
    $_SERVER['MAGE_RUN_TYPE'] = 'store';
} else {
    // Since we don't have the subdomain names configured
    // let's just make sure we're loading the default store
    // 
    // Travers currently has two domain names:
    // 
    // - www.traverscanada.com
    // - www.travers.com
    //
    // How we structure the subdomains is TBD
    //
    // @TODO We should remove this and rely on the subdomains 

    $_SERVER['MAGE_RUN_CODE'] = 'default';
    $_SERVER['MAGE_RUN_TYPE'] = 'store';
}

