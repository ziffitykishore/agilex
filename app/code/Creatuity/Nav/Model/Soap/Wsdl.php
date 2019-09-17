<?php

namespace Creatuity\Nav\Model\Soap;

class Wsdl
{
    protected $filename;

    public function __construct($filename)
    {
        $this->filename = $filename;
    }

    public function getFilename()
    {
        return $this->filename;
    }
}
