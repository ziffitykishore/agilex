<?php
namespace Ziffity\Dataencryption\Model\ResourceModel;

class Dataencryption extends \Magento\Framework\Model\ResourceModel\Db\AbstractDb {
   
    protected function _construct() {
        $this->_init("ziffity_dataencryption", "id");
    }

}
