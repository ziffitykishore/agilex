<?php

namespace SomethingDigital\AssetOptimizer\Dev;

use Magento\Framework\RequireJs\ConfigFactory as RequireJsConfigFactory;
use Magento\Framework\View\Asset\Repository as AssetRepository;
use Magento\Framework\View\DesignInterface;
use Magento\RequireJs\Model\FileManager as RequireJsFileManager;
use Magento\RequireJs\Model\FileManagerFactory as RequireJsFileManagerFactory;

class RequireJsGenerator
{
    protected $requireJsConfigFactory;
    protected $requireJsFileManagerFactory;

    public function __construct(
        RequireJsConfigFactory $requireJsConfigFactory,
        RequireJsFileManagerFactory $requireJsFileManagerFactory
    ) {
        $this->requireJsConfigFactory = $requireJsConfigFactory;
        $this->requireJsFileManagerFactory = $requireJsFileManagerFactory;
    }

    public function generate(AssetRepository $assetRepo, DesignInterface $design)
    {
        /** @var RequireJsFileManager $fileManager */
        $fileManager = $this->requireJsFileManagerFactory->create(
            [
                'config' => $this->requireJsConfigFactory->create(
                    [
                        'assetRepo' => $assetRepo,
                        'design' => $design,
                    ]
                ),
                'assetRepo' => $assetRepo,
            ]
        );
        $fileManager->createRequireJsConfigAsset();
    }
}
