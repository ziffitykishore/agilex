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
            \Magento\Store\Model\System\Store $systemStore,
            array $data = []
        ) {
            $this->_systemStore = $systemStore;
            $this->_coreRegistry = $registry;
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
        }
 
        /**
         * Prepare form
         *
         * @return $this
         */
        protected function _prepareForm()
        {
            $productId = $this->getRequest()->getParam('id');
            $sku = $this->getRequest()->getParam('sku');
            $name = $this->getRequest()->getParam('name');
            $qty = floor($this->getRequest()->getParam('qty'));
            $stockStatus = $this->getRequest()->getParam('stock_status');
       
           //Preparing the form
            $form = $this->_formFactory->create(
                ['id' => 'id', 'enctype' => 'multipart/form-data', 'method' => 'post']
            );
            
            $fieldset = $form->addFieldset(
                'base_fieldset',
                ['legend' => __($name), 'class' => 'fieldset-wide']
            );
 
            $fieldset->addField(
                'name',
                'label',
                ['value' => $name, 'label' => __('Product Name')]
            );
             $fieldset->addField(
                'sku',
                'label',
                ['value' => $sku, 'label' => __('SKU')]
            );
              $fieldset->addField(
                'stock_status',
                'label',
                ['value' => $stockStatus > 0 ? "In Stock" :" Out Of Stock", 'label' => __('Stock Status')]
            );
               $fieldset->addField(
                'qty',
                'label',
                ['value' => $qty, 'label' => __('Quantity Left')]
            );
             
            $form->setUseContainer(true);
            $this->setForm($form);
            return parent::_prepareForm();
        }
}
