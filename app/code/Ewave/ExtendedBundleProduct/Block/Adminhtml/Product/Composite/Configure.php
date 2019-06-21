<?php
namespace Ewave\ExtendedBundleProduct\Block\Adminhtml\Product\Composite;

use Magento\Bundle\Model\Product\Type as Bundle;
use Magento\ConfigurableProduct\Model\Product\Type\Configurable;

class Configure extends \Magento\Catalog\Block\Adminhtml\Product\Composite\Configure
{
    /**
     * @return bool
     */
    public function hasBundleConfigurableProducts()
    {
        $product = $this->getProduct();
        if ($product->getTypeId() == Bundle::TYPE_CODE) {
            /** @var Bundle $bundleProduct */
            $bundleProduct = $product->getTypeInstance();
            $selections = $bundleProduct->getSelectionsCollection(
                $bundleProduct->getOptionsIds($product),
                $product
            );

            foreach ($selections as $selection) {
                if ($selection->getTypeId() == Configurable::TYPE_CODE) {
                    return true;
                }
            }
        }

        return false;
    }

    /**
     * {@inheritdoc}
     */
    protected function _prepareLayout()
    {
        parent::_prepareLayout();
        if ($this->hasBundleConfigurableProducts()) {
            $this->removeAllBlocks();
            $this->setHasConfigurableProducts(true);
        }
        return $this;
    }

    /**
     * @return void
     */
    protected function removeAllBlocks()
    {
        foreach ($this->getLayout()->getAllBlocks() as $block) {
            /** @var \Magento\Framework\View\Element\AbstractBlock $block */
            if ($block->getNameInLayout() != $this->getNameInLayout()) {
                $block->setTemplate('');
            }
        }
    }
}
