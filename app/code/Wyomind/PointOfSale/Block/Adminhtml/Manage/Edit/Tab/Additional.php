<?php

namespace Wyomind\PointOfSale\Block\Adminhtml\Manage\Edit\Tab;

class Additional extends \Magento\Backend\Block\Widget\Form\Generic implements \Magento\Backend\Block\Widget\Tab\TabInterface
{

    /**
     * @var \Wyomind\PointOfSale\Model\ResourceModel\Attributes\Collection
     */
    protected $attributesCollection;
    /**
     * @var \Magento\Cms\Model\Wysiwyg\Config
     */
    protected $wysiwygConfig;

    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        \Magento\Framework\Registry $registry,
        \Magento\Framework\Data\FormFactory $formFactory,
        \Wyomind\PointOfSale\Model\ResourceModel\Attributes\Collection $attributesCollection,
        \Magento\Cms\Model\Wysiwyg\Config $wysiwygConfig,
        array $data = []
    )
    {
        parent::__construct($context, $registry, $formFactory, $data);
        $this->attributesCollection = $attributesCollection;
        $this->wysiwygConfig = $wysiwygConfig;
    }

    protected function _prepareForm()
    {
        $model = $this->_coreRegistry->registry('pointofsale');
        $form = $this->_formFactory->create();
        $fieldset = $form->addFieldset('pickupatstore', ['legend' => __('Additional Information')]);


        $wysiwygConfig = $this->wysiwygConfig->getConfig();
        $wysiwygConfig->setData('add_widgets',false);
        foreach ($this->attributesCollection as $attribute) {
            if ($attribute->getType() == 1) {
                $fieldset->addField($attribute->getCode(), 'editor', [
                    'label' => __($attribute->getLabel()),
                    'name' => $attribute->getCode(),
                    'config' => $wysiwygConfig
                ]);
                ///**
                // * @var \Magento\Store\Model\StoreManagerInterface
                // */
                //protected $_storeManager;
                //
                ///**
                // * @var \Magento\Cms\Model\Template\FilterProvider
                // */
                //protected $_filterProvider;
                //
                //
                //$content = $model->getData('content');
                //return $this->_filterProvider->getBlockFilter()
                //    ->setStoreId($this->_storeManager->getStore()->getId())
                //    ->filter($content);
            } elseif ($attribute->getType() == 0) {
                $fieldset->addField($attribute->getCode(), 'textarea', [
                    'label' => __($attribute->getLabel()),
                    'name' => $attribute->getCode()
                ]);
            } elseif ($attribute->getType() == 2) {
                $fieldset->addField($attribute->getCode(), 'text', [
                    'label' => __($attribute->getLabel()),
                    'name' => $attribute->getCode()
                ]);
            }
        }

        $form->setValues($model->getData());
        $this->setForm($form);

        return parent::_prepareForm();
    }

    public function getTabLabel()
    {
        return __('Additional information');
    }

    public function getTabTitle()
    {
        return __('Additional information');
    }

    public function canShowTab()
    {
        return true;
    }

    public function isHidden()
    {
        return false;
    }
}