<?php

namespace PartySupplies\Customer\Ui\Component\Grid;

use Magento\Framework\View\Element\UiComponent\Control\ButtonProviderInterface;
use PartySupplies\Customer\Helper\Constant;

class Button implements ButtonProviderInterface
{
    /**
     * @var \Magento\Framework\UrlInterface
     */
    protected $urlBuilder;
    
    /**
     * @var \Magento\Framework\App\RequestInterface
     */
    protected $request;

    /**
     *
     * @param \Magento\Backend\Block\Widget\Context $context
     */
    public function __construct(
        \Magento\Backend\Block\Widget\Context $context
    ) {
        $this->urlBuilder = $context->getUrlBuilder();
        $this->request = $context->getRequest();
    }

    /**
     * Get button data
     *
     * @return array
     */
    public function getButtonData()
    {
        if ($this->request->getParam('account_type') === Constant::CUSTOMER) {
            $label = __('Add New User');
        } else {
            $label = __('Add New Company');
        }
        return [
            'label' => $label,
            'on_click' => sprintf("location.href = '%s';", $this->getUrl('*/*/new')),
            'class' => 'primary',
            'sort_order' => 10
        ];
    }

    /**
     * Generate url by route and parameters
     *
     * @param string $route
     * @param array $params
     * @return string
     */
    public function getUrl($route = '', $params = [])
    {
        return $this->urlBuilder->getUrl($route, $params);
    }
}
