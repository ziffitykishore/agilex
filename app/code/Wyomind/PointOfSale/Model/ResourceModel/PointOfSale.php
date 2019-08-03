<?php

namespace Wyomind\PointOfSale\Model\ResourceModel;

/**
 * Point of sale mysql resource
 */
class PointOfSale extends \Magento\Framework\Model\ResourceModel\Db\AbstractDb
{

    /**
     * @var Attributes\Collection
     */
    protected $attributesCollection;
    /**
     * @var AttributesValues\Collection
     */
    protected $attributesValuesCollection;

    /**
     * PointOfSale constructor.
     * @param \Magento\Framework\Model\ResourceModel\Db\Context $context
     * @param Attributes\Collection $attributesCollection
     * @param AttributesValues\Collection $attributesValuesCollection
     * @param string|null $connectionName
     */
    public function __construct(
        \Magento\Framework\Model\ResourceModel\Db\Context $context,
        Attributes\Collection $attributesCollection,
        AttributesValues\Collection $attributesValuesCollection,
        string $connectionName = null)
    {
        $this->attributesCollection = $attributesCollection;
        $this->attributesValuesCollection = $attributesValuesCollection;
        parent::__construct($context, $connectionName);
    }

    protected function _construct()
    {
        $this->_init('pointofsale', 'place_id');
    }

    protected function _afterLoad(\Magento\Framework\Model\AbstractModel $pointOfSale)
    {
        $attributesValues = $this->attributesValuesCollection->getByPointOfSaleId($pointOfSale->getId());

        foreach ($attributesValues as $attributeValue) {
            $pointOfSale->setData($attributeValue->getCode(),$attributeValue->getValue());
        }

        return parent::_afterLoad($pointOfSale);
    }

    protected function _afterSave(\Magento\Framework\Model\AbstractModel $pointOfSale)
    {


        if ($pointOfSale->getData('use_assignation_rules') !== 0) {
            $toInsertFields = [
                "attribute_id",
                "pointofsale_id",
                "value"
            ];
            $toInsertData = [];
            foreach ($this->attributesCollection as $attribute) {
                $code = $attribute->getCode();
                $value = $pointOfSale->getData($code);
                $attributeId = $attribute->getId();
                $toInsertData[] = [
                    "attribute_id" => $attributeId,
                    "pointofsale_id" => $pointOfSale->getId(),
                    "value" => $value
                ];
            }

            $this->_resources->getConnection()->insertOnDuplicate($this->_resources->getTableName("pointofsale_attributes_values"), $toInsertData, $toInsertFields);
        }

        return parent::_afterSave($pointOfSale);
    }
}
