<?php

namespace Creatuity\MagNav\Model\Connector;

/**
 * We need to hack SoapClient due to PHPBug
 * https://bugs.php.net/bug.php?id=27777
 *
 */
class SoapClientException extends \Exception
{
}

class SoapClient extends \SoapClient
{

    /**
     * @var string
     */
    protected $username = '';

    /**
     * @var string
     */
    protected $password = '';

    public function __construct($username, $password, $wsdl, array $options = null)
    {
        $this->username = $username;
        $this->password = $password;

        $wsdlLocalPath = $this->downloadWsdlToTmpFile($wsdl);

        parent::__construct($wsdlLocalPath, $options);
    }

    protected function downloadWsdlToTmpFile($url)
    {
        $path = tempnam(sys_get_temp_dir(), 'WALTWO_MAGNAV');

        if (empty(trim($path))) {
            throw new SoapClientException("Wsdl path cannot be empty!");
        }

        $wsdlContent = $this->callCurl($url);
        if (empty(trim($wsdlContent))) {
            throw new SoapClientException("Wsdl file cannot be empty!");
        }
        \file_put_contents($path, $wsdlContent);
        return $path;
    }

    public function __doRequest($request, $location, $action, $version, $one_way = 0)
    {
        return $this->callCurl($location, $request, [
            'SOAPAction: "' . $action . '"',
        ]);
    }

    public function callCurl($location, $request = '', $headers = [])
    {
        $headers += [
            'Method: POST',
            'Connection: Keep-Alive',
            'User-Agent: PHP-SOAP-CURL',
            'Content-Type: text/xml; charset=utf-8',
        ];

        $ch = curl_init($location);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $request);
        curl_setopt($ch, CURLOPT_HTTP_VERSION, CURL_HTTP_VERSION_1_1);
        curl_setopt($ch, CURLOPT_HTTPAUTH, CURLAUTH_NTLM);
        curl_setopt($ch, CURLOPT_USERPWD, $this->username . ":" . $this->password);
        $response = curl_exec($ch);

        return $response;
    }

}