<?php
namespace Ewave\ExtendedBundleProduct\Model\ResourceModel\Product;

class Collection extends \Magento\Catalog\Model\ResourceModel\Product\Collection
{
    /**
     * @return $this
     */
    public function addFilterByRequiredOptions()
    {
        return $this;
    }
}
