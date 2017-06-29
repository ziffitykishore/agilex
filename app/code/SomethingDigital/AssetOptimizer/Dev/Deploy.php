<?php

namespace SomethingDigital\AssetOptimizer\Dev;

use Magento\Framework\App\Filesystem\DirectoryList;
use Magento\Framework\Module\ModuleList;
use Magento\Framework\Module\Dir as ModuleDir;
use Magento\Framework\Module\Dir\Reader as ModuleDirReader;
use Magento\Framework\View\Design\Theme\ThemePackageList;
use SomethingDigital\AssetOptimizer\Dev\AssetParams;
use SomethingDigital\AssetOptimizer\Dev\AssetQueue;
use SomethingDigital\AssetOptimizer\Dev\RequireJsGenerator;

class Deploy
{
    protected $assetParams;
    protected $assetQueue;
    protected $dirList;
    protected $moduleDirReader;
    protected $moduleList;
    protected $requireJsGenerator;
    protected $themePackageList;

    public function __construct(
        AssetParams $assetParams,
        AssetQueue $assetQueue,
        DirectoryList $dirList,
        ModuleDirReader $moduleDirReader,
        ModuleList $moduleList,
        RequireJsGenerator $requireJsGenerator,
        ThemePackageList $themePackageList
    ) {
        $this->assetParams = $assetParams;
        $this->assetQueue = $assetQueue;
        $this->dirList = $dirList;
        $this->moduleDirReader = $moduleDirReader;
        $this->moduleList = $moduleList;
        $this->requireJsGenerator = $requireJsGenerator;
        $this->themePackageList = $themePackageList;
    }

    public function generateTheme(array $params)
    {
        if (empty($params['requirejs-only'])) {
            $this->enqueueModules($params['area']);
            $this->enqueueThemes($params['area']);
            $this->enqueueLib();
        }

        // And now generate everything we enqueued.
        $assetRepo = $this->assetParams->getAssetRepo($params);
        $design = $this->assetParams->getDesign($params);
        $this->assetQueue->generate($assetRepo, $params);
        $this->requireJsGenerator->generate($assetRepo, $design);
    }

    protected function enqueueModules($area)
    {
        foreach ($this->moduleList->getNames() as $module) {
            $path = $this->moduleDirReader->getModuleDir(ModuleDir::MODULE_VIEW_DIR, $module);
            $this->assetQueue->enqueue($path . '/base/web/', $module);
            $this->assetQueue->enqueue($path . '/' . $area . '/web/', $module);
        }
    }

    protected function enqueueThemes($area)
    {
        $themePackages = $this->themePackageList->getThemes();
        foreach ($themePackages as $themePackage) {
            if ($themePackage->getArea() == $area) {
                $path = $themePackage->getPath();
                $this->assetQueue->enqueue($path . '/web/');
            }
        }
    }

    protected function enqueueLib()
    {
        $path = $this->dirList->getPath(DirectoryList::LIB_WEB);
        $this->assetQueue->enqueue($path);
    }
}
