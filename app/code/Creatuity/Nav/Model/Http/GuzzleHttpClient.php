<?php

namespace Creatuity\Nav\Model\Http;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\BadResponseException;
use Creatuity\Nav\Exception\SoapException;

/**
 * @category Creatuity
 * @package waltwo
 * @copyright Copyright (c) 2008-2017 Creatuity Corp. (http://www.creatuity.com)
 * @license http://www.creatuity.com/license
 */
class GuzzleHttpClient implements HttpClientInterface
{
    /**
     * @var Client
     */
    protected $guzzleHttpClient;


    public function __construct()
    {
        $this->guzzleHttpClient = new Client();
    }

    /**
     * @param string|UriInterface $uri
     * @return string
     */
    public function get($uri, array $options = [])
    {
        try {
            return $this->guzzleHttpClient->get($uri, $options)->getBody()->getContents();
        } catch ( BadResponseException $e ) {
            $errResBody = $e->getResponse()->getBody()->getContents();

            throw new SoapException('GuzzleHttpClient SoapError: ' . $errResBody, 0, $e);
        } catch (\Exception $e) {
            throw new SoapException('GuzzleHttpClient exception: ' . $e->getMessage(), 0, $e);
        }
    }
}