<?php

namespace Ziffity\Common\Block\Navigation;

class State extends \Magento\LayeredNavigation\Block\Navigation\State
{
    /**
     * @var string
     */
    protected $_template = 'WeltPixel_LayeredNavigation::layer/state.phtml';

    /**
     * Catalog layer
     *
     * @var \Magento\Catalog\Model\Layer
     */
    protected $_catalogLayer;

    /**
     * @var \WeltPixel\LayeredNavigation\Helper\Data
     */
    protected $_wpHelper;

    /**
     * @param \Magento\Framework\View\Element\Template\Context $context
     * @param \Magento\Catalog\Model\Layer\Resolver            $layerResolver
     * @param array                                            $data
     */
    public function __construct(
        \Magento\Framework\View\Element\Template\Context $context,
        \Magento\Catalog\Model\Layer\Resolver $layerResolver,
        \WeltPixel\LayeredNavigation\Helper\Data $wpHelper,
        array $data = []
    ) {
        $this->_catalogLayer = $layerResolver->get();
        $this->_wpHelper = $wpHelper;
        parent::__construct($context,$layerResolver, $data);
    }
    /**
     * Returns filter url for based on filter key
     * 
     * @param string $key
     * @return string
     */
    public function getClearFilterUrl($key)
    {
        $filterState = [];
        foreach ($this->getActiveFilters() as $item) {
            if ($item->getFilter()->getRequestVar() === $key) {
                $filterState[$item->getFilter()->getRequestVar()] = $item->getFilter()->getCleanValue();
            }
        }
        $params['_current'] = true;
        $params['_use_rewrite'] = true;
        $params['_query'] = $filterState;
        $params['_escape'] = true;
        return $this->_urlBuilder->getUrl('*/*/*', $params);
    }

}
