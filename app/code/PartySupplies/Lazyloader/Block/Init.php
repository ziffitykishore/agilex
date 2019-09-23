<?php

namespace PartySupplies\Lazyloader\Block;

class Init extends \Magento\Framework\View\Element\Template
{

    protected $scopeConfig;    
                
    protected $catalogSession;
    
    protected $registry;

    public function __construct(
        \Magento\Framework\View\Element\Template\Context $context,
        \Magento\Catalog\Model\Session $catalogSession,
        \Magento\Framework\Registry $registry,
        array $data = []
    ) {
        $this->scopeConfig = $context->getScopeConfig();
        $this->catalogSession = $catalogSession;
        $this->registry = $registry;
        parent::__construct($context, $data);
    }


    public function getConfig($fullPath)
    {
        return $this->scopeConfig->getValue($fullPath, \Magento\Store\Model\ScopeInterface::SCOPE_STORE);
    }


    public function getScrollConfig($node)
    {
        return $this->getConfig('partysupplies_lazyloader/' . $node);
    }


    public function getSelector($selector)
    {
        return $this->getScrollConfig('selectors/'.$selector);
    }

    public function getDesign($design)
    {
        return $this->getScrollConfig('design/'.$design);
    }

    public function isEnabled()
    {
        $enabled = ($this->getScrollConfig('general/enabled') && $this->isEnabledInCurrentPage());
        return $enabled;
    }

    public function isMemoryActive()
    {
        return $this->getScrollConfig('memory/enabled');
    }

    public function getNextPageNumber()
    {
        return $this->getRequest()->getParam('p');
    }

    public function getCurrentPageType()
    {
        $where = 'grid';
        $currentCategory = $this->getCurrentCategory();
        if ($currentCategory) {
            $where = "grid";
            if ($currentCategory->getIsAnchor()) {
                $where = "layer";
            }
        }
        $controller = $this->getRequest()->getControllerName();
        if ($controller == "result") {
            $where = "search";
        } elseif ($controller == "advanced") {
            $where = "advanced";
        }
        return $where;
    }

    public function getCurrentCategory()
    {
        return $this->registry->registry('current_category');
    }

    public function isEnabledInCurrentPage()
    {
        $pageType = $this->getCurrentPageType();
        return $this->getScrollConfig('instances/'.$pageType);
    }

    public function getLoaderImage()
    {
        $url = $this->getScrollConfig('design/loading_img');
        if (!empty($url)) {
            $url = strpos($url, 'http') === 0 ? $url : $this->getSkinUrl($url);
        }
        return empty($url) ? false : $url;
    }

    public function getProductListMode()
    {
        $paramProductListMode = $this->getRequest()->getParam('product_list_mode');
        $currentMode = $paramProductListMode ? $paramProductListMode : $this->catalogSession->getDisplayMode();
        if ($currentMode) {
            switch ($currentMode) {
                case 'list':
                    $productListMode = 'list';
                    break;
                case 'grid':
                default:
                    $productListMode = 'grid';
            }
        } else {
            $defaultMode = $this->getConfig('catalog/frontend/list_mode');
            switch ($defaultMode) {
                case 'grid-list':
                    $productListMode = 'grid';
                    break;
                case 'list-grid':
                    $productListMode = 'list';
                    break;
                default:
                    $productListMode = $defaultMode;
            }
        }
        return $productListMode;
    }
}
