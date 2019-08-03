<?php
/**
 * Copyright © 2019 Wyomind. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace Wyomind\PointOfSale\Block\Adminhtml\Manage\Edit\Tab;

class Address extends \Magento\Backend\Block\Widget\Form\Generic implements \Magento\Backend\Block\Widget\Tab\TabInterface
{
    /**
     * @var \Magento\Directory\Model\Config\Source\Country|null
     */
    protected $_country = null;

    /**
     * @var \Magento\Directory\Model\ResourceModel\Region\Collection|null
     */
    protected $_regionCollection = null;

    /**
     * Address constructor.
     * @param \Magento\Backend\Block\Template\Context $context
     * @param \Magento\Framework\Registry $registry
     * @param \Magento\Framework\Data\FormFactory $formFactory
     * @param \Magento\Directory\Model\Config\Source\Country $country
     * @param \Magento\Directory\Model\ResourceModel\Region\Collection $regionCollection
     * @param array $data
     */
    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        \Magento\Framework\Registry $registry,
        \Magento\Framework\Data\FormFactory $formFactory,
        \Magento\Directory\Model\Config\Source\Country $country,
        \Magento\Directory\Model\ResourceModel\Region\Collection $regionCollection,
        array $data = []
    )
    {
        parent::__construct($context, $registry, $formFactory, $data);
        $this->_country = $country;
        $this->_regionCollection = $regionCollection;
    }

    protected function _prepareForm()
    {
        $model = $this->_coreRegistry->registry('pointofsale');
        $form = $this->_formFactory->create();
        $fieldset = $form->addFieldset('base_fieldset', ['legend' => __('Address & Contact')]);

        $fieldset->addField(
            'address_line_1',
            'text',
            [
                'label' => __('Address line 1'),
                'name' => 'address_line_1',
                'class' => 'required-entry',
                'required' => true
            ]
        );

        $fieldset->addField(
            'address_line_2',
            'text',
            [
                'label' => __('Address line 2'),
                'name' => 'address_line_2'
            ]
        );

        $fieldset->addField(
            'city',
            'text',
            [
                'label' => __('City'),
                'class' => 'required-entry',
                'name' => 'city',
                'required' => true
            ]
        );

        $fieldset->addField(
            'postal_code',
            'text',
            [
                'label' => __('Postal code'),
                'class' => 'required-entry',
                'name' => 'postal_code',
                'required' => true
            ]
        );

        $country = $fieldset->addField(
            'country_code',
            'select',
            [
                'name' => 'country_code',
                'label' => __('Country'),
                'values' => $this->_country->toOptionArray(),
                'class' => 'required-entry',
                'required' => true,
            ]
        );

        $country->setAfterElementHtml("<script>require(['jquery', 'pos_edit'], function($, pointofsale) {"
            . "var reloadStateUrl = '" . $this->getUrl('pointofsale/manage/state') . "country/';"
            . "$(document).on('change', '#country_code', function() {pointofsale.getstate(this, reloadStateUrl);});"
            . "});</script>");

        $stateCollection = $this->_regionCollection->addCountryFilter($model->getCountryCode())->load();
        $states = [];

        if ($this->getRequest()->getParam('id')) {
            foreach ($stateCollection as $_state) {
                $states[] = ['value' => $_state->getCode(), 'label' => $_state->getDefaultName()];
            }
        } else {
            $states[] = ['value' => null, 'label' => __('--Please select a state--') . $model->getCountryCode()];
        }

        $fieldset->addField(
            'state',
            'select',
            [
                'label' => __('State'),
                'name' => 'state',
                'values' => $states
            ]
        );

        $fieldset->addField(
            'main_phone',
            'text',
            [
                'label' => __('Main phone'),
                'name' => 'main_phone'
            ]
        );

        $fieldset->addField(
            'email',
            'text',
            [
                'label' => __('Email'),
                'name' => 'email'
            ]
        );

        $fieldset->addField(
            'image',
            'image',
            [
                'label' => __('Image'),
                'name' => 'image'
            ]
        );

        $fieldset->addField(
            'description',
            'textarea',
            [
                'label' => __('Description'),
                'name' => 'description'
            ]
        );

        $fieldset->addField(
            'hours',
            'hidden',
            ['name' => 'hours']
        );

        $fieldset->addField(
            'clone-hours',
            '\Wyomind\PointOfSale\Block\Adminhtml\Manage\Renderer\Hours',
            [
                'label' => __('Hours'),
                'name' => 'clone-hours'
            ]
        );

        $fieldset->addField(
            'days_off',
            'textarea',
            [
                'label' => __('Days off'),
                'name' => 'days_off',
                'note' => __('Each date on a new line formatted as follows: yyyy-mm-dd HH:ii-HH:ii<br/>e.g: 2019-01-01<br/>2019-12-24 16:30-20:00<br/>2019-12-25')
            ]
        );

        $form->setValues($model->getData());
        $this->setForm($form);

        return parent::_prepareForm();
    }

    public function getTabLabel()
    {
        return __('Address & Hours');
    }

    public function getTabTitle()
    {
        return __('Address & Hours');
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