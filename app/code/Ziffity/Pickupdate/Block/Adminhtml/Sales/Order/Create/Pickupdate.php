<?php

namespace Ziffity\Pickupdate\Block\Adminhtml\Sales\Order\Create;

use Ziffity\Pickupdate\Model\PickupdateFactory;
use Magento\Framework\View\Element\Template\Context;

class Pickupdate extends \Magento\Framework\View\Element\Template
{

    /**
     * @var PickupdateFactory
     */
    protected $pickupDateFactory;

    /**
     * @var \Magento\Framework\Registry
     */
    protected $coreRegistry;

    /**
     * @var \Ziffity\Pickupdate\Helper\Data
     */
    protected $pickupHelper;

    /**
     * @var \Magento\Framework\Data\FormFactory
     */
    protected $formFactory;

    /**
     * @var \Magento\Framework\App\Config\ScopeConfigInterface
     */
    protected $scopeConfig;

    /**
     * @var \Ziffity\Pickupdate\Model\ResourceModel\Pickupdate
     */
    protected $pickupdateResourceModel;

    /**
     * @var \Magento\Backend\Model\Session\Quote
     */
    protected $sessionQuote;

    /**
     * @var \Magento\Framework\Stdlib\DateTime\DateTime
     */
    protected $date;

    /**
     * @var \Ziffity\Pickupdate\Model\PickupdateConfigProvider
     */
    private $configProvider;

    public function __construct(
        Context $context,
        PickupdateFactory $pickupDateFactory,
        \Magento\Framework\Registry $coreRegistry,
        \Ziffity\Pickupdate\Helper\Data $pickupHelper,
        \Magento\Framework\Data\FormFactory $formFactory,
        \Ziffity\Pickupdate\Model\ResourceModel\Pickupdate $pickupdateResourceModel,
        \Magento\Backend\Model\Session\Quote $sessionQuote,
        \Magento\Framework\Stdlib\DateTime\DateTime $date,
        \Ziffity\Pickupdate\Model\PickupdateConfigProvider $configProvider,
        array $data = []
    ) {
        $this->pickupDateFactory = $pickupDateFactory;
        $this->coreRegistry = $coreRegistry;
        $this->pickupHelper = $pickupHelper;
        $this->sessionQuote = $sessionQuote;
        $this->pickupdateResourceModel = $pickupdateResourceModel;
        $this->formFactory = $formFactory;
        $this->scopeConfig = $context->getScopeConfig();
        $this->date = $date;
        $this->configProvider = $configProvider;
        parent::__construct($context, $data);
    }

    protected function _construct()
    {
        parent::_construct();

        $pickupDate = $this->pickupDateFactory->create();
        $orderId = 0;
        if ($this->sessionQuote->getOrderId()) { // edit order
            $orderId = $this->sessionQuote->getOrderId();
        } elseif ($this->sessionQuote->getReordered()) { // reorder
            $orderId = $this->sessionQuote->getReordered();
        }

        if ($orderId) {
            $this->pickupdateResourceModel->load($pickupDate, $orderId, 'order_id');
            $this->coreRegistry->register('current_pickupdate', $pickupDate);
        }

        $this->setTemplate('Ziffity_Pickupdate::pickup_create.phtml');
    }

    public function getFormElements()
    {
        $form = $this->formFactory->create();
        $form->setHtmlIdPrefix('ziffity_pickupdate_');
        $availableFields = $this->pickupHelper->whatShow('order_create');
        $storeId = $this->_storeManager->getStore(true)->getStoreId();

        $fieldset = $form->addFieldset('base_fieldset', ['legend' => __('Pickup Date')]);

        if (in_array('date', $availableFields)) {
            $date = $fieldset->addField(
                'date',
                \Ziffity\Pickupdate\Block\Adminhtml\Sales\Order\Renderer\Date::class,
                [
                    'label' => __('Pickup Date'),
                    'name' => 'pickupdate[date]',
                    'input_format' => \Magento\Framework\Stdlib\DateTime::DATE_INTERNAL_FORMAT,
                    'style' => 'width: 40%',
                    'format' => $this->pickupHelper->getDefaultScopeValue('date_field/format'),
                    'required' => false,
                    'date_format' => $this->pickupHelper->getDefaultScopeValue('date_field/format'),
                    'min_date' => $this->date->date($this->pickupHelper->getPhpFormat()),
                    'value' => $this->configProvider->getDefaultPickupDate()
                ]
            );
        }

        if ($this->scopeConfig->getValue('pickupdate/time_field/enabled_time')
            && in_array('time', $availableFields)) {
            $options = $this->pickupHelper->getTIntervals($storeId);
            $fieldset->addField(
                'tinterval_id',
                'select',
                [
                    'label' => __('Pickup Time Interval'),
                    'name' => 'pickupdate[tinterval_id]',
                    'style' => 'width: 40%',
                    'required' => false,
                    'value' => $this->configProvider->getDefaultPickupTime(),
                    'options' => $options,
                ]
            );
        }

        if ($this->scopeConfig->getValue('pickupdate/comment_field/enabled_comment')
            && in_array('comment', $availableFields)) {
            $fieldset->addField(
                'comment',
                'textarea',
                [
                    'label' => __('Pickup Comments'),
                    'title' => __('Pickup Comments'),
                    'name' => 'pickupdate[comment]',
                    'required' => false,
                    'style' => 'width: 40%',
                ]
            );
        }

        if ($pickupDate = $this->coreRegistry->registry('current_pickupdate')) {
            $data = $pickupDate->getData();
            if (isset($data['date']) && '0000-00-00' == $data['date']) {
                $data['date'] = '';
            }
            $form->setValues($data);
        }

        return $form->getElements();
    }
}