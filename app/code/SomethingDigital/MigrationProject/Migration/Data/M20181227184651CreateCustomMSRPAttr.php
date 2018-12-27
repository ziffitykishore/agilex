<?php

namespace SomethingDigital\MigrationProject\Migration\Data;

use SomethingDigital\Migration\Api\MigrationInterface;
use Magento\Framework\Setup\SetupInterface;
use Magento\Catalog\Model\ResourceModel\Product\Action;
use Magento\Eav\Setup\EavSetup;
use Magento\Catalog\Model\ResourceModel\Product\Collection;
use Magento\Eav\Model\Entity\Attribute\SetFactory;
use Magento\Eav\Model\Entity\Attribute\GroupFactory;
use Magento\Eav\Model\AttributeManagement;
use Psr\Log\LoggerInterface;

class M20181227184651CreateCustomMSRPAttr implements MigrationInterface
{
    const PRODUCT_TYPE_ID = 4;

    const ATT_SORT_ORDER = 26;

    private $eavSetup;
    private $productAction;
    private $collection;
    private $attributeSetFactory;
    private $attributeGroupFactory;
    private $attributeManagement;

    public function __construct(
        EavSetup $eavSetup,
        Action $productAction,
        Collection $collection,
        SetFactory $attributeSetFactory,
        GroupFactory $attributeGroupFactory,
        AttributeManagement $attributeManagement
    ) {
        $this->eavSetup = $eavSetup;
        $this->productAction = $productAction;
        $this->collection = $collection;
        $this->attributeSetFactory = $attributeSetFactory;
        $this->attributeGroupFactory = $attributeGroupFactory;
        $this->attributeManagement = $attributeManagement;
    }
    public function execute(SetupInterface $setup)
    {
        $attributeData = [
            'manufacturer_price'=> [
                'type' => 'decimal',
                'backend' => '',
                'frontend' => '',
                'frontend_class' => 'validate-number',
                'label' => 'Manufacturer Price',
                'input' => 'text',
                'class' => '',
                'source' => '',
                'global' => \Magento\Eav\Model\Entity\Attribute\ScopedAttributeInterface::SCOPE_GLOBAL,
                'visible' => true,
                'required' => false,
                'user_defined' => true,
                'default' => '',
                'searchable' => false,
                'filterable' => false,
                'comparable' => false,
                'visible_on_front' => true,
                'used_in_product_listing' => true,
                'unique' => false,
                'apply_to' => ''
            ]
        ];
        foreach ($attributeData as $attributeCode => $data) {
            $this->createAttribute($attributeCode, $data);
        }

        $this->eavSetup->updateAttribute(\Magento\Catalog\Model\Product::ENTITY, 'manufacturer_price', 'used_in_product_listing', 1);
    }

    private function createAttribute($attributeCode, $data)
    {
        if ($this->eavSetup->getAttributeId(\Magento\Catalog\Model\Product::ENTITY, $attributeCode)) {
            return;
        }

        // create attribute
        $this->eavSetup->addAttribute(
            \Magento\Catalog\Model\Product::ENTITY,
            $attributeCode,
            $data
        );
        // Assign attribute to all product attribute sets
        $setCollection = $this->attributeSetFactory->create()->getCollection();
        $setCollection->addFieldToFilter('entity_type_id', self::PRODUCT_TYPE_ID);
        /** @var Set $attributeSet */
        foreach ($setCollection as $attributeSet) {
            /** @var Group $group */
            $group = $this->attributeGroupFactory->create()->getCollection()
                ->addFieldToFilter('attribute_group_code', ['eq' => 'product-details'])
                ->addFieldToFilter('attribute_set_id', ['eq' => $attributeSet->getId()])
                ->getFirstItem();
            $groupId = $group->getId() ? $group->getId() : $attributeSet->getDefaultGroupId();
            try {
                $this->attributeManagement->assign(
                    'catalog_product',
                    $attributeSet->getId(),
                    $groupId,
                    $attributeCode,
                    self::ATT_SORT_ORDER
                );
            } catch (\Exception $e) {
                echo $e->getMessage();
                return;
            }
        }
    }
}
