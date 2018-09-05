<?php

namespace Ziffity\Webforms\Model\ResourceModel;

use Magento\Framework\Model\ResourceModel\Db\Context;
use Magento\Framework\Model\ResourceModel\Db\AbstractDb;

class Coin extends AbstractDb
{
  

    public function __construct(
        Context $context
    ) {
        parent::__construct($context);
    }

    protected function _construct()
    {
 
        $this->_init('find_your_coin','customer_id');
    }

 
}
