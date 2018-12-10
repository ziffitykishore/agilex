<?php

namespace Ziffity\Blockcustomers\Model\ResourceModel\Data;

use Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection;

class Collection extends AbstractCollection
{
    /**
     * @var string
     * @codingStandardsIgnoreStart
     */
    protected $_idFieldName = 'id';

    /**
     * Collection initialisation
     */
    protected function _construct()
    {
        // @codingStandardsIgnoreEnd
        $this->_init('Ziffity\Blockcustomers\Model\Data', 'Ziffity\Blockcustomers\Model\ResourceModel\Data');
    }
}
