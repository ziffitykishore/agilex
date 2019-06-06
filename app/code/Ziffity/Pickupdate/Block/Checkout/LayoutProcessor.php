<?php

namespace Ziffity\Pickupdate\Block\Checkout;

use Ziffity\Pickupdate\Helper\Data;
use Ziffity\Pickupdate\Model\PickupdateConfigProvider;
use Magento\Checkout\Block\Checkout\LayoutProcessorInterface;

class LayoutProcessor implements LayoutProcessorInterface
{
    /**
     * @var Data
     */
    protected $pickupHelper;

    /**
     * @var \Ziffity\Pickupdate\Model\ResourceModel\Tinterval\Collection
     */
    protected $tintervalCollection;

    /**
     * @var \Magento\Customer\Model\Session
     */
    protected $customerSession;

    /**
     * @var \Ziffity\Pickupdate\Model\PickupDate
     */
    protected $pickupDate;

    /**
     * @var PickupdateConfigProvider
     */
    private $configProvider;

    /**
     * @var \Ziffity\Pickupdate\Model\Tinterval
     */
    protected $tintervalModel;

    public function __construct(
        Data $pickupHelper,
        \Ziffity\Pickupdate\Model\ResourceModel\Tinterval\Collection $tintervalCollection,
        \Magento\Customer\Model\Session $customerSession,
        PickupdateConfigProvider $configProvider,
        \Ziffity\Pickupdate\Model\Pickupdate $pickupDate,
        \Ziffity\Pickupdate\Model\Tinterval $tintervalModel
    ) {
        $this->pickupHelper = $pickupHelper;
        $this->tintervalCollection = $tintervalCollection;
        $this->customerSession = $customerSession;
        $this->configProvider = $configProvider;
        $this->pickupDate = $pickupDate;
        $this->tintervalModel = $tintervalModel;
    }

    /**
     * @param array $jsLayout
     *
     * @return array
     */
    public function process($jsLayout)
    {
        if ($this->pickupHelper->moduleEnabled()) {
            $elements = [];
            if ($this->showByGroup('date_field')) {
                $elements['pickupdate_date'] = [
                    'component' => 'Ziffity_Pickupdate/js/checkout/date',
                    'label' => __('Pickup Date'),
                    'sortOrder' => 200,
                    'disabled' => false,
                    'validation' => [
                        'validate-date' => true
                    ],
                    'additionalClasses' => 'date',
                    'dataScope' => 'shippingAddress.pickupdate_date',
                    'provider' => 'checkoutProvider',
                    'notice' => $this->pickupHelper->getStoreScopeValue('date_field/note'),
                    'visible' => !$this->pickupHelper->getStoreScopeValue('date_field/enabled_carriers'),
                    'config' => [
                        'template' => 'ui/form/field',
                        'id' => 'pickup-date',
                        'options' => [
                            'showOn' => 'both',
                            'pickupconf' => $this->configProvider->getPickupDateFieldConfig()
                        ],
                        'readonly' => 1,
                        'pickerDefaultDateFormat' => $this->configProvider->getPickerDateFormat(),
                        'pickerDateTimeFormat' => $this->configProvider->getPickerDateFormat(),
                        'outputDateFormat' => PickupdateConfigProvider::OUTPUT_DATE_FORMAT,
                        'inputDateFormat' => $this->configProvider->getInputDateFormat(),
                        'default' => $this->configProvider->getDefaultPickupDate(),
                        'currentDate' => $this->pickupDate->getCurrentStoreDate()
                    ]
                ];
                if ((int)$this->pickupHelper->getStoreScopeValue('date_field/required') == 1) {
                    $elements['pickupdate_date']['validation']['required-entry'] = true;
                }
            }

            if ($this->pickupHelper->getStoreScopeValue('time_field/enabled_time')
                && $this->showByGroup('time_field')
            ) {
                $optionsForOtherDays = $this->tintervalCollection->toOptionArray();
                $optionsForCurrentDay = $this->tintervalCollection->clear()->getValidTinterval()->toOptionArray();
                $optionsForCurrentDay = $this->tintervalModel->restrictCurrentTinterval($optionsForCurrentDay);

                if (!empty($optionsForOtherDays)) {
                    $elements['pickupdate_time'] = [
                        'component' => 'Ziffity_Pickupdate/js/checkout/select',
                        'label' => __('Pickup Time Interval'),
                        'sortOrder' => 201,
                        'disabled' => false,
                        'validation' => [
                            'required-entry' => (bool)$this->pickupHelper
                                ->getStoreScopeValue('time_field/required')
                        ],
                        'dataScope' => 'shippingAddress.pickupdate_time',
                        'notice' => $this->pickupHelper->getStoreScopeValue('time_field/note'),
                        'visible' => !$this->pickupHelper->getStoreScopeValue('time_field/enabled_carriers'),
                        'provider' => 'checkoutProvider',
                        'config' => [
                            'template' => 'ui/form/field',
                            'caption' => __('Please select time interval.'),
                            'options' => [
                                'full' => $optionsForOtherDays,
                                'abridge' => $optionsForCurrentDay
                            ]
                        ]
                    ];
                }
            }

            if ($this->pickupHelper->getStoreScopeValue('comment_field/enabled_comment')
                && $this->showByGroup('comment_field')
            ) {
                $validation = [
                    'required-entry' => (bool)$this->pickupHelper->getStoreScopeValue('comment_field/required')
                ];
                if ($maxLength = (int)$this->pickupHelper->getStoreScopeValue('comment_field/maxlength')) {
                    $validation['max_text_length'] = $maxLength;
                }

                $elements['pickupdate_comment'] = [
                    'component' => 'Magento_Ui/js/form/element/textarea',
                    'label' => __('Message Card'),
                    'sortOrder' => 202,
                    'validation' => $validation,
                    'dataScope' => 'shippingAddress.pickupdate_comment',
                    'provider' => 'checkoutProvider',
                    'notice' => $this->pickupHelper->getStoreScopeValue('comment_field/note'),
                    'visible' => !$this->pickupHelper->getStoreScopeValue('comment_field/enabled_carriers'),
                    'config' => [
                        'template' => 'ui/form/field',
                        'cols' => 5,
                        'rows' => 5,
                        'elementTmpl' => 'ui/form/element/textarea',
                        'options' => []
                    ]
                ];
            }

            if (!empty($elements)) {
                if (isset(
                    $jsLayout['components']['checkout']['children']['steps']['children']['shipping-step']['children']
                    ['shippingAddress']['children']['shippingAdditional']['children']['ziffity-pickup-date']['children']
                )
                ) {
                    foreach ($elements as $key => $element) {
                        $jsLayout['components']['checkout']['children']['steps']['children']['shipping-step']['children']
                        ['shippingAddress']['children']['shippingAdditional']['children']['ziffity-pickup-date']['children'][$key] =
                            $element;
                    }
                }
            }
        }

        return $jsLayout;
    }

    /**
     * Is current Customer Group can view field
     *
     * @param string $field
     *
     * @return bool
     */
    protected function showByGroup($field)
    {
        if ($this->pickupHelper->getStoreScopeValue($field . '/enabled_customer_groups')) {
            $groupId = $this->customerSession->getCustomerGroupId();
            $groups = explode(',', $this->pickupHelper->getStoreScopeValue($field . '/customer_groups'));
            if (!in_array($groupId, $groups)) {
                return false;
            }
        }

        return true;
    }
}
