<?php

namespace Travers\Seo\Plugin;

use Magento\Framework\App\Request\Http;
use Magento\Framework\UrlInterface;
use Magento\Framework\View\Page\Config as PageConfig;
use Magento\Framework\View\Page\Config\Renderer;
use Psr\Log\LoggerInterface;

class CanonicalTag
{
    /**
     * @var PageConfig
     */
    protected $pageConfig;

    /**
     * @var Http
     */
    protected $request;

    /**
     * @var UrlInterface
     */
    protected $url;

    /**
     * @var LoggerInterface
     */
    protected $logger;

    /**
     * @param PageConfig $pageConfig
     * @param Http $request
     * @param UrlInterface $url
     * @param LoggerInterface $logger
     */
    public function __construct(
        PageConfig $pageConfig,
        Http $request,
        UrlInterface $url,
        LoggerInterface $logger
    ) {
        $this->pageConfig = $pageConfig;
        $this->request = $request;
        $this->url = $url;
        $this->logger = $logger;
    }

    /**
     * Add canonical tags to cms and other pages.
     * 
     * @param Renderer $subject
     * @param $result
     * @return mixed
     */
    public function afterRenderMetadata(
        Renderer $subject,
        $result
    ) {
        try {
            $queryParam = explode('?', $this->url->getCurrentUrl())[0];

            if ($this->request->getFullActionName() != 'catalog_product_view'
                && $this->request->getFullActionName() != 'catalog_category_view'
            ) {
                $this->pageConfig->addRemotePageAsset(
                    $queryParam,
                    'canonical',
                    ['attributes' => ['rel' => 'canonical']]
                );
            }
        } catch (\Exception $e) {
            $this->logger->critical($e->getMessage());
        }

        return $result;
    }
}
