<?php

namespace Ziffity\Blockcustomers\Block\Adminhtml\Order;

use Magento\Framework\DB\Select;

/**
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * This is the Summary for this element.
 * 
 * @inheritDoc
 */
class Grid extends \Magento\Backend\Block\Widget\Grid\Extended
{

    /**
     * @var \Magento\Framework\Module\Manager
     */
    protected $moduleManager;

    /**
     * @var \Magento\Eav\Model\ResourceModel\Entity\Attribute\Set\CollectionFactory]
     */
    protected $_setsFactory;

    /**
     * @var \Magento\Catalog\Model\ProductFactory
     */
    protected $_productFactory;

    /**
     * @var \Magento\Catalog\Model\Product\Type
     */
    protected $_type;

    /**
     * @var \Magento\Catalog\Model\Product\Attribute\Source\Status
     */
    protected $_status;
    protected $customerFactory;

    /**
     * @var \Magento\Catalog\Model\Product\Visibility
     */
    protected $_visibility;

    /**
     * @var \Magento\Store\Model\WebsiteFactory
     */
    protected $_websiteFactory;
    protected $orderFactory;
    protected $loggerInterface;

    /**
     * @param \Magento\Backend\Block\Template\Context $context
     * @param \Magento\Backend\Helper\Data $backendHelper
     * @param \Magento\Store\Model\WebsiteFactory $websiteFactory
     * @param \Magento\Eav\Model\ResourceModel\Entity\Attribute\Set\CollectionFactory $setsFactory
     * @param \Magento\Catalog\Model\ProductFactory $productFactory
     * @param \Magento\Catalog\Model\Product\Type $type
     * @param \Magento\Catalog\Model\Product\Attribute\Source\Status $status
     * @param \Magento\Catalog\Model\Product\Visibility $visibility
     * @param \Magento\Framework\Module\Manager $moduleManager
     * @param array $data
     *
     * @SuppressWarnings(PHPMD.ExcessiveParameterList)
     */
    public function __construct(
    \Magento\Backend\Block\Template\Context $context, \Magento\Backend\Helper\Data $backendHelper, \Magento\Store\Model\WebsiteFactory $websiteFactory, \Magento\Customer\Model\CustomerFactory $customerFactory, \Psr\Log\LoggerInterface $loggerInterface,
    //\Magento\Customer\Model\ResourceModel\Customer\Collection $collection1,
            \Magento\Sales\Model\OrderFactory $orderFactory, \Magento\Framework\Module\Manager $moduleManager, array $data = []
    )
    {

        $this->_customerFactory = $customerFactory;
        $this->_log = $loggerInterface;
        $this->_websiteFactory = $websiteFactory;
        $this->moduleManager = $moduleManager;
        $this->_orderFactory = $orderFactory;
        parent::__construct($context, $backendHelper, $data);
    }

    /**
     * @return void
     */
    protected function _construct()
    {
        parent::_construct();
        $this->setId('CustomerGrid');
        $this->setDefaultSort('id');
        $this->setDefaultDir('DESC');
        $this->setUseAjax(false);
    }

    /**
     * @return Store
     */
    protected function _getStore()
    {
        $storeId = (int) $this->getRequest()->getParam('store', 0);
        return $this->_storeManager->getStore($storeId);
    }

    /**
     * @return $this
     */
    protected function _prepareCollection()
    {
        try {


            $model = $this->_customerFactory->create();
            $customerCollection = $model->getCollection();
            $customerCollection->getSelect()
                    ->reset(\Zend_Db_Select::COLUMNS)->columns(
                    ['entity_id', 'name' => "CONCAT(firstname, ' ', lastname)", 'email' => 'email']);
            /*    $sqlQ = clone $customerCollection->getSelect();


              $orderModel = $this->_orderFactory->create();
              $orderCollection = $orderModel->getCollection();
              $orderCollection->addAttributeToFilter('customer_is_guest', ['eq' => 1]);


              $orderCollection->getSelect()
              ->reset(\Zend_Db_Select::COLUMNS)->columns(
              [new \Zend_Db_Expr('"NULL" AS entity_id'), 'name' => "CONCAT(customer_firstname, ' ', customer_lastname)", 'email' => 'customer_email']);

             */
            
            // $orderCollection->addFieldToSelect(array('name' => 'customer_firstname', 'email' => 'customer_email'));

           /* $sqlT = clone $orderCollection->getSelect();
            $this->_log->debug((string) $sqlT);


            $collection1 = $orderCollection->getSelect()->reset()->union([$sqlQ, $sqlT]);*/
            $this->setCollection($customerCollection);

            parent::_prepareCollection();

            return $this;
        } catch (Exception $e) {
            echo $e->getMessage();
            
        }
    }

    /**
     * @return $this
     * @SuppressWarnings(PHPMD.ExcessiveMethodLength)
     */
    protected function _prepareColumns()
    {
        $this->addColumn(
                'id', [
            'header' => __('ID'),
            'type' => 'number',
            'index' => 'entity_id',
            'header_css_class' => 'col-id',
            'column_css_class' => 'col-id'
                ]
        );


        $this->addColumn(
                'name', [
            'header' => __('name'),
            'type' => 'string',
            'index' => 'name',
            'class' => 'name'
                ]
        );
        $this->addColumn(
                'email', [
            'header' => __('email'),
            'index' => 'email',
            'class' => 'email'
                ]
        );
        $this->addColumn(
                'Actions', [
            'header' => __('Actions'),
            'type' => 'action',
            'getter' => 'getId',
            'actions' => [
                [
                    'caption' => __('Block'),
                    'url' => [
                        'base' => '*/*/blockcustomers'
                    ],
                    'field' => 'id'
                ]
            ],
            'filter' => false,
            'sortable' => false,
            'index' => 'stores',
            'header_css_class' => 'col-action',
            'column_css_class' => 'col-action'
                ]
        );
        /* {{CedAddGridColumn}} */

//        $block = $this->getLayout()->getBlock('grid.bottom.links');
//        if ($block) {
//            $this->setChild('grid.bottom.links', $block);
//        }

        return parent::_prepareColumns();
    }

    /**
     * @return $this
     */
    protected function _prepareMassaction()
    {
        $this->setMassactionIdField('id');
        $this->getMassactionBlock()->setFormFieldName('id');


        $this->getMassactionBlock()->addItem(
                'block', array(
            'label' => __('Block'),
            'url' => $this->getUrl('blockcustomers/*/massBlock')
                )
        );
        return $this;
    }

    /**
     * @return string
     */
    public function getGridUrl()
    {
        return $this->getUrl('blockcustomers/*/index', ['_current' => true]);
    }

    /**
     * @param \Magento\Catalog\Model\Product|\Magento\Framework\Object $row
     * @return string
     */
    public function getRowUrl($row)
    {
        return $this->getUrl(
                        'blockcustomers/*/edit', ['store' => $this->getRequest()->getParam('store'), 'id' => $row->getId()]
        );
    }

}
