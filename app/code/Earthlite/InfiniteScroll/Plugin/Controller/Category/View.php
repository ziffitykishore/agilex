<?php

namespace Earthlite\InfiniteScroll\Plugin\Controller\Category;

/**
 * Class View
 * @package Mageplaza\LayeredNavigation\Controller\Plugin\Category
 */
class View
{
    /** @var \Magento\Framework\Json\Helper\Data */
    protected $_jsonHelper;

    /** @var \Mageplaza\LayeredNavigation\Helper\Data */
    protected $_moduleHelper;

    /**
     * View constructor.
     * @param \Magento\Framework\Json\Helper\Data $jsonHelper
     * @param \Mageplaza\AjaxLayer\Helper\Data $moduleHelper
     */
    public function __construct(
        \Magento\Framework\Json\Helper\Data $jsonHelper,
        \Mageplaza\AjaxLayer\Helper\Data $moduleHelper
    )
    {
        $this->_jsonHelper   = $jsonHelper;
        $this->_moduleHelper = $moduleHelper;
    }

    /**
     * @param \Magento\Catalog\Controller\Category\View $action
     * @param $page
     * @return mixed
     */
    public function afterExecute(\Magento\Catalog\Controller\Category\View $action, $page)
    {        
        if ($this->_moduleHelper->ajaxEnabled() && $action->getRequest()->isAjax()) {            
            
            $request_body = file_get_contents('php://input');            
            
            if($request_body && method_exists($page, 'getLayout'))
            {
                $navigation = $page->getLayout()->getBlock('catalog.leftnav');
                $products   = $page->getLayout()->getBlock('category.products');
                $result     = ['products' => $products->toHtml(), 'navigation' => $navigation->toHtml()];
                $action->getResponse()->representJson($this->_jsonHelper->jsonEncode($result));
            }
            else
            {
                return $page;
            }
        } else {
            return $page;
        }
    }
}
