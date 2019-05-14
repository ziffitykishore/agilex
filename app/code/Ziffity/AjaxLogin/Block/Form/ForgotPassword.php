<?php

namespace Ziffity\AjaxLogin\Block\Form;

use Magento\Framework\View\Element\Template\Context;
use Ziffity\AjaxLogin\Helper\Data;

class ForgotPassword extends \Magento\Framework\View\Element\Template
{
    /**
     * @var Ziffity\AjaxLogin\Helper\Data
     */
    protected $blockHelper;

    /**
     * @param \Magento\Framework\View\Element\Template\Context  $context
     * @param Ziffity\AjaxLogin\Helper\Data                     $blockHelper
     * @param array $data
     */
    public function __construct(
        Context $context,
        Data $blockHelper,
        array $data = []
    ) {
        parent::__construct($context, $data);
        $this->blockHelper = $blockHelper;
       
    }

    /**
     * Retrieve form posting url
     *
     * @return string
     */
    public function getPostActionUrl()
    {
        return $this->getUrl('ajaxlogin/customer_ajax/resetpassword',['_secure' => true]);
    }
    
    /**
     * Returns the helper class
     * 
     * @return Ziffity\AjaxLogin\Helper\Data
     */
    public function getHelper() {
        return $this->blockHelper;
    }
    
}
