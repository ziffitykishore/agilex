<?php

/**
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace Ziffity\Reports\Block\Adminhtml\Salestatus;

/**
 * This is the Summary for this element.
 * 
 * @inheritDoc
 */
class Edit extends \Magento\Backend\Block\Widget\Form\Container
{
     /**
     * Core registry
     *
     * @var \Magento\Framework\Registry
     */
    protected $_coreRegistry = null;
 
    protected $_optionType;
  
    protected $productCollection;
    /**
     * @param \Magento\Backend\Block\Widget\Context $context
     * @param \Magento\Framework\Registry $registry
     * @param array $data
     */
    public function __construct(
        \Magento\Backend\Block\Widget\Context $context,
        \Magento\Framework\Registry $registry,
        \Magento\Catalog\Model\ResourceModel\Product\Collection $productCollection,
        array $data = []
    ) {
        $this->_coreRegistry = $registry;
        $this->_productCollection = $productCollection;
        parent::__construct($context, $data);
    }
 
    /**
     * Initialize cms page edit block
     *
     * @return void
     */
    protected function _construct()
    {
        
        $this->_objectId = 'id';
       $this->_blockGroup = 'Ziffity_Reports';
        $this->_headerText = __('Salestatus');
        $this->_controller = 'adminhtml_salestatus';
         parent::_construct();
        $this->buttonList->remove('delete');
         $this->buttonList->remove('save');
        $this->buttonList->remove('reset');
    } 
    public function getHeaderText()
    {
        
    }
 
    /**
     * Check permission for passed action
     *
     * @param string $resourceId
     * @return bool
     */
    protected function _isAllowedAction($resourceId)
    {
        return $this->_authorization->isAllowed($resourceId);
    }

 
}
