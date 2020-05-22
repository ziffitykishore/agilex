<?php

namespace Earthlite\Blog\Block\Search;

use Magento\Framework\Registry;
use Magento\Framework\View\Element\Template;
use Magento\Framework\View\Element\Template\Context;
use Magento\Theme\Block\Html\Breadcrumbs;
use Magento\Theme\Block\Html\Title;
use Mirasvit\Blog\Model\Config;
use Mirasvit\Blog\Model\ResourceModel\Post\Collection;

class Result extends \Mirasvit\Blog\Block\Search\Result
{
    /**
     * @var Registry
     */
    protected $registry;

    /**
     * @var Context
     */
    protected $context;

    /**
     * @var Config
     */
    protected $config;

    protected $_pageCollection;

    protected $_storeManager;    

    /**
     * @param Config   $config
     * @param Registry $registry
     * @param Context  $context
     * @param array    $data
     */
    public function __construct(
        Config $config,
        Registry $registry,
        Context $context,
        \Magento\Cms\Model\ResourceModel\Page\CollectionFactory $pageCollection,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        array $data = []
    ) {
        $this->config   = $config;
        $this->registry = $registry;
        $this->context  = $context;
        $this->_pageCollection = $pageCollection;
        $this->_storeManager = $storeManager;        
        parent::__construct($config, $registry, $context);        
    }

    public function getCmsPageFilters()
    {
        $reqFilter = $this->getRequest()->getParam('q');
    	$cmsPageCollection = $this->_pageCollection->create();
        $cmsPageCollection->addStoreFilter($this->getStoreId())
                ->addFieldToFilter('title', array('like' => '%'.$reqFilter.'%'))
                ->addFieldToFilter('store_id', array('like' => 1));
        return $cmsPageCollection;
    }

    /**
     * Get store identifier
     *
     * @return  int
     */
    public function getStoreId()
    {
        return $this->_storeManager->getStore()->getId();
    }  
}
