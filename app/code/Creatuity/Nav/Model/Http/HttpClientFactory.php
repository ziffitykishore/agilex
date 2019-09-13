<?php

namespace Creatuity\Nav\Model\Http;


class HttpClientFactory
{
    /**
     * @return HttpClientInterface
     */
    public function create()
    {
        return new GuzzleHttpClient();
    }
}
