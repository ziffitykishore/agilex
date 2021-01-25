<?php
namespace Travers\Order\Block\Onepage;

class Success extends \Magento\Checkout\Block\Onepage\Success
{
    protected $checkoutSession;
    protected $customerSession;
    protected $orderData;
    protected $orderConfig;
    protected $orderFactory;
    protected $product;
    protected $currencyInterface;
    protected $countryFactory;

    public function __construct(
        \Magento\Checkout\Model\Session $checkoutSession,
        \Magento\Customer\Model\Session $customerSession,
        \Magento\Sales\Model\Order\Config $orderConfig,
        \Magento\Framework\App\Http\Context $httpContext,
        \Magento\Sales\Model\OrderFactory $orderFactory,
        \Magento\Catalog\Model\Product $product,
        \Magento\Framework\Locale\CurrencyInterface $currencyInterface,
        \Magento\Directory\Model\CountryFactory $countryFactory,
        \Magento\Framework\View\Element\Template\Context $context,
        \Magento\Framework\Registry $registry,
        array $data = []
    ) {
        parent::__construct(
            $context,
            $checkoutSession,
            $orderConfig,
            $httpContext,
            $data
        );
        $this->checkoutSession = $checkoutSession;
        $this->customerSession = $customerSession;
        $this->orderData = $orderFactory->create();
        $this->product = $product;
        $this->currencyInterface = $currencyInterface;
        $this->countryFactory = $countryFactory;
    }
    
    public function getCurrencyDetails()
    {
        return $this->currencyInterface;
    }
    
    public function storeManagerObject()
    {
        return $this->_storeManager;
    }
    
    public function getCountryname($countryCode)
    {
        $country = $this->countryFactory->create()->loadByCode($countryCode);
        return $country->getName();
    }
    
    public function getOrderDetails()
    {
        /**
         * @var \Magento\Sales\Model\Order $this->orderData
         * @var \Magento\Store\Model\Store $store         
         */        
        $orderDetails = [];
        $orderId = $this->getOrderId();
        $order = $this->orderData->loadByIncrementId($orderId);
        $orderDetails['order'] = $order;
        $orderDetails['shipping'] = $order->getShippingAddress();
        $orderDetails['billing'] = $order->getBillingAddress();
        $orderDetails['orderItems'] = $order->getAllVisibleItems();
        $orderDetails['customerNote'] = $order->getCheckoutOrdernotes();
        $store = $this->storeManagerObject()->getStore();
        $currency = $this->getCurrencyDetails();
        $currencyCode = $store->getBaseCurrencyCode();
        $orderDetails['symbol'] = $currency->getCurrency($currencyCode)->getSymbol();
        return $orderDetails;
    }
}