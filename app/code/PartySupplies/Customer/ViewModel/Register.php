<?php

namespace PartySupplies\Customer\ViewModel;

use Magento\Framework\UrlInterface;
use Magento\Framework\View\Element\Block\ArgumentInterface;
use PartySupplies\Customer\Helper\Constant;
use Magento\Framework\App\Config\ScopeConfigInterface;

class Register implements ArgumentInterface
{
    /**
     * @var UrlInterface
     */
    protected $urlBuilder;
    
    /**
     * @var ScopeConfigInterface
     */
    protected $scopeConfig;

    /**
     * @param UrlInterface $urlBuilder
     */
    public function __construct(UrlInterface $urlBuilder, ScopeConfigInterface $scopeConfig)
    {
        $this->urlBuilder = $urlBuilder;
        $this->scopeConfig = $scopeConfig;
    }

    public function getResellerRegisterationForm()
    {
        return $this->urlBuilder->getUrl('pub/media/reseller_certificate/').
            $this->scopeConfig->getValue('reseller_certification/general/upload_form');
    }

    /**
     * Retrieve Company register form url
     *
     * @return string
     */
    public function getRegisterUrl()
    {
        return $this->urlBuilder->getUrl(Constant::COMPANY_CREATE_URL);
    }

    /**
     * Retrieve Company register form post url
     *
     * @return string
     */
    public function getRegisterPostUrl()
    {
        return $this->urlBuilder->getUrl(Constant::ACCOUNT_CREATE_POST_URL);
    }
}
