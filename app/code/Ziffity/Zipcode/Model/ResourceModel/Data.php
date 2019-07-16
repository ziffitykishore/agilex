<?php

namespace Ziffity\Zipcode\Model\ResourceModel;

use Magento\Framework\Model\ResourceModel\Db\Context;
use Magento\Framework\Model\AbstractModel;
use Magento\Framework\Stdlib\DateTime\DateTime;
use Magento\Framework\Model\ResourceModel\Db\AbstractDb;

class Data extends AbstractDb
{

    protected $date;

    public function __construct(
        Context $context,
        DateTime $date
    ) {
        $this->date = $date;
        parent::__construct($context);
    }

    protected function _construct()
    {
        // @codingStandardsIgnoreEnd
        $this->_init('ziffity_zipcode_data', 'data_id');
    }

    protected function _beforeSave(AbstractModel $object)
    {
        // @codingStandardsIgnoreEnd
        $object->setUpdatedAt($this->date->gmtDate());
        if ($object->isObjectNew()) {
            $object->setCreatedAt($this->date->gmtDate());
        }
        return parent::_beforeSave($object);
    }
}
