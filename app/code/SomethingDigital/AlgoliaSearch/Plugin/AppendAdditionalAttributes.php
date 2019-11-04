<?php

namespace SomethingDigital\AlgoliaSearch\Plugin;

use Magento\Catalog\Model\ResourceModel\Product\Attribute\CollectionFactory;

class AppendAdditionalAttributes
{
    private $collectionFactory;

    public function __construct(
        CollectionFactory $collectionFactory
    ) {
        $this->collectionFactory = $collectionFactory;
    }

    /**
     * Add searchable product attributes to product data in Algolia index
     *
     * @param \Algolia\AlgoliaSearch\Helper\Entity\ProductHelper $subject
     * @param type $result
     * @param type $storeId
     */
    public function afterGetAdditionalAttributes(\Algolia\AlgoliaSearch\Helper\Entity\ProductHelper $subject, $result, $storeId = null)
    {
        $collection = $this->collectionFactory->create();
        $collection->addFieldToFilter('is_searchable', true);
        $collection->setOrder('position','ASC');

        foreach ($collection as $item) {
            $result[] = [
                "attribute" => $item->getAttributeCode(),
                "searchable"=> 1,
                "order"=> "unordered",
                "retrievable"=> 1
            ];
        }
        return $result;
    }
}
