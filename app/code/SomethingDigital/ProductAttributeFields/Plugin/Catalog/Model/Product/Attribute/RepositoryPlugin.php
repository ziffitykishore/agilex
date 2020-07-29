<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

declare(strict_types=1);

namespace SomethingDigital\ProductAttributeFields\Plugin\Catalog\Model\Product\Attribute;

class RepositoryPlugin
{
    /**
     * @var \Magento\Eav\Api\Data\AttributeExtensionFactory
     */
    private $extensionAttributesFactory;

    /**
     * @param \Magento\Eav\Api\Data\AttributeExtensionFactory $extensionAttributesFactory
     */
    public function __construct(
        \Magento\Eav\Api\Data\AttributeExtensionFactory $extensionAttributesFactory
    ) {
        $this->extensionAttributesFactory = $extensionAttributesFactory;
    }

    /**
     * Process custom extension attribute
     *
     * @param \Magento\Catalog\Model\Product\Attribute\Repository $subject
     * @param \Magento\Catalog\Api\Data\ProductAttributeInterface $attribute
     * @return void
     */
    public function beforeSave(
        \Magento\Catalog\Model\Product\Attribute\Repository $subject,
        \Magento\Catalog\Api\Data\ProductAttributeInterface $attribute
    ) {
        if ($attribute->getData('extension_attributes')) {
            $listPosition = $attribute->getData('extension_attributes')->getListPosition();
            $includeInTable = $attribute->getData('extension_attributes')->getIncludeInTable();
            $tablePosition = $attribute->getData('extension_attributes')->getTablePosition();
            $includeInFlyout = $attribute->getData('extension_attributes')->getIncludeInFlyout();
            $flyoutPosition = $attribute->getData('extension_attributes')->getFlyoutPosition();
            $searchableInLayeredNav = $attribute->getData('extension_attributes')->getSearchableInLayeredNav();
            $layeredNavDescription = $attribute->getData('extension_attributes')->getLayeredNavDescription();
            $includeInList = $attribute->getData('extension_attributes')->getIncludeInList();
            $includeInSearchTable = $attribute->getData('extension_attributes')->getIncludeInSearchTable();
            $includeInSearchList = $attribute->getData('extension_attributes')->getIncludeInSearchList();
            $searchPosition = $attribute->getData('extension_attributes')->getSearchPosition();
            $attribute->setData('list_position', $listPosition);
            $attribute->setData('include_in_table', $includeInTable);
            $attribute->setData('table_position', $tablePosition);
            $attribute->setData('include_in_flyout', $includeInFlyout);
            $attribute->setData('flyout_position', $flyoutPosition);
            $attribute->setData('searchable_in_layered_nav', $searchableInLayeredNav);
            $attribute->setData('layered_nav_description', $layeredNavDescription);
            $attribute->setData('include_in_list', $includeInList);
            $attribute->setData('include_in_search_table', $includeInSearchTable);
            $attribute->setData('include_in_search_list', $includeInSearchList);
            $attribute->setData('search_position', $searchPosition);
        }
    }

    /**
     * Set custom extension attributes
     *
     * @param \Magento\Catalog\Model\Product\Attribute\Repository $subject
     * @param \Magento\Catalog\Api\Data\ProductAttributeInterface $result
     * @return \Magento\Catalog\Api\Data\ProductAttributeInterface
     */
    public function afterGet(
        \Magento\Catalog\Model\Product\Attribute\Repository $subject,
        \Magento\Catalog\Api\Data\ProductAttributeInterface $result
    ) {
        $listPosition = $result->getData('list_position');
        $includeInTable = $result->getData('include_in_table');
        $tablePosition = $result->getData('table_position');
        $includeInFlyout = $result->getData('include_in_flyout');
        $flyoutPosition = $result->getData('flyout_position');
        $searchableInLayeredNav = $result->getData('searchable_in_layered_nav');
        $layeredNavDescription = $result->getData('layered_nav_description');
        $includeInList = $result->getData('include_in_list');
        $includeInSearchTable = $result->getData('include_in_search_table');
        $includeInSearchList = $result->getData('include_in_search_list');
        $searchPosition = $result->getData('search_position');

        $extensionAttribute = $result->getExtensionAttributes()
            ? $result->getExtensionAttributes()
            : $this->extensionAttributesFactory->create();

        $extensionAttribute->setListPosition($listPosition);
        $extensionAttribute->setIncludeInTable($includeInTable);
        $extensionAttribute->setTablePosition($tablePosition);
        $extensionAttribute->setIncludeInFlyout($includeInFlyout);
        $extensionAttribute->setFlyoutPosition($flyoutPosition);
        $extensionAttribute->setSearchableInLayeredNav($searchableInLayeredNav);
        $extensionAttribute->setLayeredNavDescription($layeredNavDescription);
        $extensionAttribute->setIncludeInList($includeInList);
        $extensionAttribute->setIncludeInSearchTable($includeInSearchTable);
        $extensionAttribute->setIncludeInSearchList($includeInSearchList);
        $extensionAttribute->setSearchPosition($searchPosition);

        $result->setExtensionAttributes($extensionAttribute);
        return $result;
    }
}
