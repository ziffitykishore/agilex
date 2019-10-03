<?php

namespace PartySupplies\Catalog\Pricing\Render;

use Magento\Catalog\Model\Product\Pricing\Renderer\SalableResolverInterface;
use Magento\Framework\View\Element\Template\Context;
use Magento\Framework\Pricing\SaleableInterface;
use Magento\Framework\Pricing\Price\PriceInterface;
use Magento\Framework\Pricing\Render\RendererPool;
use Magento\Framework\App\ObjectManager;
use Magento\Catalog\Pricing\Price\MinimalPriceCalculatorInterface;
use Magento\Catalog\Pricing\Render\FinalPriceBox;

class FinalPrice extends FinalPriceBox
{
    /**
     * @var SalableResolverInterface
     */
    private $salableResolver;

    /**
     * @var MinimalPriceCalculatorInterface
     */
    private $minimalPriceCalculator;

    /**
     * @param Context                           $context
     * @param SaleableInterface                 $saleableItem
     * @param PriceInterface                    $price
     * @param RendererPool                      $rendererPool
     * @param array                             $data
     * @param SalableResolverInterface          $salableResolver
     * @param MinimalPriceCalculatorInterface   $minimalPriceCalculator
     */
    public function __construct(
        Context $context,
        SaleableInterface $saleableItem,
        PriceInterface $price,
        RendererPool $rendererPool,
        array $data = [],
        SalableResolverInterface $salableResolver = null,
        MinimalPriceCalculatorInterface $minimalPriceCalculator = null
    ) {
        parent::__construct($context, $saleableItem, $price, $rendererPool, $data);
        $this->salableResolver = $salableResolver ?: ObjectManager::getInstance()->get(SalableResolverInterface::class);
        $this->minimalPriceCalculator = $minimalPriceCalculator
            ?: ObjectManager::getInstance()->get(MinimalPriceCalculatorInterface::class);
    }
    
    /**
     *
     * @param string $html
     * @return string
     */
    protected function wrapResult($html)
    {
        return $html;
    }
}
