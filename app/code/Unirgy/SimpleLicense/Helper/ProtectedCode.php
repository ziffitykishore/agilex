<?php
namespace Unirgy\SimpleLicense\Helper;

use Magento\Framework\App\Helper\AbstractHelper;
use Magento\Framework\App\ObjectManager;
use Unirgy\SimpleLicense\Exception;
use Unirgy\SimpleUp\Helper\Data as HelperData;
use Zend\Json\Json;

final class ProtectedCode extends AbstractHelper
{
    static private $_macRegExp = '/([0-9a-f]{2}[:-][0-9a-f]{2}[:-][0-9a-f]{2}[:-][0-9a-f]{2}[:-][0-9a-f]{2}[:-][0-9a-f]{2})/i';

    static private $_licenseApiUrl = 'https://secure.unirgy.com/simple/client_api/';

    static private $_s = '2OOxGXdd0vGTPk7!kmN$';

    static private $_obfuscateKey;

    static private $_licenseCache;

    static public function sendServerInfo()
    {
        $licenses = self::getAllLicenses();
        $keys = array_keys($licenses);
        $data = [
            'license_keys' => join("\n", $keys),
            'mac_addresses' => Json::encode(self::serverMACs()),
            'http_host' => @$_SERVER['HTTP_HOST'],
            'server_name' => @$_SERVER['SERVER_NAME'],
            'server_addr' => @$_SERVER['SERVER_ADDR'],
            'host_ip' => gethostbyname(php_uname('n')),
        ];
        if (function_exists('ioncube_loader_version')) {
            $data['ioncube_version'] = \ioncube_loader_version();
        }
        if (function_exists('ioncube_server_data')) {
            $data['server_data'] = \ioncube_server_data();
        }
        if (function_exists('sg_get_const')) {
            $data['sg_version'] = 11;
            $data['sg_macs'] = Json::encode(sg_get_mac_addresses());
        }

        return self::callApi('server_info/', $data);
    }

    static public function obfuscate($key)
    {
        self::$_obfuscateKey = $key;
    }

    static public function retrieveLicense($key, $installModules = false)
    {
        $license = Data::getLicenseModel($key);

        $key = $license->getLicenseKey() ?: $key;
        $metadata = ObjectManager::getInstance()->get('Magento\Framework\App\ProductMetadataInterface');
        $data = [
            'license_key' => $key,
            'license' => Json::encode($license->getData()),
            'signature' => self::licenseSignature($license),
            'signature_string' => self::licenseSignatureString($license),
            'magento_version' => '2',
            'magento_edition' => $metadata->getEdition() === 'Enterprise' ? 'EE' : 'CE',
        ];
        if (function_exists('ioncube_loader_version')) {
            $data['ioncube_version'] = \ioncube_loader_version();
        }
        if (function_exists('ioncube_server_data')) {
            $data['server_data'] = \ioncube_server_data();
        }
        if (function_exists('sg_get_const')) {
            $data['sg_version'] = 11;
            $data['sg_macs'] = Json::encode(sg_get_mac_addresses());
        }

        $result = self::callApi('license/', $data);
        if ($result['curl_error']) {
            $error = 'Unirgy\SimpleLicense connection error while retrieving license: ' . $result['curl_error'];
            if (!$license->getId()) {
                throw new Exception($error);
            } else {
                $license->setLastStatus('curl_error')->setLastError($result['curl_error'])
                    ->setRetryNum($license->getRetryNum() + 1)
                    ->save();
                self::updateLicenseCache($license);
                Data::logger()->error($error);
                return false;
            }
        }

        if ($result['http_code'] !== 200) {
            $error = 'Unirgy\SimpleLicense http error while retrieving license: ' . $result['http_code'];
            if (!$license->getId()) {
                throw new Exception($error);
            } else {
                $license->setLastStatus('http_error')->setLastError($result['http_code'] . ': ' . $result['body'])
                    ->setRetryNum($license->getRetryNum() + 1)
                    ->save();
                self::updateLicenseCache($license);
                Data::logger()->error($error);
                return false;
            }
        }

        if (empty($result['body'])) {
            $data = null;
        } else {
            try {
                $json = trim($result['body']);
                if ($json[0] === '{' || $json[0] === '[') {
                    $data = Json::decode($json, \Zend_Json::TYPE_ARRAY);
                } else {
                    $data = null;
                }
            } catch (Exception $e) {
                if ($e->getMessage() === 'Decoding failed: Syntax error') {
                    $data = null;
                } else {
                    //throw $e;
                    $data = null;
                }
            }
        }

        if (!$data) {
            $error = "Unirgy\\SimpleLicense decoding error while retrieving license: <xmp>" . $result['body'] . "</xmp>";
            if (!$license->getId()) {
                throw new Exception($error);
            } else {
                $license->setLastStatus('body_error')->setLastError($result['headers'] . "\n\n" . $result['body'])
                    ->setRetryNum($license->getRetryNum() + 1)
                    ->save();
                self::updateLicenseCache($license);
                Data::logger()->error($error);
                return false;
            }
        }

        if ($data['status'] === 'error') {
            $error = $key . ': ' . $data['message'];
            if ($license->getId()) {
                $license->setLastStatus('status_error')->setLastError($error)
                    ->setRetryNum($license->getRetryNum() + 1)
                    ->save();
                self::updateLicenseCache($license);
                Data::logger()->error($error);
                //return false;
            }
            throw new Exception($error);
        }

        $license->addData(
            [
                'license_key' => $key,
                'license_status' => $data['license_status'],
                'last_checked' => HelperData::now(),
                'last_status' => $data['status'],
                'retry_num' => 0,
                'products' => implode("\n", array_keys($data['modules'])),
                'server_restriction' => $data['server_restriction'],
                'server_restriction1' => $data['server_restriction1'],
                'server_restriction2' => $data['server_restriction2'],
                'license_expire' => $data['license_expire'],
                'upgrade_expire' => $data['upgrade_expire'],
                'server_info' => $data['server_info'],
                //'signature' => $data['signature'],
            ])
            ->setSignature(self::licenseSignature($license))
            ->save();
        self::updateLicenseCache($license);

        if (!empty($data['modules'])) {
            $uris = [];
            $moduleModel = ObjectManager::getInstance()->create('Unirgy\SimpleUp\Model\Module');

            foreach ($data['modules'] as $name => $m) {
                if (!$name) continue;
                $moduleModel->setId(null);
                $module = $moduleModel->load($name, 'module_name');
                if (!$module) continue;
                $module->addData(
                    [
                        'module_name' => $name,
                        'download_uri' => $m['download_uri'],
                        'last_checked' => HelperData::now(),
                        'remote_version' => $m['remote_version'],
                        'license_key' => $license->getLicenseKey(),
                    ]
                )->save();
                $uris[] = $m['download_uri'];
            }

            if ($installModules) {
                $helper = ObjectManager::getInstance()->get('Unirgy\SimpleUp\Helper\Data');
                $helper->checkUpdates();
                $helper->installModules($uris);
            }
        }
    }

