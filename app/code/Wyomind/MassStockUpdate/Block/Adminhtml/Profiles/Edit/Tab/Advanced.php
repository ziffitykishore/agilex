<?php
/**
 * Copyright © 2019 Wyomind. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace Wyomind\MassStockUpdate\Block\Adminhtml\Profiles\Edit\Tab;

/**
 * Class Advanced
 * @package Wyomind\MassStockUpdate\Block\Adminhtml\Profiles\Edit\Tab
 */
class Advanced extends \Magento\Backend\Block\Widget\Form\Generic
    implements \Magento\Backend\Block\Widget\Tab\TabInterface
{

    /**
     * @var string
     */
    public $module="MassStockUpdate";
    /**
     * @var null|\Wyomind\MassStockUpdate\Helper\Data
     */
    protected $_dataHelper=null;

    /**
     * Advanced constructor.
     * @param \Magento\Backend\Block\Template\Context $context
     * @param \Magento\Framework\Registry $registry
     * @param \Wyomind\MassStockUpdate\Helper\Data $dataHelper
     * @param \Magento\Framework\Data\FormFactory $formFactory
     * @param array $data
     */
    public function __construct(
        \Magento\Backend\Block\Template\Context $context, \Magento\Framework\Registry $registry,
        \Wyomind\MassStockUpdate\Helper\Data $dataHelper,
        \Magento\Framework\Data\FormFactory $formFactory, array $data=[]
    ) {
        $this->_dataHelper=$dataHelper;
        parent::__construct($context, $registry, $formFactory, $data);
    }

    /**
     * @return \Magento\Backend\Block\Widget\Form\Generic
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    protected function _prepareForm()
    {


        $model=$this->_coreRegistry->registry('profile');

        $form=$this->_formFactory->create();
        $class="\Wyomind\\" . $this->module . "\Helper\Data";
        foreach ($class::MODULES as $module) {
            $objectManager=\Magento\Framework\App\ObjectManager::getInstance();
            $resource=$objectManager->get("\Wyomind\\" . $this->module . "\Model\ResourceModel\Type\\" . $module);
            if ($resource->hasFields()) {
                $fieldset=$form->addFieldset($this->module . '_' . strtolower($module) . '_option', ['legend'=>__($this->_dataHelper->fromCamelCase($module) . ' Settings')]);
                $resource->getFields($fieldset, $this, $this);
            }
        }


        $form->setValues($model->getData());
        $this->setForm($form);


        return parent::_prepareForm();
    }

    /**
     * @return mixed
     */
    public function getProfileId()
    {
        $model=$this->_coreRegistry->registry('profile');
        return $model->getId();
    }

    /**
     * @return \Magento\Framework\Phrase|string
     */
    public function getTabLabel()
    {
        return __('Advanced Settings');
    }

    /**
     * @return \Magento\Framework\Phrase|string
     */
    public function getTabTitle()
    {
        return __('Advanced Settings');
    }

    /**
     * @return bool
     */
    public function canShowTab()
    {
        return true;
    }

    /**
     * @return bool
     */
    public function isHidden()
    {
        return false;
    }

}
