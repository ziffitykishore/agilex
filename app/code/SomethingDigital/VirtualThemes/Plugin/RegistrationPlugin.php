<?php

namespace SomethingDigital\VirtualThemes\Plugin;

use Magento\Theme\Model\ResourceModel\Theme\Data\CollectionFactory;
use Magento\Framework\View\Design\ThemeInterface;
use Magento\Theme\Model\Theme\Collection as FilesystemCollection;
use SomethingDigital\VirtualThemes\Exception\ChangeException;

class RegistrationPlugin
{
    private $collectionFactory;
    private $filesystemCollection;

    public function __construct(CollectionFactory $collectionFactory, FilesystemCollection $filesystemCollection)
    {
        $this->collectionFactory = $collectionFactory;
        $this->filesystemCollection = $filesystemCollection;
    }

    public function beforeCheckPhysicalThemes()
    {
        $themes = $this->collectionFactory->create()->addTypeFilter(ThemeInterface::TYPE_PHYSICAL);
        /** @var $theme ThemeInterface */
        foreach ($themes as $theme) {
            if (!$this->filesystemCollection->hasTheme($theme)) {
                throw new ChangeException('Refusing to change theme to virtual: ' . $theme->getThemeTitle());
            }
        }

        return [];
    }
}
