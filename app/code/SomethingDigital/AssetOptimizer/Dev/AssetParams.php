<?php

namespace SomethingDigital\AssetOptimizer\Dev;

use Magento\Framework\App\Request\Http as Request;
use Magento\Framework\View\Asset\Repository as AssetRepository;
use Magento\Framework\View\Asset\RepositoryFactory as AssetRepositoryFactory;
use Magento\Framework\View\Design\Theme\ListInterface as ThemeList;
use Magento\Framework\View\DesignInterface;
use Magento\Framework\View\DesignInterfaceFactory;

class AssetParams
{
    protected $assetRepoFactory;
    protected $designFactory;
    protected $request;
    protected $themeList;

    public function __construct(
        AssetRepositoryFactory $assetRepoFactory,
        DesignInterfaceFactory $designFactory,
        Request $request,
        ThemeList $themeList
    ) {
        $this->assetRepoFactory = $assetRepoFactory;
        $this->designFactory = $designFactory;
        $this->request = $request;
        $this->themeList = $themeList;
    }

    public function getDesign($params)
    {
        $theme = $this->themeList->getThemeByFullPath($params['area'] . '/' . $params['theme']);
        /** @var DesignInterface $design */
        $design = $this->designFactory->create();
        $design->setDesignTheme($theme, $params['area']);

        return $design;
    }

    public function getAssetRepo($params)
    {
        // Needed to generate SSL requirejs/etc. assets.
        $this->request->getServer()->set('HTTPS', 'on');

        /** @var AssetRepository $assetRepo */
        $assetRepo = $this->assetRepoFactory->create([
            'design' => $this->getDesign($params),
            'request' => $this->request,
        ]);
        return $assetRepo;
    }
}
