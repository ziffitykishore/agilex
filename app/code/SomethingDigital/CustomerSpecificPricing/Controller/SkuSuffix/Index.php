<?php

namespace SomethingDigital\CustomerSpecificPricing\Controller\SkuSuffix;

use Magento\Framework\App\Action\Action;
use Magento\Framework\App\Action\Context;
use Magento\Framework\Session\SessionManagerInterface;

class Index extends Action
{
    protected $sessionManager;

    public function __construct(
        Context $context,
        SessionManagerInterface $sessionManager
    ) {
        $this->sessionManager = $sessionManager;
        parent::__construct($context);
    }

    public function execute()
    {
        $jsonResult = $this->resultFactory->create('json');
        $suffix = $suffix = $this->sessionManager->getSkuSuffix();

        $jsonResult->setHttpResponseCode(200);
        $jsonResult->setData(
            [
                'status' => 'success',
                'code' => 200,
                'suffix' => $suffix
            ]
        );

        return $jsonResult;
    }
}
