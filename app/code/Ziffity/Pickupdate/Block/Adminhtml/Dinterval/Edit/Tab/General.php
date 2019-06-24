<?php

namespace Ziffity\Pickupdate\Block\Adminhtml\Dinterval\Edit\Tab;

use Magento\Backend\Block\Widget\Form\Generic;
use Magento\Backend\Block\Widget\Tab\TabInterface;

class General extends Generic implements TabInterface
{
    /**
     * @var \Magento\Store\Model\System\Store
     */
    protected $_systemStore;

    /**
     * @var \Ziffity\Pickupdate\Helper\Data
     */
    protected $helper;

    /**
     * @var \Magento\Config\Model\Config\Source\Yesno
     */
    private $yesno;

    /**
     * General constructor.
     *
     * @param \Magento\Backend\Block\Template\Context   $context
     * @param \Magento\Framework\Registry               $registry
     * @param \Magento\Framework\Data\FormFactory       $formFactory
     * @param \Magento\Store\Model\System\Store         $systemStore
     * @param \Ziffity\Pickupdate\Helper\Data           $helper
     * @param \Magento\Config\Model\Config\Source\Yesno $yesno
     * @param array                                     $data
     */
    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        \Magento\Framework\Registry $registry,
        \Magento\Framework\Data\FormFactory $formFactory,
        \Magento\Store\Model\System\Store $systemStore,
        \Ziffity\Pickupdate\Helper\Data $helper,
        \Magento\Config\Model\Config\Source\Yesno $yesno,
        array $data = []
    ) {
        parent::__construct($context, $registry, $formFactory, $data);
        $this->_systemStore = $systemStore;
        $this->helper     = $helper;
        $this->yesno        = $yesno;
    }

    /**
     * Return Tab label
     *
     * @return \Magento\Framework\Phrase
     */
    public function getTabLabel()
    {
        return __('Properties');
    }

    /**
     * Prepare title for tab
     *
     * @return \Magento\Framework\Phrase
     */
    public function getTabTitle()
    {
        return __('General');
    }

    /**
     * Returns status flag about this tab can be showed or not
     *
     * @return bool
     */
    public function canShowTab()
    {
        return true;
    }

    /**
     * Returns status flag about this tab hidden or not
     *
     * @return bool
     */
    public function isHidden()
    {
        return false;
    }

    protected function _prepareForm()
    {
        /** @var \Ziffity\Pickupdate\Model\Dinterval $model */
        $model = $this->_coreRegistry->registry('current_ziffity_pickupdate_dinterval');
        $yesno = $this->yesno->toOptionArray();

        /** @var \Magento\Framework\Data\Form $form */
        $form = $this->_formFactory->create();
        $form->setHtmlIdPrefix('ziffity_pickupdate_');

        $fieldset = $form->addFieldset('base_fieldset', ['legend' => __('General')]);

        if ($model->getId()) {
            $fieldset->addField('dinterval_id', 'hidden', ['name' => 'dinterval_id']);
        }

        if ($this->_storeManager->isSingleStoreMode()) {
            $storeId = $this->_storeManager->getStore(true)->getStoreId();
            $fieldset->addField('store_ids', 'hidden', ['name' => 'store_ids[]', 'value' => $storeId]);
            $model->setStoreIds($storeId);
        } else {
            $field = $fieldset->addField(
                'store_ids',
                'multiselect',
                [
                    'name'     => 'store_ids[]',
                    'label'    => __('Stores'),
                    'title'    => __('Stores'),
                    'values'   => $this->_systemStore->getStoreValuesForForm(false, true),
                    'required' => true,
                ]
            );
            $renderer = $this->getLayout()->createBlock(
                'Magento\Backend\Block\Store\Switcher\Form\Renderer\Fieldset\Element'
            );
            $field->setRenderer($renderer);
        }

        $eachYearElement = $fieldset->addField(
            'each_year',
            'select',
            [
                'label' => __('Each year'),
                'title' => __('Each year'),
                'name' => 'each_year',
                'values' => $yesno,
                'value' => $model->isForEachYear()
            ]
        );

        $eachMonthElement = $fieldset->addField(
            'each_month',
            'select',
            [
                'label' => __('Each month'),
                'title' => __('Each month'),
                'name' => 'each_month',
                'values' => $yesno,
                'value' => $model->isForEachMonth()
            ]
        );

        $fieldset->addField(
            'from_day',
            'select',
            [
                'label' => __('From Day'),
                'title' => __('From Day'),
                'name' => 'from_day',
                'values' => $this->helper->getDays()
            ]
        );

        $fromMonthElement = $fieldset->addField(
            'from_month',
            'select',
            [
                'label' => __('From Month'),
                'title' => __('From Month'),
                'name' => 'from_month',
                'values' => $this->helper->getMonths()
            ]
        );

        $fromYearElement = $fieldset->addField(
            'from_year',
            'select',
            [
                'label' => __('From Year'),
                'title' => __('From Year'),
                'name' => 'from_year',
                'values' => $this->helper->getYears()
            ]
        );

        $fieldset->addField(
            'to_day',
            'select',
            [
                'label' => __('To Day'),
                'title' => __('To Day'),
                'name' => 'to_day',
                'values' => $this->helper->getDays()
            ]
        );

        $toMonthElement = $fieldset->addField(
            'to_month',
            'select',
            [
                'label' => __('To Month'),
                'title' => __('To Month'),
                'name' => 'to_month',
                'values' => $this->helper->getMonths()
            ]
        );

        $toYearElement = $fieldset->addField(
            'to_year',
            'select',
            [
                'label' => __('To Year'),
                'title' => __('To Year'),
                'name' => 'to_year',
                'values' => $this->helper->getYears()
            ]
        );

        $fieldset->addField(
            'description',
            'text',
            [
                'label' => __('Description'),
                'title' => __('Description'),
                'name' => 'description'
            ]
        );

        $form->addValues($model->getData());

        /**
         * define field dependencies
         * @var \Magento\Backend\Block\Widget\Form\Element\Dependence $dependence
         */
        $dependence = $this
            ->getLayout()
            ->createBlock('Magento\Backend\Block\Widget\Form\Element\Dependence');

        /* Each Year */
        $dependence->addFieldMap($eachYearElement->getHtmlId(), $eachYearElement->getName())
            ->addFieldMap($fromYearElement->getHtmlId(), $fromYearElement->getName())
            ->addFieldMap($toYearElement->getHtmlId(), $toYearElement->getName())
            ->addFieldDependence(
                $fromYearElement->getName(),
                $eachYearElement->getName(),
                '0'
            )
            ->addFieldDependence(
                $toYearElement->getName(),
                $eachYearElement->getName(),
                '0'
            );

        /* Each Month */
        $dependence->addFieldMap($eachMonthElement->getHtmlId(), $eachMonthElement->getName())
            ->addFieldMap($fromMonthElement->getHtmlId(), $fromMonthElement->getName())
            ->addFieldMap($toMonthElement->getHtmlId(), $toMonthElement->getName())
            ->addFieldDependence(
                $fromMonthElement->getName(),
                $eachMonthElement->getName(),
                '0'
            )
            ->addFieldDependence(
                $toMonthElement->getName(),
                $eachMonthElement->getName(),
                '0'
            );
        $this->setChild('form_after', $dependence);

        $this->setForm($form);

        return parent::_prepareForm();
    }
}
