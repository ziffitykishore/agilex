<?php

namespace Wyomind\PointOfSale\Controller\Adminhtml;

abstract class Attributes extends \Magento\Backend\App\Action
{

    protected $_dataPersistor;
    protected $_resultJsonFactory;
    protected $_coreRegistry;
    protected $_filter;
    protected $_attributesModelFactory;
    protected $_attributesCollectionFactory;

    public function __construct(
        \Magento\Backend\App\Action\Context $context,
        \Magento\Framework\App\Request\DataPersistorInterface $dataPersistor,
        \Magento\Framework\Controller\Result\JsonFactory $resultJsonFactory,
        \Magento\Framework\Registry $coreRegistry,
        \Magento\Ui\Component\MassAction\Filter $filter,
        \Wyomind\PointOfSale\Model\AttributesFactory $attributesModelFactory,
        \Wyomind\PointOfSale\Model\ResourceModel\Attributes\CollectionFactory $attributesCollectionFactory
    )
    {
        parent::__construct($context);
        $this->_dataPersistor = $dataPersistor;
        $this->_resultJsonFactory = $resultJsonFactory;
        $this->_coreRegistry = $coreRegistry;
        $this->_filter = $filter;
        $this->_attributesModelFactory = $attributesModelFactory;
        $this->_attributesCollectionFactory = $attributesCollectionFactory;
    }

    protected function _initAction($title)
    {
        $this->_view->loadLayout();
        $this->_setActiveMenu('Magento_Backend::sales');
        $this->_view->getPage()->getConfig()->getTitle()->prepend(__('Point Of Sale > ') . $title);

        return $this;
    }
}