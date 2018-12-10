<?php
/**
 * Catalog super product configurable part block
 *
 */
namespace Ziffity\StockStatus\Block\Product\View\Type;

use \Magento\ConfigurableProduct\Model\ConfigurableAttributeData;
use \Magento\Customer\Helper\Session\CurrentCustomer;
use \Magento\Customer\Model\Session;
use \Magento\Framework\App\ObjectManager;
use \Magento\Framework\Locale\Format;
use \Magento\Framework\Pricing\PriceCurrencyInterface;
use \Magento\ConfigurableProduct\Block\Product\View\Type\Configurable as BaseConfigurable;
use Ziffity\StockStatus\Model\ConfigurableAttributeData as CustomConfigurableAttributeData;

/**
 * @api
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 * @api
 * @since 100.0.2
 */
class Configurable extends BaseConfigurable
{
    /**
     * Catalog product
     *
     * @var \Magento\Catalog\Helper\Product
     */
    protected $catalogProduct = null;

    /**
     * Current customer
     *
     * @deprecated 100.2.0, as unused property
     * @var CurrentCustomer
     */
    protected $currentCustomer;

    /**
     * Prices
     *
     * @var array
     */
    protected $_prices = [];

    /**
     * @var \Magento\Framework\Json\EncoderInterface
     */
    protected $jsonEncoder;

    /**
     * @var \Magento\ConfigurableProduct\Helper\Data $imageHelper
     */
    protected $helper;

    /**
     * @var PriceCurrencyInterface
     */
    protected $priceCurrency;

    /**
     * @var ConfigurableAttributeData
     */
    protected $configurableAttributeData;

    /**
     * @var Format
     */
    private $localeFormat;

    /**
     * @var Session
     */
    private $customerSession;

    /**
     * @var \Magento\ConfigurableProduct\Model\Product\Type\Configurable\Variations\Prices
     */
    private $variationPrices;

    /**
     * @param \Magento\Catalog\Block\Product\Context $context
     * @param \Magento\Framework\Stdlib\ArrayUtils $arrayUtils
     * @param \Magento\Framework\Json\EncoderInterface $jsonEncoder
     * @param \Magento\ConfigurableProduct\Helper\Data $helper
     * @param \Magento\Catalog\Helper\Product $catalogProduct
     * @param CurrentCustomer $currentCustomer
     * @param PriceCurrencyInterface $priceCurrency
     * @param ConfigurableAttributeData $configurableAttributeData
     * @param array $data
     * @param Format|null $localeFormat
     * @param Session|null $customerSession
     * @param \Magento\ConfigurableProduct\Model\Product\Type\Configurable\Variations\Prices|null $variationPrices
     * @SuppressWarnings(PHPMD.ExcessiveParameterList)
     */
    public function __construct(\Magento\Catalog\Block\Product\Context $context,
        \Magento\Framework\Stdlib\ArrayUtils $arrayUtils,
        \Magento\Framework\Json\EncoderInterface $jsonEncoder,
        \Magento\ConfigurableProduct\Helper\Data $helper,
        \Magento\Catalog\Helper\Product $catalogProduct,
        CurrentCustomer $currentCustomer,
        PriceCurrencyInterface $priceCurrency,
        \Magento\ConfigurableProduct\Model\ConfigurableAttributeData $configurableAttributeData,
        CustomConfigurableAttributeData $customConfigureAttributeData,
        array $data = array(),
        Format $localeFormat = null,
        Session $customerSession = null,
        \Magento\ConfigurableProduct\Model\Product\Type\Configurable\Variations\Prices $variationPrices = null
    ) {
        $this->customConfigureAttributeData = $customConfigureAttributeData;
        parent::__construct($context,$arrayUtils, $jsonEncoder, $helper, $catalogProduct, $currentCustomer, $priceCurrency, $configurableAttributeData, $data, $localeFormat, $customerSession, $variationPrices);
    }

    /**
     * Composes configuration for js
     *
     * @return string
     */
    public function getJsonConfig()
    {
        $store = $this->getCurrentStore();
        $currentProduct = $this->getProduct();

        $options = $this->helper->getOptions($currentProduct, $this->getAllowProducts());
        //Load custom configure attributes with out of stock label
        $attributesData = $this->customConfigureAttributeData->getAttributesData($currentProduct, $options);

        $config = [
            'attributes' => $attributesData['attributes'],
            'template' => str_replace('%s', '<%- data.price %>', $store->getCurrentCurrency()->getOutputFormat()),
            'currencyFormat' => $store->getCurrentCurrency()->getOutputFormat(),
            'optionPrices' => $this->getOptionPrices(),
            'priceFormat' => $this->localeFormat->getPriceFormat(),
            'prices' => $this->variationPrices->getFormattedPrices($this->getProduct()->getPriceInfo()),
            'productId' => $currentProduct->getId(),
            'chooseText' => __('Choose an Option...'),
            'images' => $this->getOptionImages(),
            'index' => isset($options['index']) ? $options['index'] : [],
        ];

        if ($currentProduct->hasPreconfiguredValues() && !empty($attributesData['defaultValues'])) {
            $config['defaultValues'] = $attributesData['defaultValues'];
        }

        $config = array_merge($config, $this->_getAdditionalConfig());

        return $this->jsonEncoder->encode($config);
    }
}
