<?php

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
namespace Ziffity\Common\Block\Catalog\Bundle;

class Option extends \Magento\Bundle\Block\Catalog\Product\View\Type\Bundle\Option\Checkbox
{
    public function __construct(
            \Magento\Framework\View\Element\Template\Context $context, 
            \Magento\Framework\Json\EncoderInterface $jsonEncoder, 
            \Magento\Catalog\Helper\Data $catalogData, 
            \Magento\Framework\Registry $registry, 
            \Magento\Framework\Stdlib\StringUtils $string, 
            \Magento\Framework\Math\Random $mathRandom, 
            \Magento\Checkout\Helper\Cart $cartHelper, 
            \Magento\Tax\Helper\Data $taxData, 
            \Magento\Framework\Pricing\Helper\Data $pricingHelper, 
            \Magento\Catalog\Api\ProductRepositoryInterface $productRepository,
            array $data = array()) {
        $this->productRepository = $productRepository;
        parent::__construct($context, $jsonEncoder, $catalogData, $registry, $string, $mathRandom, $cartHelper, $taxData, $pricingHelper, $data);
    }
    
    public function getProducturl($sku) 
    {
        $product = $this->productRepository->get($sku);
        return $product->getProductUrl();
    }

}
