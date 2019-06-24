<?php

namespace Ziffity\Pickupdate\Block\Adminhtml\Pickupdate\Edit;

use Magento\Backend\Block\Widget\Form as WidgetForm;

class Form extends \Magento\Backend\Block\Widget\Form\Generic
{

    /**
     * @var \Magento\Framework\App\Config\ScopeConfigInterface
     */
    protected $scopeConfig;
    /**
     * @var \Ziffity\Pickupdate\Helper\Data
     */
    protected $pickupHelper;
    /**
     * @var \Magento\Framework\Registry
     */
    protected $coreRegistry;
    /**
     * @var \Magento\Framework\Stdlib\DateTime\DateTime
     */
    protected $date;

    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        \Magento\Framework\Registry $registry,
        \Magento\Framework\Data\FormFactory $formFactory,
        \Ziffity\Pickupdate\Helper\Data $pickupHelper,
        \Magento\Framework\Registry $coreRegistry,
        \Magento\Framework\Stdlib\DateTime\DateTime $date,
        array $data = []
    ) {
        $this->scopeConfig = $context->getScopeConfig();
        $this->pickupHelper = $pickupHelper;
        $this->coreRegistry = $coreRegistry;
        $this->date = $date;
        parent::__construct($context, $registry, $formFactory, $data);
    }


    /**
     * @return void
     */
    protected function _construct()
    {
        parent::_construct();
        $this->setId('edit_form');
        $this->setTitle(__('Edit'));
    }

    /**
     * @return WidgetForm
     */
    protected function _prepareForm()
    {
        $orderId = $this->coreRegistry->registry('current_order')->getId();

        /** @var \Magento\Framework\Data\Form $form */
        $form = $this->_formFactory->create(
            [
                'data' => [
                    'id' => 'edit_form',
                    'action' => $this->getUrl('ziffity_pickupdate/*/save', ['order_id' => $orderId]),
                    'method' => 'post',
                ],
            ]
        );

        $storeId = $this->_storeManager->getStore(true)->getStoreId();

        $fieldset = $form->addFieldset('base_fieldset', ['legend' => __('Pickup Date')]);

        $fieldset->addField('date', 'Ziffity\Pickupdate\Block\Adminhtml\Sales\Order\Renderer\Date', array(
            'label'    => __('Pickup Date'),
            'title'    => __('Pickup Date'),
            'name'     => 'date',
            'format'       => $this->scopeConfig->getValue('pickupdate/date_field/format'),
            'required'     => $this->scopeConfig->getValue('pickupdate/date_field/required'),
            'date_format'  => $this->scopeConfig->getValue('pickupdate/date_field/format'),
            'min_date'     => $this->date->date($this->pickupHelper->getPhpFormat())
        ));

        $fieldset->addField('clear', 'checkbox', array(
            'label'    => __('Reset Pickup Date Value'),
            'title'    => __('Reset Pickup Date Value'),
            'name'     => 'clear'
        ));

        if ($this->scopeConfig->getValue('pickupdate/time_field/enabled_time')) {
            $options = $this->pickupHelper->getTIntervals($storeId);
            if (!empty($options)) {
                $fieldset->addField('tinterval_id', 'select', array(
                    'label'    => __('Pickup Time Interval'),
                    'title'    => __('Pickup Time Interval'),
                    'name'     => 'tinterval_id',
                    'required' => $this->scopeConfig->getValue('pickupdate/time_field/required'),
                    'values'   => $options
                ));
            }
        }

        if ($this->scopeConfig->getValue('pickupdate/comment_field/enabled_comment')) {
            $fieldset->addField('comment', 'textarea', array(
                'label'    => __('Pickup Comments'),
                'title'    => __('Pickup Comments'),
                'name'     => 'comment',
                'required' => $this->scopeConfig->getValue('pickupdate/comment_field/required'),
            ));
        }

        $fieldset->addField('notify', 'checkbox', array(
            'label'    => __('Notify Customer by Email'),
            'title'    => __('Notify Customer by Email'),
            'name'     => 'notify'
        ));

        $data = $this->coreRegistry->registry('current_ziffity_pickupdate')->getData();

        if (array_key_exists('date', $data)
            && ('0000-00-00' == $data['date']
                || '1970-01-01' == $data['date'])) {
            unset($data['date']);
        }

        $form->setValues($data);


        $form->setUseContainer(true);
        $this->setForm($form);
        return parent::_prepareForm();
    }
}