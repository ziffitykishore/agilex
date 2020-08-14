<?php

/**
 * UUID class
 *
 * The following class generates VALID RFC 4122 COMPLIANT
 * Universally Unique IDentifiers (UUID) version 1, 3, 4 and 5.
 *
 * UUIDs generated validates using OSSP UUID Tool, and output
 * for named-based UUIDs are exactly the same. This is a pure
 * PHP implementation.
 *
 * @author Andrew Moore
 * @author webpatser/laravel-uuid
 * @link   http://www.php.net/manual/en/function.uniqid.php#94959
 */
namespace Unirgy\RapidFlowSales\Model\Misc;

class Uuid
{
    /**
     * @var string
     */
    const NS_DNS = '6ba7b810-9dad-11d1-80b4-00c04fd430c8';

    /**
     * @var string
     */
    const NS_URL = '6ba7b811-9dad-11d1-80b4-00c04fd430c8';

    /**
     * @var string
     */
    const NS_OID = '6ba7b812-9dad-11d1-80b4-00c04fd430c8';

    /**
     * @var string
     */
    const NS_X500 = '6ba7b814-9dad-11d1-80b4-00c04fd430c8';
    const MD5     = 3;
    const SHA1    = 5;
    /**
     * 00001111  Clears all bits of version byte with AND
     *
     * @var int
     */
    const CLEAR_VER = 15;
    /**
     * 00111111  Clears all relevant bits of variant byte with AND
     *
     * @var int
     */
    const CLEAR_VAR = 63;
    /**
     * 11100000  Variant reserved for future use
     *
     * @var int
     */
    const VAR_RES = 224;
    /**
     * 11000000  Microsoft UUID variant
     *
     * @var int
     */
    const VAR_MS = 192;
    /**
     * 10000000  The RFC 4122 variant (this variant)
     *
     * @var int
     */
    const VAR_RFC = 128;
    /**
     * 00000000  The NCS compatibility variant
     *
     * @var int
     */
    const VAR_NCS = 0;
    /**
     * 00010000
     * @var int
     */
    const VERSION_1 = 16;
    /**
     * 00110000
     * @var int
     */
    const VERSION_3 = 48;
    /**
     * 01000000
     * @var int
     */
    const VERSION_4 = 64;
    /**
     * 01010000
     * @var int
     */
    const VERSION_5 = 80;
    /**
     * Time (in 100ns steps) between the start of the UTC and Unix epochs
     *
     * @var int
     */
    const INTERVAL = 0x01b21dd213814000;
    /**
     * @var string
     */
    protected static $randomFunc = 'randomMcrypt';

    /**
     * Generate v3 UUID
     *
     * Version 3 UUIDs are named based. They require a namespace (another
     * valid UUID) and a value (the name). Given the same namespace and
     * name, the output is always the same.
     *
     * @param    string $namespace - valid uuid sring
     * @param    string $name
     * @return bool|string
     */
    public static function v3($namespace, $name)
    {
        if (!self::is_valid($namespace)) {
            return false;
        }

        // Get hexadecimal components of namespace
        $nhex = str_replace(['-', '{', '}'], '', $namespace);

        // Binary Value
        $nstr = '';

        // Convert Namespace UUID to bits
        for ($i = 0, $lNhex = strlen($nhex); $i < $lNhex; $i += 2) {
            $nstr .= chr(hexdec($nhex[$i] . $nhex[$i + 1]));
        }

        // Calculate hash value
        $hash = md5($nstr . $name);

        return sprintf('%08s-%04s-%04x-%04x-%12s',

            // 32 bits for "time_low"
            substr($hash, 0, 8),

            // 16 bits for "time_mid"
            substr($hash, 8, 4),

            // 16 bits for "time_hi_and_version",
            // four most significant bits holds version number 3
            (hexdec(substr($hash, 12, 4)) & 0x0fff) | 0x3000,

            // 16 bits, 8 bits for "clk_seq_hi_res",
            // 8 bits for "clk_seq_low",
            // two most significant bits holds zero and one for variant DCE1.1
            (hexdec(substr($hash, 16, 4)) & 0x3fff) | 0x8000,

            // 48 bits for "node"
            substr($hash, 20, 12)
        );
    }

