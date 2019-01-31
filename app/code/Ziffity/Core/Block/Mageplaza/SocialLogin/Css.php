<?php

/**
 * To remove unused css in Mageplaza
 *
 */
namespace Ziffity\Core\Block\Mageplaza\SocialLogin;

use Magento\Framework\View\Element\Template\Context;
use Mageplaza\SocialLogin\Helper\Data as DataHelper;

class Css extends \Mageplaza\SocialLogin\Block\Css
{
    /**
     * @type \Mageplaza\SocialLogin\Helper\Data
     */
    protected $_helper;

    /**
     * Css constructor.
     * @param \Magento\Framework\View\Element\Template\Context $context
     * @param \Mageplaza\SocialLogin\Helper\Data $helper
     * @param array $data
     */
    public function __construct(
        Context $context,
        DataHelper $helper,
        array $data = []
    )
    {
        parent::__construct($context, $helper, $data);
        $this->_helper = $helper;
    }
    
    /**
     * @return $this
     */
    protected function _prepareLayout()
    {
        parent::_prepareLayout();

        if ($this->_helper->isEnabled()) {
            $this->pageConfig->getAssetCollection()->remove('Mageplaza_Core::css/font-awesome.min.css');
        }

        return $this;
    }
}
