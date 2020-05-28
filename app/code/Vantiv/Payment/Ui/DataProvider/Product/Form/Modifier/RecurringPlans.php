<?php
/**
 * Copyright © 2018 Vantiv, LLC. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace Vantiv\Payment\Ui\DataProvider\Product\Form\Modifier;

use Magento\Catalog\Model\Locator\LocatorInterface;
use Magento\Catalog\Ui\DataProvider\Product\Form\Modifier\AbstractModifier;
use Magento\Framework\Stdlib\ArrayManager;
use Magento\Ui\Component\Container;
use Magento\Ui\Component\DynamicRows;
use Magento\Ui\Component\Form;
use Vantiv\Payment\Model\Recurring\Source\Website;
use Magento\Ui\Component\Modal;
use Magento\Framework\UrlInterface;
use Vantiv\Payment\Helper\Recurring as RecurringHelper;

class RecurringPlans extends AbstractModifier
{
    const CODE_RECURRING_DATA = 'subscriptions';
    const CODE_RECURRING_PLANS = 'vantiv_recurring_plans';
    const CODE_PLANS = 'plans';
    const CODE_ADD_PLAN_MODAL = 'vantiv_add_recurring_plan_modal';

    /**
     * @var LocatorInterface
     */
    private $locator;

    /**
     * @var ArrayManager
     */
    private $arrayManager;

    /**
     * @var Website
     */
    private $websiteSource;

    /**
     * @var Data\RecurringPlans
     */
    private $plansData;

    /**
     * @var UrlInterface
     */
    private $urlBuilder;

    /**
     * @var RecurringHelper
     */
    private $recurringHelper;

    /**
     * @param LocatorInterface $locator
     * @param ArrayManager $arrayManager
     * @param Website $websiteSource
     * @param Data\RecurringPlans $plansData
     * @param UrlInterface $urlBuilder
     * @param RecurringHelper $recurringHelper
     */
    public function __construct(
        LocatorInterface $locator,
        ArrayManager $arrayManager,
        Website $websiteSource,
        Data\RecurringPlans $plansData,
        UrlInterface $urlBuilder,
        RecurringHelper $recurringHelper
    ) {
        $this->locator = $locator;
        $this->arrayManager = $arrayManager;
        $this->websiteSource = $websiteSource;
        $this->plansData = $plansData;
        $this->urlBuilder = $urlBuilder;
        $this->recurringHelper = $recurringHelper;
    }

    /**
     * {@inheritdoc}
     */
    public function modifyMeta(array $meta)
    {
        if (!$this->locator->getProduct()->getId()
            || !in_array($this->locator->getProduct()->getTypeId(), $this->recurringHelper->getAllowedProductTypeIds())
        ) {
            return $meta;
        }

        $path = $this->arrayManager->findPath(static::CODE_RECURRING_DATA, $meta, null, 'children');

        $meta = $this->moveDataScopeToChildren($meta, $path);

        $meta = $this->arrayManager->merge(
            $path,
            $meta,
            [
                'children' => [
                    self::CODE_RECURRING_PLANS => [
                        'arguments' => [
                            'data' => [
                                'config' => [
                                    'componentType' => Form\Fieldset::NAME,
                                    'additionalClasses' => 'admin__fieldset-section',
                                    'label' => __('Payment Plans'),
                                    'dataScope' => 'data.' . self::CODE_RECURRING_PLANS,
                                ]
                            ]
                        ],
                        'children' => [
                            self::CODE_PLANS => $this->getPlansGrid(),
                            'add_button' => $this->getAddPlanButton()
                        ]
                    ],
                    self::CODE_ADD_PLAN_MODAL => $this->getAddPlanModal()
                ]
            ]
        );

        return $meta;
    }

    /**
     * {@inheritdoc}
     */
    public function modifyData(array $data)
    {
        $productId = $this->locator->getProduct()->getId();
        if (!$productId) {
            return $data;
        }

        $data[$productId][self::CODE_RECURRING_PLANS][self::CODE_PLANS] = $this->plansData->getPlansData();

        return $data;
    }

    /**
     * Move dataScope data to children
     *
     * @param array $meta
     * @param $path
     * @return array
     */
    private function moveDataScopeToChildren(array $meta, $path)
    {
        $pathMeta = $this->arrayManager->get($path, $meta);

        if (isset($pathMeta['arguments']['data']['config']['dataScope'])
            && $pathMeta['arguments']['data']['config']['dataScope']
        ) {
            $parentDataScope = $pathMeta['arguments']['data']['config']['dataScope'];
            $metaUpdate = [
                'arguments' => [
                    'data' => [
                        'config' => [
                            'dataScope' => ''
                        ]
                    ]
                ]
            ];

            foreach ($pathMeta['children'] as $childKey => $child) {
                $childComponentType = isset($child['arguments']['data']['config']['componentType'])
                    ? $child['arguments']['data']['config']['componentType'] : '';
                if ($childComponentType == Container::NAME) {
                    foreach ($pathMeta['children'][$childKey]['children'] as $containerChildKey => $containerChild) {
                        $containerChildDataScope = isset($child['arguments']['data']['config']['dataScope'])
                            ? $child['arguments']['data']['config']['dataScope'] : '';
                        $metaUpdate['children'][$childKey]['children'][$containerChildKey] = [
                            'arguments' => [
                                'data' => [
                                    'config' => [
                                        'dataScope' => ($parentDataScope ? $parentDataScope . '.' : '')
                                            . ($containerChildDataScope ? $containerChildDataScope : $containerChildKey)
                                    ]
                                ]
                            ]
                        ];
                    }
                } else {
                    $childDataScope = isset($child['arguments']['data']['config']['dataScope'])
                        ? $child['arguments']['data']['config']['dataScope'] : '';
                    $metaUpdate['children'][$childKey] = [
                        'arguments' => [
                            'data' => [
                                'config' => [
                                    'dataScope' => ($parentDataScope ? $parentDataScope . '.' : '')
                                        . ($childDataScope ? $childDataScope : $childKey)
                                ]
                            ]
                        ]
                    ];
                }
            }

            $meta = $this->arrayManager->merge($path, $meta, $metaUpdate);
        }
        return $meta;
    }

    /**
     * Get plans grid structure
     *
     * @return array
     */
    private function getPlansGrid()
    {
        return [
            'arguments' => [
                'data' => [
                    'config' => [
                        'addButton' => false,
                        'componentType' => DynamicRows::NAME,
                        'itemTemplate' => 'record',
                        'renderDefaultRecord' => false,
                        'columnsHeader' => false,
                        'columnsHeaderAfterRender' => true,
                        'additionalClasses' => 'admin__field-wide',
                        'dataScope' => '',
                        'pageSize' => 10000
                    ]
                ]
            ],
            'children' => [
                'record' => [
                    'arguments' => [
                        'data' => [
                            'config' => [
                                'componentType' => Container::NAME,
                                'isTemplate' => true,
                                'is_collection' => true,
                                'component' => 'Magento_Ui/js/dynamic-rows/record',
                                'dataScope' => '',
                            ]
                        ]
                    ],
                    'children' => [
                        'code' => [
                            'arguments' => [
                                'data' => [
                                    'config' => [
                                        'componentType' => Form\Field::NAME,
                                        'dataType' => Form\Element\DataType\Text::NAME,
                                        'formElement' => Form\Element\Input::NAME,
                                        'elementTmpl' => 'ui/dynamic-rows/cells/text',
                                        'label' => __('Code'),
                                        'dataScope' => 'code',
                                    ]
                                ]
                            ]
                        ],
                        'name' => [
                            'arguments' => [
                                'data' => [
                                    'config' => [
                                        'componentType' => Form\Field::NAME,
                                        'dataType' => Form\Element\DataType\Text::NAME,
                                        'formElement' => Form\Element\Input::NAME,
                                        'elementTmpl' => 'ui/dynamic-rows/cells/text',
                                        'label' => __('Name'),
                                        'dataScope' => 'name',
                                    ]
                                ]
                            ]
                        ],
                        'description' => [
                            'arguments' => [
                                'data' => [
                                    'config' => [
                                        'componentType' => Form\Field::NAME,
                                        'dataType' => Form\Element\DataType\Text::NAME,
                                        'formElement' => Form\Element\Input::NAME,
                                        'elementTmpl' => 'ui/dynamic-rows/cells/text',
                                        'label' => __('Description'),
                                        'dataScope' => 'description',
                                    ]
                                ]
                            ]
                        ],
                        'number_of_payments' => [
                            'arguments' => [
                                'data' => [
                                    'config' => [
                                        'componentType' => Form\Field::NAME,
                                        'dataType' => Form\Element\DataType\Text::NAME,
                                        'formElement' => Form\Element\Input::NAME,
                                        'elementTmpl' => 'ui/dynamic-rows/cells/text',
                                        'label' => __('# of Payments'),
                                        'dataScope' => 'number_of_payments',
                                    ]
                                ]
                            ]
                        ],
                        'interval' => [
                            'arguments' => [
                                'data' => [
                                    'config' => [
                                        'componentType' => Form\Field::NAME,
                                        'dataType' => Form\Element\DataType\Text::NAME,
                                        'formElement' => Form\Element\Input::NAME,
                                        'elementTmpl' => 'ui/dynamic-rows/cells/text',
                                        'label' => __('Interval'),
                                        'dataScope' => 'interval',
                                    ]
                                ]
                            ]
                        ],
                        'interval_amount' => [
                            'arguments' => [
                                'data' => [
                                    'config' => [
                                        'componentType' => Form\Field::NAME,
                                        'dataType' => Form\Element\DataType\Text::NAME,
                                        'formElement' => Form\Element\Input::NAME,
                                        'elementTmpl' => 'ui/dynamic-rows/cells/text',
                                        'label' => __('Amount'),
                                        'dataScope' => 'interval_amount',
                                    ]
                                ]
                            ]
                        ],
                        'trial_interval' => [
                            'arguments' => [
                                'data' => [
                                    'config' => [
                                        'componentType' => Form\Field::NAME,
                                        'dataType' => Form\Element\DataType\Text::NAME,
                                        'formElement' => Form\Element\Input::NAME,
                                        'elementTmpl' => 'ui/dynamic-rows/cells/text',
                                        'label' => __('Trial Interval'),
                                        'dataScope' => 'trial_interval',
                                    ]
                                ]
                            ]
                        ],
                        'number_of_trial_intervals' => [
                            'arguments' => [
                                'data' => [
                                    'config' => [
                                        'componentType' => Form\Field::NAME,
                                        'dataType' => Form\Element\DataType\Text::NAME,
                                        'formElement' => Form\Element\Input::NAME,
                                        'elementTmpl' => 'ui/dynamic-rows/cells/text',
                                        'label' => __('# of Trial Intervals'),
                                        'dataScope' => 'number_of_trial_intervals',
                                    ]
                                ]
                            ]
                        ],
                        'position' => [
                            'arguments' => [
                                'data' => [
                                    'config' => [
                                        'componentType' => Form\Field::NAME,
                                        'formElement' => Form\Element\Input::NAME,
                                        'dataType' => Form\Element\DataType\Number::NAME,
                                        'dataScope' => 'sort_order',
                                        'visible' => false,
                                    ]
                                ]
                            ]
                        ],
                        'website' => [
                            'arguments' => [
                                'data' => [
                                    'config' => [
                                        'label' => __('Website'),
                                        'formElement' => Form\Element\Select::NAME,
                                        'componentType' => Form\Field::NAME,
                                        'dataType' => Form\Element\DataType\Number::NAME,
                                        'dataScope' => 'website_id',
                                        'options' => $this->websiteSource->toOptionArray(),
                                    ]
                                ]
                            ]
                        ],
                        'active' => [
                            'arguments' => [
                                'data' => [
                                    'config' => [
                                        'label' => __('Active'),
                                        'formElement' => Form\Element\Checkbox::NAME,
                                        'componentType' => Form\Field::NAME,
                                        'dataType' => Form\Element\DataType\Number::NAME,
                                        'dataScope' => 'active',
                                        'valueMap' => [
                                            'false' => '0',
                                            'true' => '1'
                                        ]
                                    ]
                                ]
                            ]
                        ]
                    ]
                ]
            ]
        ];
    }

    /**
     * Get add new plan button structure
     *
     * @return array
     */
    private function getAddPlanButton()
    {
        return [
            'arguments' => [
                'data' => [
                    'config' => [
                        'title' => __('Add Payment Plan'),
                        'formElement' => Container::NAME,
                        'componentType' => Container::NAME,
                        'component' => 'Magento_Ui/js/form/components/button',
                        'actions' => [
                            [
                                'targetName' => 'product_form.product_form.'
                                    . self::CODE_RECURRING_DATA . '.' . self::CODE_ADD_PLAN_MODAL,
                                'actionName' => 'toggleModal',
                            ]
                        ]
                    ]
                ]
            ]
        ];
    }

    /**
     * Get add new plan modal structure
     *
     * @return array
     */
    private function getAddPlanModal()
    {
        $urlParams = ['product_id' => $this->locator->getProduct()->getId()];
        return [
            'arguments' => [
                'data' => [
                    'config' => [
                        'isTemplate' => false,
                        'componentType' => Modal::NAME,
                        'dataScope' => '',
                        'provider' => 'product_form.product_form_data_source',
                        'options' => [
                            'title' => __('Add Payment Plan'),
                        ],
                        'imports' => [
                            'state' => '!index=catalog_product_recurring_plan_add_form:responseStatus'
                        ],
                    ],
                ],
            ],
            'children' => [
                'vantiv_add_recurring_plan_form' => [
                    'arguments' => [
                        'data' => [
                            'config' => [
                                'label' => __('New Payment Plan'),
                                'componentType' => Container::NAME,
                                'component' => 'Vantiv_Payment/js/components/new-recurring-plan-insert-form',
                                'dataScope' => '',
                                'update_url' => $this->urlBuilder->getUrl('mui/index/render'),
                                'render_url' => $this->urlBuilder->getUrl(
                                    'mui/index/render_handle',
                                    [
                                        'handle' => 'catalog_product_recurring_plan_add_form',
                                        'buttons' => 1
                                    ]
                                ),
                                'autoRender' => true,
                                'ns' => 'catalog_product_recurring_plan_add_form',
                                'externalProvider' => '${ $.ns }.catalog_product_recurring_plan_add_form_data_source',
                                'toolbarContainer' => '${ $.parentName }',
                                'formSubmitType' => 'ajax',
                                'saveUrl' => $this->urlBuilder->getUrl('vantiv/recurring_plan/save', $urlParams),
                                'validateUrl' => $this->urlBuilder->getUrl(
                                    'vantiv/recurring_plan/validate',
                                    $urlParams
                                ),
                                'exports' => [
                                    'saveUrl' => '${ $.externalProvider }:client.urls.save',
                                    'validateUrl' => '${ $.externalProvider }:client.urls.beforeSave'
                                ]
                            ]
                        ]
                    ]
                ]
            ]
        ];
    }
}