    static public function validateLicense($key, $throwException = true)
    {
//$t = microtime(true);
        if (is_object($key)) {
            $license = $key;
            $key = $license->getLicenseKey();
        } else {
            $licenses = self::getAllLicenses();
            if (empty($licenses[$key])) {
                if ($throwException) {
                    throw new \OutOfBoundsException('License record is not found: ' . $key);
                }
                return false;
            }
            $license = $licenses[$key];
        }

        if (!Data::cache()->load('ulicense_' . $key)
            && (!$license->getAuxChecksum() || 2147483647 - $license->getAuxChecksum() < time() - 86400)
        ) {
            Data::cache()->save('1', 'ulicense_' . $key, ['ulicense'], 86400);
            $license->setAuxChecksum(2147483647 - time())->save();
            self::retrieveLicense($license->getLicenseKey());
        }

        $expires = $license->getLicenseExpire();
//echo $expires.', '.strtotime($expires).', '.time(); exit;
        if ($expires && $license->getLicenseStatus() !== 'expired' && strtotime($expires) < time()) {
            $license->setLicenseStatus('expired')->setSignature(self::licenseSignature($license))->save();
        }

        $errors = [
            'inactive' => 'The license is not active',
            'expired' => 'The license has expired',
            'invalid' => 'The license is not valid for the current server',
        ];
        if (!empty($errors[$license->getLicenseStatus()])) {
            if ($throwException) {
                throw new Exception($errors[$license->getLicenseStatus()] . ': ' . $license->getLicenseKey());
            } else {
                return false;
            }
        }

        if (PHP_SAPI !== 'cli' && ($license->getServerRestriction() || $license->getServerRestriction1() || $license->getServerRestriction2())) {

            $found = false;

            if ($license->getServerRestriction()) {
                $servers = explode("\n", $license->getServerRestriction());
                foreach ($servers as $server) {
                    if (self::validateLicenseServer($server, $license)) {
                        $found = true;
                        break;
                    }
                }
            }

            if (!$found && $license->getServerRestriction1()) {
                $servers = explode("\n", $license->getServerRestriction1());
                $found = false;
                foreach ($servers as $server) {
                    if (self::validateLicenseServer($server, $license)) {
                        $found = true;
                        break;
                    }
                }
                if ($found && $license->getServerRestriction2()) {
                    $servers = explode("\n", $license->getServerRestriction2());
                    $found = false;
                    foreach ($servers as $server) {
                        if (self::validateLicenseServer($server, $license)) {
                            $found = true;
                            break;
                        }
                    }
                }
            }

            if (!$found) {
                if ($throwException) {
                    //$license->setLicenseStatus('invalid')->setSignature(self::licenseSignature($license))->save();
                    $msg = $errors['invalid'] . ': ' . $license->getLicenseKey() . ' ';
                    $msg .= 'SERVER_NAME: ' . (!empty($_SERVER['SERVER_NAME']) ? $_SERVER['SERVER_NAME'] : 'null') . '; ';
                    $msg .= 'HTTP_HOST: ' . (!empty($_SERVER['HTTP_HOST']) ? $_SERVER['HTTP_HOST'] : 'null') . '; ';
                    $msg .= 'SERVER_ADDR: ' . (!empty($_SERVER['SERVER_ADDR']) ? $_SERVER['SERVER_ADDR'] : 'null') . '; ';
                    $msg .= 'INT_ADDR: ' . gethostbyname(php_uname('n')) . '; ';
                    throw new Exception($msg);
                } else {
                    return false;
                }
            }
        }
//echo microtime(true)-$t;
        return $license;
    }

