<?php
namespace Ziffity\Blockcustomers\Model\ResourceModel;
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
class Blockcustomers extends \Magento\Framework\Model\ResourceModel\Db\AbstractDb
{
    public  function _construct()
    {
       $this->_init('blocked_customers','id');
    }

}
