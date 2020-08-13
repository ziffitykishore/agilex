<?php

/**
 * Unirgy LLC
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the EULA
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://www.unirgy.com/LICENSE-M1.txt
 *
 * @category   Unirgy
 * @package    Unirgy_RapidFlow
 * @copyright  Copyright (c) 2008-2009 Unirgy LLC (http://www.unirgy.com)
 * @license    http:///www.unirgy.com/LICENSE-M1.txt
 */
namespace Unirgy\RapidFlowSales\Block\Adminhtml\Profile\Sales;

use Magento\Backend\Block\Template\Context;
use Magento\Backend\Block\Widget\Form\Element\Dependence;
use Magento\Backend\Block\Widget\Form\Generic;
use Magento\Backend\Block\Widget\Form\Renderer\Fieldset;
use Magento\Framework\App\Filesystem\DirectoryList;
use Magento\Framework\App\Helper\AbstractHelper;
use Magento\Framework\Data\FormFactory;
use Magento\Framework\Date;
use Magento\Framework\Model\AbstractModel;
use Magento\Framework\Registry;
use Magento\Framework\Stdlib\DateTime;
use Magento\Framework\View\LayoutFactory;
use Unirgy\RapidFlowSales\Helper\Data as HelperData;
use Unirgy\RapidFlowSales\Model\Source;

class ExportOptions
    extends Generic
{
    /**
     * @var LayoutFactory
     */
    protected $_viewLayoutFactory;

    /**
     * @var Source
     */
    protected $source;

    /**
     * @var HelperData
     */
    protected $_helperData;

    public function __construct(Context $context,
        Registry $registry,
        FormFactory $formFactory,
        LayoutFactory $viewLayoutFactory,
        Source $source,
        HelperData $helperData,
        array $data = [])
    {
        $this->_viewLayoutFactory         = $viewLayoutFactory;
        $this->source                     = $source;
        $this->_helperData                = $helperData;

        parent::__construct($context, $registry, $formFactory, $data);
    }

    protected function _prepareForm()
    {
        $source = $this->getSource();

        $profile = $this->getProfile();
        $source->setProfile($profile);

        $form     = $this->getForm();
        $fieldset = $form->addFieldset('export_options_form', ['legend' => __('Export Options')]);

        $fieldset->addField('store_ids', 'multiselect', [
            'label'  => __('Stores to Export'),
            'name'   => 'options[store_ids]',
            'values' => $source->setPath('stores')->toOptionArray(),
            'value'  => $profile->getData('options/store_ids'),
        ]);

        $selected     = json_decode($profile->getData('options/row_types_json'), true);
        if(!$selected){
            $selected = [];
        }
        $rowTypesTree = $source->getSalesRowTypesTree($selected);
        // tree
        $form->addFieldset('export_row_types_fieldset', [
            'legend' => __('Export Row Types')
        ])->setRenderer(
            $this->_viewLayoutFactory->create()
                 ->createBlock(Fieldset::class)
                 ->setTemplate('Unirgy_RapidFlowSales::urfsales/row_types.phtml')
                 ->setRowTypesTreeJson(json_encode($rowTypesTree))
        );

        $fieldsetConditions = $form->addFieldset('export_conditions_form',
            ['legend' => __('Export Conditions')]);

        $fieldsetConditions->addField('date_from', 'date', [
                'name'         => 'options[date_from]',
                'html_id'      => 'date_from',
                //'readonly'     => 'readonly',
                'label'        => __('Filter Entities From'),
                'title'        => __('Filter Entities From'),
                'format'       => $this->_localeDate->getDateFormat(\IntlDateFormatter::MEDIUM),
                'input_format' => DateTime::DATE_INTERNAL_FORMAT,
                'single_click' => true,
                'value' => $profile->getData('options/date_from')
            ]
        );
        $fieldsetConditions->addField('date_to', 'date', [
                'name'         => 'options[date_to]',
                'html_id'      => 'date_to',
                //'readonly'     => 'readonly',
                'label'        => __('Filter Entities To'),
                'title'        => __('Filter Entities To'),
                'format'       => $this->_localeDate->getDateFormat(\IntlDateFormatter::MEDIUM),
                'input_format' => DateTime::DATE_INTERNAL_FORMAT,
                'single_click' => true,
                'value' => $profile->getData('options/date_to')
            ]
        );
        $fieldsetConditions->addField('date_filtered_types', 'multiselect', [
            'label'  => __('Filter by date range'),
            'name'   => 'options[date_filtered_types]',
            'values' => $source->setPath(Source::DATE_FILTERED_TYPES)->toOptionArray(),
            'value'  => $profile->getData('options/date_filtered_types'),
        ]);

        return parent::_prepareForm();
    }

    public function getForm()
    {
        if (null === $this->_form) {
            $this->setForm($this->_formFactory->create());
        }

        return parent::getForm();
    }

    /**
     * @return AbstractModel|Source
     */
    protected function getSource()
    {
        return $this->source;
    }

    /**
     * @return \Unirgy\RapidFlow\Model\Profile
     */
    protected function getProfile()
    {
        return $this->_coreRegistry->registry('profile_data');
    }

    /**
     * @return AbstractHelper|HelperData
     */
    protected function hlp()
    {
        return $this->_helperData;
    }

    /**
     * @return Dependence|\Magento\Framework\View\Element\AbstractBlock
     * @throws \InvalidArgumentException
     */
    protected function getDependenceBlock()
    {
        return $this->_viewLayoutFactory->create()->createBlock(Dependence::class);
    }

    protected function getMediaUrl($media)
    {
        return $this->_storeManager->getStore()->getBaseUrl(DirectoryList::MEDIA) . '/' . trim($media, '/');
    }
}
