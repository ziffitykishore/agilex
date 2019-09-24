<?php

namespace PartySupplies\Lazyloader\Block;

class Init extends \Magento\Framework\View\Element\Template
{

    /**
     * @var \Magento\Framework\App\Config\ScopeConfigInterface
     */
    protected $scopeConfig;
    
    /**
     * @var \Magento\Catalog\Model\Session
     */
    protected $catalogSession;
    
    /**
     * @var \Magento\Framework\Registry
     */
    protected $registry;

    /**
     * @param \Magento\Framework\View\Element\Template\Context $context
     * @param \Magento\Catalog\Model\Session $catalogSession
     * @param \Magento\Framework\Registry $registry
     * @param array $data = []
     */
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

    /**
     * @param string $fullPath
     *
     * @return string
     */
    public function getConfig($fullPath)
    {
        return $this->scopeConfig->getValue($fullPath, \Magento\Store\Model\ScopeInterface::SCOPE_STORE);
    }

    /**
     * @param string $node
     *
     * @return string
     */
    public function getScrollConfig($node)
    {
        return $this->getConfig('partysupplies_lazyloader/' . $node);
    }

    /**
     * @param string $selector
     *
     * @return string
     */
    public function getSelector($selector)
    {
        return $this->getScrollConfig('selectors/'.$selector);
    }

    /**
     * @param string $design
     *
     * @return string
     */
    public function getDesign($design)
    {
        return $this->getScrollConfig('design/'.$design);
    }

    /**
     * @return bool
     */
    public function isEnabled()
    {
        return ($this->getScrollConfig('general/enabled') && $this->isEnabledInCurrentPage());
    }

    /**
     * @return bool
     */
    public function isMemoryActive()
    {
        return $this->getScrollConfig('memory/enabled');
    }

    /**
     * @return string
     */
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

    /**
     * @return Magento\Catalog\Model\Category
     */
    public function getCurrentCategory()
    {
        return $this->registry->registry('current_category');
    }

    /**
     * @return bool
     */
    public function isEnabledInCurrentPage()
    {
        $pageType = $this->getCurrentPageType();
        return $this->getScrollConfig('instances/'.$pageType);
    }

    /**
     * @return bool|string
     */
    public function getLoaderImage()
    {
        $url = $this->getScrollConfig('design/loading_img');
        if (!empty($url)) {
            return strpos($url, 'http') === 0 ? $url : $this->getSkinUrl($url);
        }
        return false;
    }

    /**
     * @return string
     */
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
