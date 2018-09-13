<?php
/**
 *  Ziffity AdvanceSearch
 */
namespace Ziffity\AdvanceSearch\Block\Advanced;

use Magento\Catalog\Model\Layer\Resolver as LayerResolver;
use Magento\CatalogSearch\Model\Advanced;
use Magento\Framework\UrlFactory;
use Magento\Framework\View\Element\Template\Context;

class Result extends \Magento\CatalogSearch\Block\Advanced\Result
{
    /**
     * Catalog layer
     *
     * @var \Magento\Catalog\Model\Layer
     */
    protected $_catalogLayer;

    /**
     * @param Context $context
     * @param Advanced $catalogSearchAdvanced
     * @param LayerResolver $layerResolver
     * @param UrlFactory $urlFactory
     * @param array $data
     */
    public function __construct(
        Context $context,
        Advanced $catalogSearchAdvanced,
        LayerResolver $layerResolver,
        UrlFactory $urlFactory,
        array $data = []
    ) {
        $this->_catalogLayer = $layerResolver->get();
        parent::__construct(
            $context,
            $catalogSearchAdvanced,
            $layerResolver,
            $urlFactory,
            $data
        );
    }

    /**
     * Set order options
     *
     * @return void
     */
    public function setListOrders()
    {
        /* @var $category \Magento\Catalog\Model\Category */
        $category = $this->_catalogLayer->getCurrentCategory();
        $availableOrders = $category->getAvailableSortByOptions();
        unset($availableOrders['position']);
        /*added relevance*/
        $availableOrders['relevance'] = __('Relevance');
        $availableOrders['name'] = __('Name');
        $this->getChildBlock('search_result_list')->setAvailableOrders($availableOrders);
    }
}
