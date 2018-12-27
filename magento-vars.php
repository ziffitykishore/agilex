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

if (checkHost('traverscanada')) {
    $_SERVER['MAGE_RUN_CODE'] = 'canada';
    $_SERVER['MAGE_RUN_TYPE'] = 'store';
} else if (checkHost('travers')) {
    $_SERVER['MAGE_RUN_CODE'] = 'default';
    $_SERVER['MAGE_RUN_TYPE'] = 'store';
}