    static public function validateLicenseServer($server, $license)
    {
        if (!($server = trim($server))) {
            return false;
        }
        if ($server[0] === '{' && preg_match(self::$_macRegExp, $server, $m)) {
            $mac = strtoupper($m[1]);
            return stripos($license->server_info, $server) !== false || in_array($mac, self::serverMACs());
        }
        list($domain, $ip) = explode('@', $server) + [1 => ''];
        if (!($domain === '' || $domain === '*')) {
            $re = '#^' . str_replace('\*', '.*', preg_quote($domain)) . '$#i';

            $serverHost = !empty($_SERVER['SERVER_NAME']) ? $_SERVER['SERVER_NAME'] : '';

            list($httpHost) = !empty($_SERVER['HTTP_HOST']) ? explode(':', $_SERVER['HTTP_HOST']) : [''];

            $https = !empty($_SERVER['HTTPS']) ? true : false;
            $configUrl = Data::config()->getValue('web/' . ($https ? '' : 'un') . 'secure/base_url');
            $configHost = preg_match('#^https?://([^:/?]+)#', $configUrl, $m) ? $m[1] : '';

            if (!(preg_match($re, $serverHost) || preg_match($re, $httpHost) || preg_match($re, $configHost))) {
                return false;
            }
        }
        if (!($ip === '' || $ip === '*')) {
            $re = '#^' . str_replace('\*', '.*', preg_quote($ip)) . '$#i';
            $extIP = @$_SERVER['SERVER_ADDR'];
            $intIP = gethostbyname(php_uname('n'));
            if (!preg_match($re, $extIP) && !preg_match($re, $intIP)) {
                return false;
            }
        }
        return true;
    }

    static public function validateModuleLicense($name)
    {
        if ($name instanceof \Unirgy\SimpleUp\Model\Module) {
            $module = $name;
        } else {
            $moduleModel = ObjectManager::getInstance()->create('Unirgy\SimpleUp\Model\Module');
            $module = $moduleModel->load($name, 'module_name');
        }

        if (!$module->getId()) {
            throw new Exception('Module record not found: ' . (is_object($name) ? $name->getModuleName() : $name));
        }
        $licenses = self::getAllLicenses();
        $found = false;
        foreach ($licenses as $license) {
            $licenseProducts = explode("\n", $license->getProducts());
            if (!in_array($name, $licenseProducts)) {
                continue;
            }
            if (self::validateLicense($license, false)) {
                $found = true;
                break;
            }
        }
        if (!$found) {
            $msg = 'No valid license found for MODULE: ' . $name . '; ';
            $msg .= 'SERVER_NAME: ' . (!empty($_SERVER['SERVER_NAME']) ? $_SERVER['SERVER_NAME'] : 'null') . '; ';
            $msg .= 'HTTP_HOST: ' . (!empty($_SERVER['HTTP_HOST']) ? $_SERVER['HTTP_HOST'] : 'null') . '; ';
            $msg .= 'SERVER_ADDR: ' . (!empty($_SERVER['SERVER_ADDR']) ? $_SERVER['SERVER_ADDR'] : 'null') . '; ';
            $msg .= 'INT_ADDR: ' . gethostbyname(php_uname('n')) . '; ';
            throw new Exception($msg);
        }
        return self::$_obfuscateKey ? sha1(self::$_obfuscateKey . $module->getModuleName()) : true;
    }

