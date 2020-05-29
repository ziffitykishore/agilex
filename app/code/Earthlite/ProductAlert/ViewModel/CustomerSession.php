<?php

namespace Earthlite\ProductAlert\ViewModel;

use Magento\Framework\View\Element\Block\ArgumentInterface;
use Magento\Customer\Model\Session;
use Magento\Framework\Registry;
use Magento\Framework\App\Http\Context;

/**
 * View Model class for stock alert block
 */
class CustomerSession implements ArgumentInterface
{
    /**
     * Customer context
     */
    const CONTEXT_CUSTOMER_ID = 'customer_id';

    /**
     * @var Context
     */
    protected $httpContext;

    /**
     * @var Registry
     */
    protected $registry;

    /**
     * @param Context $httpContext
     * @param Registry $registry
     */
    public function __construct(
        Context $httpContext,
        Registry $registry
    ) {
        $this->httpContext = $httpContext;
        $this->registry = $registry;
    }

    /**
     * Check whether the customer is logged in or not.
     * 
     * @return bool
     */
    public function isLoggedIn()
    {
        return $this->httpContext->getValue(self::CONTEXT_CUSTOMER_ID);
    }

    /**
     * Retrieve current product object
     *
     * @return \Magento\Catalog\Model\Product|boolean
     */
    public function getProduct()
    {
        $product = $this->registry->registry('current_product');
        if ($product && $product->getId()) {
            return $product;
        }
        return false;
    }
}
