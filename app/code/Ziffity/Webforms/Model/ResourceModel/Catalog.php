<?php

namespace Ziffity\Webforms\Model\ResourceModel;

use Magento\Framework\Model\ResourceModel\Db\Context;
use Magento\Framework\Model\ResourceModel\Db\AbstractDb;

class Catalog extends AbstractDb
{
  

    public function __construct(
        Context $context
    ) {
        parent::__construct($context);
    }

    protected function _construct()
    {
 
        $this->_init('catalog_request','customer_id');
    }

 
}
