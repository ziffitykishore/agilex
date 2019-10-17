<?php
namespace SomethingDigital\CategoryAttributes\Controller\Pricing;
 
use Magento\Framework\UrlFactory;
use SomethingDigital\CustomerSpecificPricing\Model\SpotPricingApi;
use Magento\Framework\Pricing\PriceCurrencyInterface;
use Magento\Framework\Stdlib\ArrayManager;
 
class View extends \Magento\Framework\App\Action\Action
{
    protected $context;
    protected $pageFactory;
    protected $jsonEncoder;
    private $spotPricingApi;
    private $arrayManager;

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
        ArrayManager $arrayManager
    ) {
        $this->context = $context;
        $this->pageFactory = $pageFactory;
        $this->jsonEncoder = $encoder;
        $this->spotPricingApi = $spotPricingApi;
        $this->priceCurrency = $priceCurrency;
        $this->arrayManager = $arrayManager;
        parent::__construct($context);
    }
    
    public function execute() 
    {       
        $productsSkus = $this->getRequest()->getParam('products');

        if ($productsSkus) {
            $productsSkusArray = explode(',', $productsSkus);
            try {
                $data = array();

                foreach ($productsSkusArray as $sku) {
                    $prices = $this->spotPricingApi->getSpotPrice($sku);
                    if ($this->arrayManager->get('body/DiscountPrice', $prices)) {
                        $data[] = [
                            "sku" => $sku,
                            "price" => $this->arrayManager->get('body/DiscountPrice', $prices),
                            "price_formatted" => $this->priceCurrency->format($this->arrayManager->get('body/Price', $prices),false,2),
                            "QtyPrice1" => $this->arrayManager->get('body/QtyPrice1', $prices, 0),
                            "QtyPrice2" => $this->arrayManager->get('body/QtyPrice2', $prices, 0),
                            "QtyPrice3" => $this->arrayManager->get('body/QtyPrice3', $prices, 0),
                            "QtyBreak1" => $this->arrayManager->get('body/QtyBreak1', $prices, 0),
                            "QtyBreak2" => $this->arrayManager->get('body/QtyBreak2', $prices, 0),
                            "QtyBreak3" => $this->arrayManager->get('body/QtyBreak3', $prices, 0)
                        ];
                    }
                }

                $this->getResponse()->representJson($this->jsonEncoder->encode($data))->setHeader('Cache-Control', 'no-cache, public');
            } catch (\Magento\Framework\Exception\NoSuchEntityException $e) {
                $this->logger->error("SomethingDigital_CategoryAttributes - Pricing API: " . $e->getMessage());
            }
        }
    }
}