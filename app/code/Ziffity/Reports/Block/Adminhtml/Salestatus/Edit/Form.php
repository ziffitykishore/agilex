<?php
namespace Ziffity\Reports\Block\Adminhtml\Salestatus\Edit;
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
class Form extends \Magento\Backend\Block\Widget\Form\Generic
{
    /**
         * @var \Magento\Store\Model\System\Store
         */
        protected $_systemStore;
         
        /**
         * Core registry
         *
         * @var \Magento\Framework\Registry
         */
        protected $_coreRegistry;
        
        protected $productFactory;


        /**
         * @param \Magento\Backend\Block\Template\Context $context
         * @param \Magento\Framework\Registry $registry
         * @param \Magento\Framework\Data\FormFactory $formFactory
         * @param \Magento\Store\Model\System\Store $systemStore
         * @param array $data
         */
        public function __construct(
            \Magento\Backend\Block\Template\Context $context,
            \Magento\Framework\Registry $registry,
            \Magento\Framework\Data\FormFactory $formFactory,
                \Magento\Catalog\Model\ProductFactory $productFactory,
            \Magento\Store\Model\System\Store $systemStore,
            array $data = []
        ) {
            $this->_systemStore = $systemStore;
            $this->_coreRegistry = $registry;
            $this->_productFactory = $productFactory;
            parent::__construct($context, $registry, $formFactory, $data);
        }
 
        /**
         * Init form
         *
         * @return void
         */
        protected function _construct()
        {
            parent::_construct();
            $this->setId('demo_form');
            $this->setTitle(__('Demo Information'));
        }
 
        /**
         * Prepare form
         *
         * @return $this
         */
        protected function _prepareForm()
        {
            $model = $this->_productFactory->create();
        $collection = $model->getCollection();
        $data = $collection->getData();
        foreach($data as $product){
            
        }
           //Preparing the form here.
            $form = $this->_formFactory->create(
                ['data' => ['id' => 'edit_form', 'enctype' => 'multipart/form-data', 'method' => 'post']]
            );
            $form->setHtmlIdPrefix('demo_');
 
            $fieldset = $form->addFieldset(
                'base_fieldset',
                ['legend' => __('Product Information'), 'class' => 'fieldset-wide']
            );
 
            $fieldset->addField(
                'name',
                'text',
                ['name' => 'name', 'label' => __('Product Name'), 'title' => __('Product Name')]
            );
             $fieldset->addField(
                'sku',
                'text',
                ['name' => 'sku', 'label' => __('SKU'), 'title' => __('SKU')]
            );
              $fieldset->addField(
                'stock_status',
                'text',
                ['name' => 'stock_status', 'label' => __('Stock Status'), 'title' => __('Stock Status')]
            );
               $fieldset->addField(
                'qty',
                'text',
                ['name' => 'qty', 'label' => __('Quantity Left'), 'title' => __('Quantity Left')]
            );
             
            $form->setUseContainer(true);
            $this->setForm($form);
            return parent::_prepareForm();
        }
}
