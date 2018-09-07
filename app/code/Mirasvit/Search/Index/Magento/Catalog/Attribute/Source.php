<?php
/**
 * Mirasvit
 *
 * This source file is subject to the Mirasvit Software License, which is available at https://mirasvit.com/license/.
 * Do not edit or add to this file if you wish to upgrade the to newer versions in the future.
 * If you wish to customize this module for your needs.
 * Please refer to http://www.magentocommerce.com for more information.
 *
 * @category  Mirasvit
 * @package   mirasvit/module-search
 * @version   1.0.78
 * @copyright Copyright (C) 2018 Mirasvit (https://mirasvit.com/)
 */



namespace Mirasvit\Search\Index\Magento\Catalog\Attribute;

use Magento\Catalog\Model\ResourceModel\Product\Attribute\CollectionFactory as AttributeCollectionFactory;
use Magento\Framework\Option\ArrayInterface;

class Source implements ArrayInterface
{
    /**
     * @var AttributeCollectionFactory
     */
    private $attributeCollectionFactory;

    public function __construct(
        AttributeCollectionFactory $attributeCollectionFactory
    ) {
        $this->attributeCollectionFactory = $attributeCollectionFactory;
    }

    /**
     * @return array
     */
    public function toOptionArray()
    {
        $collection = $this->attributeCollectionFactory->create()
            ->addVisibleFilter()
            ->addDisplayInAdvancedSearchFilter()
            ->setOrder('attribute_id', 'asc');

        $options = [];
        foreach ($collection as $attribute) {
            $options[] = [
                'value' => $attribute->getAttributeCode(),
                'label' => $attribute->getDefaultFrontendLabel() . ' [' . $attribute->getAttributeCode() . ']',
            ];
        }

        return $options;
    }
}
