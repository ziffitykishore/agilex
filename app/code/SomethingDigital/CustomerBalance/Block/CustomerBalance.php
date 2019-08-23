<?php

namespace SomethingDigital\CustomerBalance\Block;

use Psr\Log\LoggerInterface;
use Magento\Framework\Stdlib\ArrayManager;
use Magento\Framework\View\Element\Template\Context;
use SomethingDigital\CustomerBalance\Model\CustomerBalanceApi;


class CustomerBalance extends \Magento\Framework\View\Element\Template
{
    /**
     * @var LoggerInterface
     */
    private $logger;

    /**
     * @var ArrayManager
     */
    private $arrayManager;

    /**
     * @var CustomerBalanceApi
     */
    private $customerBalanceApi;
    

    public function __construct(
        Context $context,
        LoggerInterface $logger,
        ArrayManager $arrayManager,
        CustomerBalanceApi $customerBalanceApi,
        array $data = []
    ) {
        $this->logger = $logger;
        $this->arrayManager = $arrayManager;
        $this->customerBalanceApi = $customerBalanceApi;
        parent::__construct($context, $data);
    }

    public function getCustomerBalance()
    {
        $balance = $this->arrayManager->get('body', $this->customerBalanceApi->getCustomerBalance(), []);

        return $balance;
    }
}
