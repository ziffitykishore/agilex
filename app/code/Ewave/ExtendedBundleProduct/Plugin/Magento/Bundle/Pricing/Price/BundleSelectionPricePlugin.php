<?php
namespace Ewave\ExtendedBundleProduct\Plugin\Magento\Bundle\Pricing\Price;

use Ewave\ExtendedBundleProduct\Api\SelectionLowestPriceInterface;
use Magento\Bundle\Pricing\Price\BundleSelectionPrice as Subject;
use Magento\Bundle\Model\Product\Price;
use Magento\ConfigurableProduct\Model\Product\Type\Configurable;

class BundleSelectionPricePlugin
{
    /**
     * @var SelectionLowestPriceInterface
     */
    protected $selectionLowestPrice;

    /**
     * BundleSelectionPricePlugin constructor.
     * @param SelectionLowestPriceInterface $selectionLowestPrice
     */
    public function __construct(
        SelectionLowestPriceInterface $selectionLowestPrice
    ) {
        $this->selectionLowestPrice = $selectionLowestPrice;
    }

    /**
     * @param Subject $subject
     * @param bool|float $result
     * @return bool|float
     */
    public function afterGetValue(Subject $subject, $result)
    {
        $bundle = $this->getBundle($subject);
        $selection = $subject->getProduct();

        if ($bundle->getPriceType() == Price::PRICE_TYPE_DYNAMIC
            && $selection->getTypeId() == Configurable::TYPE_CODE
        ) {
            $bundleSelectionKey = 'bundle-selection-config-value-' . $selection->getSelectionId();
            if ($selection->hasData($bundleSelectionKey)) {
                return $selection->getData($bundleSelectionKey);
            }

            $lowestPrice = $this->selectionLowestPrice->getSelectionLowestPrice($selection);
            if (false !== $lowestPrice) {
                $result = $lowestPrice;
                $selection->setData($bundleSelectionKey, $result);
            }
        }

        return $result;
    }

    /**
     * @param Subject $subject
     * @return \Magento\Catalog\Model\Product
     */
    protected function getBundle(Subject $subject)
    {
        $getBundle = function () {
            return $this->bundleProduct;
        };
        return \Closure::bind($getBundle, $subject, Subject::class)();
    }
}
