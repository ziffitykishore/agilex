<?php

namespace SomethingDigital\AssetOptimizer\Dev;

use Magento\Framework\App\View\Asset\Publisher;
use Magento\Framework\Filesystem\Directory\ReadFactory as DirReadFactory;
use Magento\Framework\View\Asset\File\NotFoundException;
use Magento\Framework\View\Asset\Repository as AssetRepository;

class AssetQueue
{
    protected $dirReadFactory;
    protected $publisher;
    protected $assets = [];
    protected $moduleAssets = [];

    public function __construct(
        DirReadFactory $dirReadFactory,
        Publisher $publisher
    ) {
        $this->dirReadFactory = $dirReadFactory;
        $this->publisher = $publisher;
    }

    public function enqueue($basePath, $module = null, $path = null)
    {
        $dirRead = $this->dirReadFactory->create($basePath);
        if (!$dirRead->isExist($path)) {
            return;
        }

        $entries = $dirRead->read($path);
        foreach ($entries as $entry) {
            if ($dirRead->isDirectory($entry)) {
                $this->enqueue($basePath, $module, $entry);
            } else {
                $this->enqueueAsset($module, $entry);
            }
        }
    }

    public function enqueueAsset($module, $entry)
    {
        if ($module === null) {
            $this->assets[$entry] = $entry;
        } else {
            $this->moduleAssets[$module][$entry] = $entry;
        }
    }

    public function generate(AssetRepository $assetRepo, array $params)
    {
        $this->generateFiles($assetRepo, $params, null, $this->assets);
        foreach ($this->moduleAssets as $module => $files) {
            $this->generateFiles($assetRepo, $params, $module, $files);
        }

        $this->assets = [];
        $this->moduleAssets = [];
    }

    protected function generateFiles(AssetRepository $assetRepo, array $params, $module, array $files)
    {
        if ($module !== null) {
            $params['module'] = $module;
        }

        foreach ($files as $file) {
            try {
                $asset = $assetRepo->createAsset($file, $params);
                $this->publisher->publish($asset);
            } catch (NotFoundException $e) {
                // Ignore failed generations because the file shouldn't exist.
            }
        }
    }
}
