<?php
namespace Ziffity\Core\Plugin;

use Magento\Framework\App\Request\Http;
use Magento\Framework\UrlInterface;
use Magento\Framework\View\Page\Config as PageConfig;
use Ziffity\Core\Helper\Data as HelperData;

class SeoProRender
{
    const PRODUCT_SUFFIX_URL = 'settings/seo/pdp_suffix_title';
    
    /**
     * @var \Magento\Framework\View\Page\Config
     */
    protected $pageConfig;

    /**
     * @var \Magento\Framework\App\Request\Http
     */
    protected $request;

    /**
     * @var \Magento\Framework\UrlInterface
     */
    protected $url;

    /**
     * @var \Ziffity\Core\Helper\Data
     */
    protected $helperData;
    

    /**
     * SeoProRender constructor.
     * @param PageConfig $pageConfig
     * @param Http $request
     * @param UrlInterface $url
     * @param HelperData $helperData
     */
    function __construct(
        PageConfig $pageConfig,
        Http $request,
        UrlInterface $url,
        HelperData $helperData
    )
    {
        $this->pageConfig   = $pageConfig;
        $this->request      = $request;
        $this->url          = $url;
        $this->helperData   = $helperData;
    }

    /**
     * @param \Magento\Framework\View\Page\Config\Renderer $subject
     * @param $result
     * @return mixed
     */
    public function afterRenderMetadata(\Magento\Framework\View\Page\Config\Renderer $subject, $result)
    {
        try{
            $cleanUrl = explode('?', $this->url->getCurrentUrl())[0];
            if(!in_array('blog', explode('/', $cleanUrl))){
                $this->AddCanonicalAndSuffixMetaTitle($cleanUrl);
            }
        } catch (Exception $ex) {
            $this->helperData->logger('seopro', $ex->getMessage(), true);
        }

        return $result;
    }

    protected function AddCanonicalAndSuffixMetaTitle($cleanUrl)
    {
        $productUrl = $this->getCurrentProductUrl();
        if (!empty($productUrl)) {
            $cleanUrl = $productUrl;
            $suffixMetaTitle = $this->helperData->getScopeConfig(self::PRODUCT_SUFFIX_URL);
            $this->pageConfig->getTitle()->set($this->pageConfig->getTitle()->get() . $suffixMetaTitle);
        }
        $this->pageConfig->addRemotePageAsset(
            $cleanUrl, 'canonical', ['attributes' => ['rel' => 'canonical']]
        );
    }

    /**
    * Retrieve a value from registry
    * 
    * @return string|null
    */
    public function getCurrentProductUrl()
    {
        $productUrl ='';
        $currentProduct = $this->helperData->getRegister()->registry('current_product');
        if ($currentProduct != null) {
            $this->helperData->getRegister()->unregister('current_category');
            $productUrl = $currentProduct->getProductUrl();
        }
        return $productUrl;
    }

}
