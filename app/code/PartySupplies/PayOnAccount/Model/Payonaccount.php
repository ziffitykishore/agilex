<?php

namespace PartySupplies\PayOnAccount\Model;

use Creatuity\Nav\Model\Provider\Nav\CustomerApproval;

/**
 *
 * @SuppressWarnings(PHPMD.CamelCasePropertyName)
 */
class Payonaccount extends \Magento\Payment\Model\Method\AbstractMethod
{

    /**
     * @const payment code
     */
    const PAYMENT_METHOD_PAY_ON_ACCOUNT = 'payonaccount';
    
    /**
     * @var string
     */
    protected $_code = self::PAYMENT_METHOD_PAY_ON_ACCOUNT;
    
    /**
     * @var string
     */
    protected $_formBlockType = \PartySupplies\PayOnAccount\Block\Form\Payonaccount::class;

    /**
     * @var string
     */
    protected $_infoBlockType = \PartySupplies\PayOnAccount\Block\Info\Payonaccount::class;
    
    /**
     * @var boolean
     */
    protected $_isOffline = true;
    
    /**
     * @var CustomerApproval
     */
    protected $customer;

    /**
     *
     * @param \Magento\Framework\Model\Context $context
     * @param \Magento\Framework\Registry $registry
     * @param \Magento\Framework\Api\ExtensionAttributesFactory $extensionFactory
     * @param \Magento\Framework\Api\AttributeValueFactory $customAttributeFactory
     * @param \Magento\Payment\Helper\Data $paymentData
     * @param \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig
     * @param \Magento\Payment\Model\Method\Logger $logger
     * @param \Magento\Framework\Model\ResourceModel\AbstractResource $resource
     * @param \Magento\Framework\Data\Collection\AbstractDb $resourceCollection
     * @param array $data
     * @param \Magento\Directory\Helper\Data $directory
     * @param CustomerApproval $customer
     */
    public function __construct(
        \Magento\Framework\Model\Context $context,
        \Magento\Framework\Registry $registry,
        \Magento\Framework\Api\ExtensionAttributesFactory $extensionFactory,
        \Magento\Framework\Api\AttributeValueFactory $customAttributeFactory,
        \Magento\Payment\Helper\Data $paymentData,
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig,
        \Magento\Payment\Model\Method\Logger $logger,
        \Magento\Framework\Model\ResourceModel\AbstractResource $resource = null,
        \Magento\Framework\Data\Collection\AbstractDb $resourceCollection = null,
        array $data = [],
        \Magento\Directory\Helper\Data $directory = null,
        CustomerApproval $customer
    ) {
        parent::__construct(
            $context,
            $registry,
            $extensionFactory,
            $customAttributeFactory,
            $paymentData,
            $scopeConfig,
            $logger,
            $resource,
            $resourceCollection,
            $data,
            $directory
        );
        $this->customer = $customer;
    }

    /**
     * To check payment method is available or not
     *
     * @param \Magento\Quote\Api\Data\CartInterface $quote
     * @return boolean
     */
    public function isAvailable(\Magento\Quote\Api\Data\CartInterface $quote = null)
    {
        $result = parent::isAvailable($quote);
        if ($result
            && $quote->getCustomer()->getCustomAttribute('pay_on_account_approval') !== null
            && $quote->getCustomer()->getCustomAttribute('nav_customer_id') !== null
        ) {
            $result = $this->checkCreditLimit(
                $quote->getCustomer()->getCustomAttribute('pay_on_account_approval')->getValue(),
                $quote->getCustomer()->getCustomAttribute('nav_customer_id')->getValue(),
                $quote->getGrandTotal()
            );
        }
        return $result;
    }
    
    /**
     * To check credit limit for customer from Navision
     *
     * @param boolean $isEligible
     * @param string $navId
     * @param float $grandTotal
     * @return boolean
     */
    protected function checkCreditLimit($isEligible, $navId, $grandTotal)
    {
        $isAllowed = false;

        if ($isEligible) {
            $customerData['No'] = $navId;
            $result = $this->customer->getExistingCustomer($customerData);
            $result = array_shift($result);
            if (isset($result['Credit_Limit_LCY'])
                && $grandTotal <= $result['Credit_Limit_LCY']
            ) {
                $isAllowed = true;
            }
        }

        return $isAllowed;
    }
}