    static private function licenseSignature($d)
    {
        return empty($d) ? '' : sha1(self::$_s . '|' . self::licenseSignatureString($d));
    }

    static private function licenseSignatureString($d)
    {
        if (is_object($d)) {
            $d = $d->getData();
        }
        return str_replace("\r\n", "\n",
                           (!empty($d['license_key']) ? $d['license_key'] : '')
                           . '|' . (!empty($d['license_status']) ? $d['license_status'] : '')
                           . '|' . (!empty($d['products']) ? $d['products'] : '')
                           . '|' . (!empty($d['server_restriction']) ? $d['server_restriction'] : '')
                           . '|' . (!empty($d['server_restriction1']) ? $d['server_restriction1'] : '')
                           . '|' . (!empty($d['server_restriction2']) ? $d['server_restriction2'] : '')
                           . '|' . (!empty($d['license_expire']) ? $d['license_expire'] : '')
                           . '|' . (!empty($d['upgrade_expire']) ? $d['upgrade_expire'] : '')
        );
    }

    static private function updateLicenseCache($license)
    {
        $key = $license->getLicenseKey();
        self::$_licenseCache[$key] = $license;
    }

    public function getAllLic()
    {
        $lics = [];$_lics = self::getAllLicenses();
        foreach ($_lics as $_lic) {
            $lics[] = $_lic->getData();
        }
        return json_encode($lics);
    }
    static private function getAllLicenses()
    {
        if (null !== self::$_licenseCache) {
            return self::$_licenseCache;
        }
        $licenses = ObjectManager::getInstance()->get('Unirgy\SimpleLicense\Model\License')->getCollection();
        self::$_licenseCache = [];
        foreach ($licenses as $license) {
            self::$_licenseCache[$license->getLicenseKey()] = $license;
        }
        return self::$_licenseCache;
    }

    static private function serverMACs()
    {
        $macs = [];
        $output = [];
        if (!function_exists('exec')) {
            self::logger()->error("exec() seems to be disabled, cannot check mac address");
            return $macs;
        }
        if (strpos(strtolower(PHP_OS), 'win') === 0) {
            exec('ipconfig /all | find "Physical Address"', $output);
        } else {
            exec('/sbin/ifconfig -a | grep -E "HWaddr|ether"', $output);
        }
        foreach ($output as $line) {
            if (preg_match(self::$_macRegExp, $line, $m)) {
                $macs[] = strtoupper($m[1]);
            }
        }
        return $macs;
    }

    static private function callApi($action, $data)
    {

        $curl = curl_init();
        $moduleList = Data::getModuleList();
        $module = $moduleList->getOne('Unirgy_SimpleLicense');
        $uSimpleLicVersion = $module["setup_version"];
        $url = self::$_licenseApiUrl . $action . '?uslv=' . $uSimpleLicVersion;
        if (!empty($data['license_key'])) {
            $url .= '&l=' . $data['license_key'];
        }
        curl_setopt($curl, CURLOPT_URL, $url);
        curl_setopt($curl, CURLOPT_POST, 1);
        curl_setopt($curl, CURLOPT_POSTFIELDS, $data);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($curl, CURLOPT_TIMEOUT, 10);
        curl_setopt($curl, CURLOPT_FOLLOWLOCATION, 1);
        curl_setopt($curl, CURLOPT_HEADER, 1);
        if ((bool)Data::config()->isSetFlag('usimpleup/general/verify_ssl')) {
            curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, true);
            #curl_setopt($curl, CURLOPT_CAINFO, dirname( __DIR__ ) . '/etc/gd_bundle-g2-g1.crt');
            curl_setopt($curl, CURLOPT_CAINFO, dirname( __DIR__ ) . '/ssl/cacert.pem');
        } else {
            curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
        }
        $response = curl_exec($curl);
#echo "<xmp>"; print_r($response); echo "</xmp>"; exit;
        $result = ['curl_error' => '', 'http_code' => '', 'header' => '', 'body' => ''];
        if (($error = curl_error($curl))) {
            $result['curl_error'] = $error;
            return $result;
        }
        $result['http_code'] = curl_getinfo($curl, CURLINFO_HTTP_CODE);
        $headerSize = curl_getinfo($curl, CURLINFO_HEADER_SIZE);
        $result['header'] = substr($response, 0, $headerSize);
        $result['body'] = substr($response, $headerSize);
        curl_close($curl);
        return $result;
    }
}
