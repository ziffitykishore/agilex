<?php
namespace Ziffity\Dataencryption\Model;
class Dataencryption extends \Magento\Framework\Model\AbstractModel {
    public function __construct(
            \Magento\Framework\Model\Context $context,
            \Magento\Framework\Registry $registry,
            \Magento\Framework\Model\ResourceModel\AbstractResource $resource = null,
            \Magento\Framework\Data\Collection\AbstractDb $resourceCollection = null,
            array $data = array()
    ) {
        parent::__construct($context, $registry, $resource, $resourceCollection, $data);
    }
    public function _construct() {
        $this->_init("Ziffity\Dataencryption\Model\ResourceModel\Dataencryption");
    }
}
