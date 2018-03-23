<?php

namespace SomethingDigital\VirtualThemes\Model;

use Magento\Framework\View\Design\ThemeInterface;
use Magento\Theme\Model\ResourceModel\Theme\Data\CollectionFactory;
use SomethingDigital\VirtualThemes\Exception\NotFoundException;

class Processor
{
    private $collectionFactory;

    public function __construct(CollectionFactory $collectionFactory)
    {
        $this->collectionFactory = $collectionFactory;
    }

    public function makeVirtual($area, $theme)
    {
        $theme = $this->getTheme($area, $theme);
        $theme->setType(ThemeInterface::TYPE_VIRTUAL)->save();
    }

    public function makePhysical($area, $theme)
    {
        $theme = $this->getTheme($area, $theme);
        $theme->setType(ThemeInterface::TYPE_PHYSICAL)->save();
    }

    public function makeAllPhysical($area)
    {
        $themes = $this->collectionFactory->create();
        $themes->addTypeFilter(ThemeInterface::TYPE_VIRTUAL);
        $themes->addAreaFilter($area);

        /** @var $theme ThemeInterface */
        foreach ($themes as $theme) {
            $theme->setType(ThemeInterface::TYPE_PHYSICAL)->save();
        }
    }

    protected function getTheme($area, $theme)
    {
        $fullPath = $area . '/' . $theme;
        $theme = $this->collectionFactory->create()->getThemeByFullPath($fullPath);
        if (!$theme->getId()) {
            throw new NotFoundException('Could not find theme: ' . $path);
        }
        return $theme;
    }
}
