<?php
declare(strict_types=1);
namespace Earthlite\ProductAlert\Block\Email;

use Magento\Framework\Pricing\PriceCurrencyInterface;
use Magento\ConfigurableProduct\Model\ResourceModel\Product\Type\ConfigurableFactory;
use Magento\ProductAlert\Block\Product\ImageProvider;
use Magento\Catalog\Model\ProductFactory;
use Magento\Catalog\Model\ResourceModel\ProductFactory as ProductResourceModelFactory;

/**
 * class ProductAlert
 */
class Stock extends \Magento\ProductAlert\Block\Email\AbstractEmail
{
    /**
     * 
     * @param \Magento\Framework\View\Element\Template\Context $context
     * @param \Magento\Framework\Filter\Input\MaliciousCode $maliciousCode
     * @param PriceCurrencyInterface $priceCurrency
     * @param \Magento\Catalog\Block\Product\ImageBuilder $imageBuilder
     * @param array $data
     * @param ImageProvider $imageProvider
     * @param ConfigurableFactory $configurableFactory
     * @param ProductFactory $productFactory
     * @param ProductResourceModelFactory $productResourceModelFactory
     */
    public function __construct(
        \Magento\Framework\View\Element\Template\Context $context,
        \Magento\Framework\Filter\Input\MaliciousCode $maliciousCode,
        PriceCurrencyInterface $priceCurrency,
        \Magento\Catalog\Block\Product\ImageBuilder $imageBuilder,
        array $data = [],
        ImageProvider $imageProvider = null,
        ConfigurableFactory $configurableFactory,
        ProductFactory $productFactory,
        ProductResourceModelFactory $productResourceModelFactory
    ) {
        $this->configurableFactory = $configurableFactory;
        $this->productFactory = $productFactory;
        $this->productResourceModelFactory = $productResourceModelFactory;
        parent::__construct(
                $context,
                $maliciousCode,
                $priceCurrency,
                $imageBuilder,
                $data,
                $imageProvider
        );
    }
    
    /**
     * @var string
     */
    protected $_template = 'Earthlite_ProductAlert::email/stock.phtml';

    /**
     * Retrieve unsubscribe url for product
     *
     * @param int $productId
     * @return string
     */
    public function getProductUnsubscribeUrl($productId)
    {
        $params = $this->_getUrlParams();
        $params['product'] = $productId;
        return $this->getUrl('productalert/unsubscribe/stock', $params);
    }

    /**
     * Retrieve unsubscribe url for all products
     *
     * @return string
     */
    public function getUnsubscribeUrl()
    {
        return $this->getUrl('productalert/unsubscribe/stockAll', $this->_getUrlParams());
    }
    
    /**
     * 
     * @param int $childProductId
     * @return int
     */
    public function getParentProduct($childProductId)
    {
        $productId = $childProductId;
        $configurableProduct = $this->configurableFactory->create();
        $parentProduct = $configurableProduct->getParentIdsByChild($productId);
        $productResourceModel = $this->productResourceModelFactory->create();
        $productModel = $this->productFactory->create();
        if(isset($parentProduct[0])){
            $productId = $parentProduct[0];
        }
        $productResourceModel->load($productModel,$productId);
        return $productModel;
    }
}
