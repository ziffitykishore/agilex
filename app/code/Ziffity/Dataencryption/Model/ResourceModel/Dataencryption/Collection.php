<?php
namespace Ziffity\Dataencryption\Model\ResourceModel\Dataencryption;

class Collection extends \Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection {
    public function _construct() {
        $this->_init("Ziffity\Dataencryption\Model\Dataencryption", "Ziffity\Dataencryption\Model\ResourceModel\Dataencryption");
    }
}
