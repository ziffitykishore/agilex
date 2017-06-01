<?php

namespace SomethingDigital\BryantPark\Helper;

use Magento\Framework\App\Filesystem\DirectoryList;
 
class Data extends \Magento\Framework\App\Helper\AbstractHelper
{
    public function __construct(
        \Magento\Framework\App\Helper\Context $context,
        \Magento\Framework\View\Asset\Repository $assetRepository,
        DirectoryList $directoryList
    ) {
        parent::__construct($context);
        $this->_assetRepo = $assetRepository;
        $this->_directoryList = $directoryList;
    }

    public function getStaticPath($svg)
    {
        return $this->_directoryList->getPath(DirectoryList::STATIC_VIEW) . '/' . 
            $this->_assetRepo->getStaticViewFileContext()->getPath() . '/' . 
            $svg;
    }
}
