<?php
/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
namespace Mconnect\Ajaxlogin\Block;

/**
 * Description of Myaccount
 *
 * @author Ziffity
 */
class Myaccount extends \Magento\Customer\Block\Account\Link
{
    /**
     * @return string
     */
    public function getHref()
    {
        return $this->_customerUrl->getAccountUrl();
    }
}
