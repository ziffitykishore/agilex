<?php
/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
namespace Mconnect\Ajaxlogin\Block;

//use Magento\Framework\View\Element\Template;

class Footerlogin extends \Magento\Customer\Block\Account\Dashboard
{
    /**
     * @return string
     */
    public function getHrefs()
    {
        return $this->_urlBuilder->getUrl('customer/account/edit');
    }
    public function getPaymentUrl()
    {
        return $this->_urlBuilder->getUrl('vault/cards/listaction');
    }

    public function getWishlisturl()
    {
        return $this->_urlBuilder->getUrl('wishlist/');
    }
    public function getOrderUrl(){
       return $this->_urlBuilder->getUrl('sales/order/history');
    }
   
}

/**
 * Description of Myaccount
 *
 * @author Ziffity
 */
/**class Footer_Login extends \Magento\Customer\Block\Account\Link
{
    
    public function getHrefs()
    {
        return $this->_customerUrl->getUrl('customer/account/edit');
    }
}
**/
