<?php
/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
namespace Mconnect\Ajaxlogin\Block;

/**
 * Description of MyWishlist
 *
 * @author Rengaraj
 */
class MyWishlist extends \Magento\Wishlist\Block\Link
{
    protected $customerSession;
    /**
     * @param \Magento\Framework\View\Element\Template\Context $context
     * @param \Magento\Wishlist\Helper\Data $wishlistHelper
     * @param array $data
     */
    public function __construct(
        \Magento\Framework\View\Element\Template\Context $context,
        \Magento\Wishlist\Helper\Data $wishlistHelper,
        \Magento\Customer\Model\Session $customerSession, 
        array $data = []
    ) {
        $this->_wishlistHelper = $wishlistHelper;
        $this->customerSession = $customerSession;
        parent::__construct($context,$wishlistHelper, $data);
    }
}
