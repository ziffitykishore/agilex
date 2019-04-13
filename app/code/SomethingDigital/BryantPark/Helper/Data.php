<?php

namespace SomethingDigital\BryantPark\Helper;

use Magento\Framework\App\Filesystem\DirectoryList;
use Magento\Framework\App\Helper\AbstractHelper;
use Magento\Framework\App\Helper\Context as HelperContext;
use Magento\Framework\View\Asset\Repository as AssetRepository;
 
class Data extends AbstractHelper
{
    protected $assetRepo;
    protected $directoryList;

    public function __construct(
        HelperContext $context,
        AssetRepository $assetRepository,
        DirectoryList $directoryList
    ) {
        parent::__construct($context);
        $this->assetRepo = $assetRepository;
        $this->directoryList = $directoryList;
    }

    public function getStaticPath($svg)
    {
        return $this->directoryList->getPath(DirectoryList::STATIC_VIEW) . '/' . 
            $this->assetRepo->getStaticViewFileContext()->getPath() . '/' . 
            $svg;
    }
}
