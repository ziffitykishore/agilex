<?php

namespace SomethingDigital\AssetOptimizer\Dev;

use Magento\Framework\RequireJs\ConfigFactory as RequireJsConfigFactory;
use Magento\Framework\View\Asset\Repository as AssetRepository;
use Magento\Framework\View\DesignInterface;
use Magento\RequireJs\Model\FileManager as RequireJsFileManager;
use Magento\RequireJs\Model\FileManagerFactory as RequireJsFileManagerFactory;
use Magento\Framework\Locale\ResolverInterface as LocaleResolver;

class RequireJsGenerator
{
    protected $requireJsConfigFactory;
    protected $requireJsFileManagerFactory;
    protected $localeResolver;

    public function __construct(
        RequireJsConfigFactory $requireJsConfigFactory,
        RequireJsFileManagerFactory $requireJsFileManagerFactory,
        LocaleResolver $localeResolver
    ) {
        $this->requireJsConfigFactory = $requireJsConfigFactory;
        $this->requireJsFileManagerFactory = $requireJsFileManagerFactory;
        $this->localeResolver = $localeResolver;
    }

    public function generate(AssetRepository $assetRepo, DesignInterface $design, $locale)
    {
        $this->localeResolver->setLocale($locale);

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
