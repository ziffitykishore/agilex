<?php
namespace SomethingDigital\CategoryAttributes\Controller\Pricing;
 
use Magento\Framework\UrlFactory;
use SomethingDigital\CustomerSpecificPricing\Model\SpotPricingApi;
use Magento\Framework\Pricing\PriceCurrencyInterface;
use Magento\Framework\Stdlib\ArrayManager;
use Magento\Store\Model\StoreManagerInterface;
 
class View extends \Magento\Framework\App\Action\Action
{
    protected $context;
    protected $pageFactory;
    protected $jsonEncoder;
    private $spotPricingApi;
    private $arrayManager;
    protected $storeManager;

    /**
     * @param Context                    $context
     * @param EncoderInterface           $encoder
     * @param PageFactory                $pageFactory
     */
    
    public function __construct(
        \Magento\Framework\App\Action\Context $context,
        \Magento\Framework\Json\EncoderInterface $encoder,
        \Magento\Framework\View\Result\PageFactory $pageFactory,
        SpotPricingApi $spotPricingApi,
        PriceCurrencyInterface $priceCurrency,
        ArrayManager $arrayManager,
        StoreManagerInterface $storeManager
    ) {
        $this->context = $context;
        $this->pageFactory = $pageFactory;
        $this->jsonEncoder = $encoder;
        $this->spotPricingApi = $spotPricingApi;
        $this->priceCurrency = $priceCurrency;
        $this->arrayManager = $arrayManager;
        $this->storeManager = $storeManager;
        parent::__construct($context);
    }
    
    public function execute() 
    {       
        $productsSkus = $this->getRequest()->getParam('products');
        $store = $this->storeManager->getStore()->getStoreId();

        if ($productsSkus) {
            $productsSkusArray = explode(',', $productsSkus);
            try {
                $data = array();
                $productsPrices = $this->spotPricingApi->getSpotPrice($productsSkusArray);

                if ($productsPrices) {
                    foreach ($productsPrices as $productPrices) {
                        if ($this->arrayManager->get('DiscountPrice', $productPrices)) {
                            $data[] = [
                                "sku" => $this->arrayManager->get('Sku', $productPrices),
                                "price" => $this->arrayManager->get('DiscountPrice', $productPrices),
                                "price_formatted" => $this->priceCurrency->format(
                                    $this->arrayManager->get('DiscountPrice', $productPrices),
                                    false,
                                    2
                                ),
                                "QtyPrice1" => $this->arrayManager->get('QtyPrice1', $productPrices, 0),
                                "QtyPrice2" => $this->arrayManager->get('QtyPrice2', $productPrices, 0),
                                "QtyPrice3" => $this->arrayManager->get('QtyPrice3', $productPrices, 0),
                                "QtyBreak1" => $this->arrayManager->get('QtyBreak1', $productPrices, 0),
                                "QtyBreak2" => $this->arrayManager->get('QtyBreak2', $productPrices, 0),
                                "QtyBreak3" => $this->arrayManager->get('QtyBreak3', $productPrices, 0)
                            ];
                        }
                    }
                }

                $this->getResponse()->representJson($this->jsonEncoder->encode($data))->setHeader('Cache-Control', 'no-cache, public');
            } catch (\Magento\Framework\Exception\NoSuchEntityException $e) {
                $this->logger->error("SomethingDigital_CategoryAttributes - Pricing API: " . $e->getMessage());
            }
        }
    }
}