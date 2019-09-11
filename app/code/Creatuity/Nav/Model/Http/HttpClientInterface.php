<?php

namespace Creatuity\Nav\Model\Http;


/**
 * @category Creatuity
 * @package waltwo
 * @copyright Copyright (c) 2008-2017 Creatuity Corp. (http://www.creatuity.com)
 * @license http://www.creatuity.com/license
 */
interface HttpClientInterface
{
    /**
     * @param string|UriInterface $uri
     * @return string
     */
    public function get($uri, array $options = []);
}