    public static function is_valid($uuid)
    {
        return preg_match('/^\{?[0-9a-f]{8}\-?[0-9a-f]{4}\-?[0-9a-f]{4}\-?' .
                          '[0-9a-f]{4}\-?[0-9a-f]{12}\}?$/i', $uuid) === 1;
    }

    /**
     *
     * Generate v4 UUID
     *
     * Version 4 UUIDs are pseudo-random.
     *
     * @throws \Exception
     */
    public static function v4()
    {
        $uuid = static::randomBytes(16);
        // set variant
        $uuid[8] = chr(ord($uuid[8]) & static::CLEAR_VAR | static::VAR_RFC);
        // set version
        $uuid[6] = chr(ord($uuid[6]) & static::CLEAR_VER | static::VERSION_4);

        return self::_stringify($uuid);
    }

    /**
     * Generate v5 UUID
     *
     * Version 5 UUIDs are named based. They require a namespace (another
     * valid UUID) and a value (the name). Given the same namespace and
     * name, the output is always the same.
     *
     * @param    string $namespace - valid uuid string
     * @param    string $name
     * @return bool|string
     */
    public static function v5($namespace, $name)
    {
        if (!self::is_valid($namespace)) {
            return false;
        }

        // Get hexadecimal components of namespace
        $nhex = str_replace(['-', '{', '}'], '', $namespace);

        // Binary Value
        $nstr = '';

        // Convert Namespace UUID to bits
        for ($i = 0, $lnHex = strlen($nhex); $i < $lnHex; $i += 2) {
            $nstr .= chr(hexdec($nhex[$i] . $nhex[$i + 1]));
        }

        // Calculate hash value
        $hash = sha1($nstr . $name);

        return sprintf('%08s-%04s-%04x-%04x-%12s',

            // 32 bits for "time_low"
            substr($hash, 0, 8),

            // 16 bits for "time_mid"
            substr($hash, 8, 4),

            // 16 bits for "time_hi_and_version",
            // four most significant bits holds version number 5
            (hexdec(substr($hash, 12, 4)) & 0x0fff) | 0x5000,

            // 16 bits, 8 bits for "clk_seq_hi_res",
            // 8 bits for "clk_seq_low",
            // two most significant bits holds zero and one for variant DCE1.1
            (hexdec(substr($hash, 16, 4)) & 0x3fff) | 0x8000,

            // 48 bits for "node"
            substr($hash, 20, 12)
        );
    }

    /**
     * Generates a Version 1 UUID.
     * These are derived from the time at which they were generated.
     *
     * @param string $node
     * @return string
     * @throws \Exception
     */
    public static function v1($node = null)
    {

        /**
         * Get time since Gregorian calendar reform in 100ns intervals
         * This is exceedingly difficult because of PHP's (and pack()'s)
         * integer size limits.
         * Note that this will never be more accurate than to the microsecond.
         */
        $time = microtime(1) * 10000000 + static::INTERVAL;

        // Convert to a string representation
        $time = sprintf('%F', $time);

        //strip decimal point
        preg_match("/^\d+/", $time, $time);

        // And now to a 64-bit binary representation
        $time = base_convert($time[0], 10, 16);
        $time = pack('H*', str_pad($time, 16, '0', STR_PAD_LEFT));

        // Reorder bytes to their proper locations in the UUID
        $uuid = $time[4] . $time[5] . $time[6] . $time[7] . $time[2] . $time[3] . $time[0] . $time[1];

        // Generate a random clock sequence
        $uuid .= static::randomBytes(2);

        // set variant
        $uuid[8] = chr(ord($uuid[8]) & static::CLEAR_VAR | static::VAR_RFC);

        // set version
        $uuid[6] = chr(ord($uuid[6]) & static::CLEAR_VER | static::VERSION_1);

        // Set the final 'node' parameter, a MAC address
        if (null !== $node) {
            $node = static::makeBin($node, 6);
        }

        // If no node was provided or if the node was invalid,
        //  generate a random MAC address and set the multicast bit
        if (null === $node) {
            $node    = static::randomBytes(6);
            $node[0] = pack('C', ord($node[0]) | 1);
        }

        $uuid .= $node;

        return self::_stringify($uuid);
    }

