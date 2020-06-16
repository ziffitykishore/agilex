<?php

namespace Earthlite\Blog\ViewModel;

use Magento\Framework\View\Element\Block\ArgumentInterface;
use Mirasvit\Blog\Model\Config;

class Blog implements ArgumentInterface
{
    /**
     * @var Config
     */
    protected $blogConfig;

    /**
     * @param Config $blogConfig
     */
    public function __construct(
        Config $blogConfig
    ) {
        $this->blogConfig = $blogConfig;
    }

    /**
     * Get blog post back url
     * @return string
     */
    public function getBlogBackUrl()
    {
        return $this->blogConfig->getBaseUrl();
    }
}
