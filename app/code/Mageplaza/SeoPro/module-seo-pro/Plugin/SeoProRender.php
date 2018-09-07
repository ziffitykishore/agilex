<?php
/**
 * Mageplaza
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Mageplaza.com license that is
 * available through the world-wide-web at this URL:
 * https://www.mageplaza.com/LICENSE.txt
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade this extension to newer
 * version in the future.
 *
 * @category    Mageplaza
 * @package     Mageplaza_SeoPro
 * @copyright   Copyright (c) Mageplaza (https://www.mageplaza.com/)
 * @license     https://www.mageplaza.com/LICENSE.txt
 */

namespace Mageplaza\SeoPro\Plugin;

use Magento\Framework\App\Request\Http;
use Magento\Framework\UrlInterface;
use Magento\Framework\View\Page\Config as PageConfig;
use Mageplaza\SeoPro\Helper\Data as HelperConfig;

/**
 * Class SeoBeforeRender
 * @package Mageplaza\Seo\Plugin
 */
class SeoProRender
{
    /**
     * @var \Magento\Framework\View\Page\Config
     */
    protected $pageConfig;

    /**
     * @var \Magento\Framework\App\Request\Http
     */
    protected $request;

    /**
     * @var \Mageplaza\SeoPro\Helper\Config
     */
    protected $helperConfig;

    /**
     * @var \Magento\Framework\UrlInterface
     */
    protected $url;

    /**
     * SeoProRender constructor.
     * @param PageConfig $pageConfig
     * @param Http $request
     * @param HelperConfig $helperConfig
     * @param UrlInterface $url
     */
    function __construct(
        PageConfig $pageConfig,
        Http $request,
        HelperConfig $helperConfig,
        UrlInterface $url
    )
    {
        $this->pageConfig   = $pageConfig;
        $this->request      = $request;
        $this->helperConfig = $helperConfig;
        $this->url          = $url;
    }

    /**
     * @param \Magento\Framework\View\Page\Config\Renderer $subject
     * @param $result
     * @return mixed
     */
    public function afterRenderMetadata(\Magento\Framework\View\Page\Config\Renderer $subject, $result)
    {
        if ($this->helperConfig->isEnableCanonicalUrl()
            && !in_array($this->request->getFullActionName(), $this->helperConfig->getDisableCanonicalPages())
            && !$this->checkRobotNoIndex()
        ) {
            $this->pageConfig->addRemotePageAsset(
                $this->url->getCurrentUrl(),
                'canonical',
                ['attributes' => ['rel' => 'canonical']]
            );
        }

        return $result;
    }

    /**
     * Check robot NOINDEX
     * @return bool
     */
    public function checkRobotNoIndex()
    {
        if ($this->helperConfig->isDisableCanonicalUrlWithNoIndexRobots()) {
            $noIndex = explode(',', $this->pageConfig->getRobots());
            if (is_array($noIndex)) {
                return trim($noIndex[0]) == 'NOINDEX' ? true : false;
            }
        }

        return false;
    }
}