    /**
     * Randomness is returned as a string of bytes
     *
     * @param $bytes
     * @return string
     * @throws \Exception
     */
    public static function randomBytes($bytes)
    {
        return call_user_func(['static', static::initRandom()], $bytes);
    }

    /**
     * Insure that an input string is either binary or hexadecimal.
     * Returns binary representation, or false on failure.
     *
     * @param string  $str
     * @param integer $len
     * @return string|null
     */
    protected static function makeBin($str, $len)
    {
        //if ($str instanceof self) {
        //    return $str->bytes;
        //}
        if (strlen($str) === $len) {
            return $str;
        } else {
            // strip URN scheme and namespace
            $str = preg_replace('/^urn:uuid:/is', '', $str);
        }
        // strip non-hex characters
        $str = preg_replace('/[^a-f0-9]/is', '', $str);
        if (strlen($str) !== ($len * 2)) {
            return null;
        } else {
            return pack('H*', $str);
        }
    }

    /**
     * Trying for php 7 secure random generator, falling back to openSSL and Mcrypt.
     * If none of the above is found, falls back to mt_rand
     * Since laravel 4.* and 5.0 requires Mcrypt and 5.1 requires OpenSSL the fallback should never be used.
     *
     * @throws Exception
     * @return string
     */
    public static function initRandom()
    {
        if (function_exists('random_bytes')) {
            return 'randomPhp7';
        } elseif (function_exists('openssl_random_pseudo_bytes')) {
            return 'randomOpenSSL';
        } elseif (function_exists('mcrypt_encrypt')) {
            return 'randomMcrypt';
        }

        // This is not the best randomizer (using mt_rand)...
        return 'randomTwister';
    }

    /**
     * Get the specified number of random bytes, using random_bytes().
     * Randomness is returned as a string of bytes
     *
     * Requires Php 7, or random_compact polyfill
     *
     * @param $bytes
     * @return mixed
     */
    protected static function randomPhp7($bytes)
    {
        return random_bytes($bytes);
    }

    /**
     * Get the specified number of random bytes, using openssl_random_pseudo_bytes().
     * Randomness is returned as a string of bytes.
     *
     * @param $bytes
     * @return mixed
     */
    protected static function randomOpenSSL($bytes)
    {
        return openssl_random_pseudo_bytes($bytes);
    }

    /**
     * Get the specified number of random bytes, using mcrypt_create_iv().
     * Randomness is returned as a string of bytes.
     *
     * @param $bytes
     * @return string
     */
    protected static function randomMcrypt($bytes)
    {
        return mcrypt_create_iv($bytes, MCRYPT_DEV_URANDOM);
    }

    /**
     * Get the specified number of random bytes, using mt_rand().
     * Randomness is returned as a string of bytes.
     *
     * @param integer $bytes
     * @return string
     */
    protected static function randomTwister($bytes)
    {
        $rand = '';
        for ($a = 0; $a < $bytes; $a++) {
            $rand .= chr(mt_rand(0, 255));
        }

        return $rand;
    }

    /**
     * @param $uuid
     * @return string
     */
    protected static function _stringify($uuid)
    {
        $strUuid = bin2hex(substr($uuid, 0, 4)) . '-' .
                   bin2hex(substr($uuid, 4, 2)) . '-' .
                   bin2hex(substr($uuid, 6, 2)) . '-' .
                   bin2hex(substr($uuid, 8, 2)) . '-' .
                   bin2hex(substr($uuid, 10, 6));

        return $strUuid;
    }
}

