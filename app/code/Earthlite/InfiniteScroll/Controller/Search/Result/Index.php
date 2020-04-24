<?php

namespace Earthlite\InfiniteScroll\Controller\Search\Result;

use Magento\Catalog\Model\Layer\Resolver;
use Magento\Catalog\Model\Session;
use Magento\CatalogSearch\Helper\Data;
use Magento\Framework\App\Action\Action;
use Magento\Framework\App\Action\Context;
use Magento\Framework\Json\Helper\Data as JsonData;
use Magento\Search\Model\QueryFactory;
use Magento\Store\Model\StoreManagerInterface;
use Mageplaza\AjaxLayer\Helper\Data as HelperData;

/**
 * Class Index
 * @package Earthlite\InfiniteScroll\Controller\Search\Result
 */
class Index extends \Mageplaza\AjaxLayer\Controller\Search\Result\Index
{
    /**
     * Catalog session
     *
     * @var Session
     */
    protected $_catalogSession;

    /**
     * @var StoreManagerInterface
     */
    protected $_storeManager;

    /**
     * @type JsonData
     */
    protected $_jsonHelper;

    /**
     * @type \Mageplaza\LayeredNavigation\Helper\Data
     */
    protected $_moduleHelper;

    /**
     * @type Data
     */
    protected $_helper;

    /**
     * @var QueryFactory
     */
    private $_queryFactory;

    /**
     * Catalog Layer Resolver
     *
     * @var Resolver
     */
    private $layerResolver;

    /**
     * Index constructor.
     * @param Context $context
     * @param Session $catalogSession
     * @param StoreManagerInterface $storeManager
     * @param QueryFactory $queryFactory
     * @param Resolver $layerResolver
     * @param Data $helper
     * @param JsonData $jsonHelper
     * @param HelperData $moduleHelper
     */
    public function __construct(
        Context $context,
        Session $catalogSession,
        StoreManagerInterface $storeManager,
        QueryFactory $queryFactory,
        Resolver $layerResolver,
        Data $helper,
        JsonData $jsonHelper,
        HelperData $moduleHelper
    )
    {
	 	$this->_storeManager   = $storeManager;
        $this->_catalogSession = $catalogSession;
        $this->_queryFactory   = $queryFactory;
        $this->layerResolver   = $layerResolver;
        $this->_jsonHelper     = $jsonHelper;
        $this->_moduleHelper   = $moduleHelper;
        $this->_helper         = $helper;
        parent::__construct($context, $catalogSession, $storeManager, $queryFactory, $layerResolver, $helper, $jsonHelper, $moduleHelper);
    }

    /**
     * @return \Magento\Framework\App\ResponseInterface|\Magento\Framework\Controller\ResultInterface|void
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function execute()
    {
        $this->layerResolver->create(Resolver::CATALOG_LAYER_SEARCH);
        /* @var $query \Magento\Search\Model\Query */
        $query = $this->_queryFactory->get();

        $query->setStoreId($this->_storeManager->getStore()->getId());

        if ($query->getQueryText() != '') {
            if ($this->_helper->isMinQueryLength()) {
                $query->setId(0)->setIsActive(1)->setIsProcessed(1);
            } else {
                $query->saveIncrementalPopularity();

                if ($query->getRedirect()) {
                    $this->getResponse()->setRedirect($query->getRedirect());

                    return;
                }
            }

            $this->_helper->checkNotes();
            $this->_view->loadLayout();

            if ($this->_moduleHelper->ajaxEnabled() && $this->getRequest()->isAjax()) {
                $request_body = file_get_contents('php://input');
	            
	            if($request_body)
	            {
	                $navigation = $this->_view->getLayout()->getBlock('catalogsearch.leftnav');
	                $products   = $this->_view->getLayout()->getBlock('search.result');
	                $result     = [
	                    'products'   => $products->toHtml(),
	                    'navigation' => $navigation->toHtml()
	                ];
                	$this->getResponse()->representJson($this->_jsonHelper->jsonEncode($result));
                }
                else
                {
                	$this->_view->renderLayout();	
                }
            } else {
                $this->_view->renderLayout();
            }
        } else {
            $this->getResponse()->setRedirect($this->_redirect->getRedirectUrl());
        }
    }
}
