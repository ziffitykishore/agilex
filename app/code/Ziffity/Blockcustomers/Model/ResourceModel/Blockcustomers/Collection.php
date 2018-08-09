<?php
namespace Ziffity\Blockcustomers\Model\ResourceModel\Blockcustomers;
/**
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * This is the Summary for this element.
 * 
 * @inheritDoc
 */
class Collection extends \Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection
{
     /**
     * Initialize resource collection
     *
     * @return void
     */
    public function _construct()
    {
        $this->_init('Ziffity\Blockcustomers\Model\Blockcustomers', 'Ziffity\Blockcustomers\Model\ResourceModel\Blockcustomers');
    }
}
