<?php
/**
 * Copyright © 2019 Wyomind. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace Wyomind\MassStockUpdate\Controller\Adminhtml;

/**
 * Class AbstractController
 * @package Wyomind\MassStockUpdate\Controller\Adminhtml
 */
abstract class AbstractController extends \Magento\Backend\App\Action
{

    public $module = "MassStockUpdate";

    protected $_resultForwardFactory = null;
    protected $_resultRedirectFactory = null;
    protected $_resultRawFactory = null;
    protected $_resultPageFactory = null;

    /**
     * AbstractController constructor.
     * @param \Magento\Backend\App\Action\Context $context
     * @param \Magento\Backend\Model\View\Result\ForwardFactory $resultForwardFactory
     * @param \Magento\Framework\Controller\Result\RawFactory $resultRawFactory
     * @param \Magento\Framework\View\Result\PageFactory $resultPageFactory
     */
    public function __construct(
        \Magento\Backend\App\Action\Context $context,
        \Magento\Backend\Model\View\Result\ForwardFactory $resultForwardFactory,
        \Magento\Framework\Controller\Result\RawFactory $resultRawFactory,
        \Magento\Framework\View\Result\PageFactory $resultPageFactory
    ) {
        parent::__construct($context);


        $this->_resultForwardFactory=$resultForwardFactory;
        $this->_resultRedirectFactory=$context->getResultRedirectFactory();
        $this->_resultRawFactory=$resultRawFactory;
        $this->_resultPageFactory=$resultPageFactory;
    }

    /**
     * @return bool
     */
    protected function _isAllowed()
    {
        return $this->_authorization->isAllowed('Wyomind_' . $this->module . '::profiles');
    }

    /**
     * @return \Magento\Framework\App\ResponseInterface|\Magento\Framework\Controller\ResultInterface
     */
    abstract public function execute();
}
