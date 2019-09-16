<?php

namespace Creatuity\Nav\Model\Soap;

use Magento\Framework\App\Filesystem\DirectoryList;
use Magento\Framework\Filesystem\Io\File;
use Creatuity\Nav\Exception\FailedToWriteFileException;
use Creatuity\Nav\Model\Service\Object\ServiceObject;
use Psr\Log\LoggerInterface;

class WsdlFactory
{
    protected $baseFilePathCode;
    protected $baseFilePath;
    protected $directoryList;
    protected $file;

    public function __construct(
        $baseFilePathCode,
        DirectoryList $directoryList,
        File $file
    ) {
        $this->baseFilePathCode = $baseFilePathCode;
        $this->directoryList = $directoryList;
        $this->file = $file;
    }

    public function create(ServiceObject $serviceObject, $content)
    {
        $baseFilePath = $this->getBaseFilePath();

        $this->file->checkAndCreateFolder($baseFilePath);

        $filename = "{$baseFilePath}/{$serviceObject->getName()}.xml";

        if (!$this->file->write($filename, $content)) {
            throw new FailedToWriteFileException("Failed to write WSDL file '{$filename}'");
        }

        return new Wsdl($filename);
    }

    protected function getBaseFilePath()
    {
        if (!isset($this->baseFilePath)) {
            $this->baseFilePath = $this->directoryList->getPath($this->baseFilePathCode).'/wsdl';
        }

        return $this->baseFilePath;
    }
}
