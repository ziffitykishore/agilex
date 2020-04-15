<?php
namespace Earthlite\LayerNavigation\Plugins\Model\Layer\Filter;

class Item
{
    /**
     * @var \Magento\Framework\UrlInterface
     */
    protected $_url;

    /**
     * @var \Magento\Theme\Block\Html\Pager
     */
    protected $_htmlPagerBlock;

    /**
     * @var \Magento\Framework\App\RequestInterface
     */
    protected $_request;

    /**
     * @var \Magento\Framework\App\Config\ScopeConfigInterface
     */
    protected $scopeConfig;

    /**
     * Item constructor.
     *
     * @param \Magento\Framework\UrlInterface                    $url
     * @param \Magento\Theme\Block\Html\Pager                    $htmlPagerBlock
     * @param \Magento\Framework\App\RequestInterface            $request
     * @param \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig
     */
    public function __construct(
        \Magento\Framework\UrlInterface $url,
        \Magento\Theme\Block\Html\Pager $htmlPagerBlock,
        \Magento\Framework\App\RequestInterface $request,
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig
    ) {
        $this->_url = $url;
        $this->_htmlPagerBlock = $htmlPagerBlock;
        $this->_request = $request;
        $this->scopeConfig = $scopeConfig;
    }

    public function aroundGetUrl(\Magento\Catalog\Model\Layer\Filter\Item $item, $proceed)
    {
        if(!$this->getConfig("layernavigation/general/enablelayernavigation")) {
            return $proceed();
        }

        $enable_price_slider = $this->getConfig("layernavigation/general/enablepriceslider"); 
        
        $value = array();
        $requestVar = $item->getFilter()->getRequestVar();
        if($requestValue = $this->_request->getParam($requestVar)) {
            $value = explode(',', $requestValue);
        }
        $value[] = $item->getValue();

        if($requestVar == 'price') {
            $value = ["{price_start}-{price_end}"];
            if(!$enable_price_slider) {
                return $proceed();
            }
        }

        $query = [
        $item->getFilter()->getRequestVar() => implode(',', $value),
            // exclude current page from urls
        $this->_htmlPagerBlock->getPageVarName() => null,
        ];
        return $this->_url->getUrl('*/*/*', ['_current' => true, '_use_rewrite' => true, '_query' => $query]);
    }

    public function aroundGetRemoveUrl(\Magento\Catalog\Model\Layer\Filter\Item $item, $proceed)
    {
        if(!$this->getConfig("layernavigation/general/enablelayernavigation")) {
            return $proceed();
        }

        $value = array();
        $requestVar = $item->getFilter()->getRequestVar();
        if($requestValue = $this->_request->getParam($requestVar)) {
            $value = explode(',', $requestValue);
        }

        if(in_array($item->getValue(), $value)) {
            $value = array_diff($value, array($item->getValue()));
        }

        if($requestVar == 'price') {
            $value = [];
        }

        $query = [$requestVar => count($value) ? implode(',', $value) : $item->getFilter()->getResetValue()];
        $params['_current'] = true;
        $params['_use_rewrite'] = true;
        $params['_query'] = $query;
        $params['_escape'] = true;
        return $this->_url->getUrl('*/*/*', $params);
    }

    public function getConfig($path)
    {
        $storeScope = \Magento\Store\Model\ScopeInterface::SCOPE_STORE;

        return $this->scopeConfig->getValue($path, $storeScope);
    }
}
