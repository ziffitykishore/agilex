<?php


namespace Wyomind\PointOfSale\Model\ResourceModel\AttributesValues;

class Collection extends \Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection
{

    /**
     * Define resource model
     * @return void
     */
    protected function _construct()
    {
        $this->_init('Wyomind\PointOfSale\Model\AttributesValues', 'Wyomind\PointOfSale\Model\ResourceModel\AttributesValues');
    }

    public function getByPointOfSaleId($posId)
    {
        /*
         select * from pointofsale_attributes_values as pav
         left join pointofsale_attributes pa on pa.attribute_id = pav.attribute_id
         where pointofsale_id = 541
         */
        $this->addFieldToFilter("pointofsale_id", ["eq" => $posId]);
        $pointofsaleAttributes = $this->getTable("pointofsale_attributes");
        $this->join($pointofsaleAttributes, $pointofsaleAttributes . ".attribute_id = main_table.attribute_id", ["code"]);
        return $this;
    }

}
