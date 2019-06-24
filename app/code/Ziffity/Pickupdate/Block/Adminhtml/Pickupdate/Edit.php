<?php

namespace Ziffity\Pickupdate\Block\Adminhtml\Pickupdate;

class Edit extends \Magento\Backend\Block\Widget\Form\Container
{
    /**
     * Core registry
     *
     * @var \Magento\Framework\Registry
     */
    protected $_coreRegistry = null;

    /**
     * @param \Magento\Backend\Block\Widget\Context $context
     * @param \Magento\Framework\Registry $registry
     * @param array $data
     */
    public function __construct(
        \Magento\Backend\Block\Widget\Context $context,
        \Magento\Framework\Registry $registry,
        array $data = []
    ) {

        $this->_coreRegistry = $registry;
        parent::__construct($context, $data);


    }

    /**
     * Initialize form
     * Add standard buttons
     * Add "Save and Apply" button
     * Add "Save and Continue" button
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_objectId = 'id';
        $this->_blockGroup = 'Ziffity_Pickupdate';
        $this->_controller = 'adminhtml_pickupdate';

        parent::_construct();

        $this->buttonList->update('save', 'label', __('Save Pickup Date'));

        $backUrl = $this->getUrl('sales/order/view', array('order_id' => $this->getRequest()->getParam('order_id')));
        $this->buttonList->update('back', 'onclick', "setLocation('{$backUrl}')");

    }
}