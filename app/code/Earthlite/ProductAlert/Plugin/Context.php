<?php

namespace Earthlite\ProductAlert\Plugin;

use Magento\Customer\Model\Session;
use Magento\Framework\App\Http\Context as HttpContext;
use Magento\Framework\App\ActionInterface;
use Magento\Framework\App\RequestInterface;

/**
 * This class will save the customer id in HTTP Context
 */
class Context
{
    /**
     * Customer context
     */
    const CONTEXT_CUSTOMER_ID = 'customer_id';

    /**
     * @var Session
     */
    protected $customerSession;

    /**
     * @var HttpContext
     */
    protected $httpContext;

    /**
     * @param Session $customerSession
     * @param HttpContext $httpContext
     */
    public function __construct(
        Session $customerSession,
        HttpContext $httpContext
    ) {
        $this->customerSession = $customerSession;
        $this->httpContext = $httpContext;
    }

    /**
     * @param ActionInterface $subject
     * @param callable $proceed
     * @param RequestInterface $request
     * @return mixed
     */
    public function aroundDispatch(
        ActionInterface $subject,
        \Closure $proceed,
        RequestInterface $request
    ) {
        $customerId = ($this->customerSession->getCustomerId()) ? $this->customerSession->getCustomerId() : 0;
        $this->httpContext->setValue(
            self::CONTEXT_CUSTOMER_ID,
            $customerId,
            false
        );
        return $proceed($request);
    }
}
