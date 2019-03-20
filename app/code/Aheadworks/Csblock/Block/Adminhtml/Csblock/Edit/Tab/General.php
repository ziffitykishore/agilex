<?php
/**
* Copyright 2016 aheadWorks. All rights reserved.
* See LICENSE.txt for license details.
*/

namespace Aheadworks\Csblock\Block\Adminhtml\Csblock\Edit\Tab;

use Aheadworks\Csblock\Model\Source\CustomerGroups;

/**
 * Class General
 * @package Aheadworks\Csblock\Block\Adminhtml\Csblock\Edit\Tab
 */
class General extends \Magento\Backend\Block\Widget\Form\Generic implements
    \Magento\Backend\Block\Widget\Tab\TabInterface
{
    /**
     * @var \Magento\Framework\Api\SearchCriteriaBuilder
     */
    protected $_searchCriteriaBuilder;

    /**
     * @var \Magento\Framework\Convert\DataObject
     */
    protected $_objectConverter;

    /**
     * @var \Magento\Store\Model\System\Store
     */
    protected $_systemStore;

    /**
     * @var \Magento\Rule\Block\Conditions
     */
    protected $_conditions;

    /**
     * @var \Magento\Backend\Block\Widget\Form\Renderer\FieldsetFactory
     */
    protected $_rendererFieldsetFactory;

    /**
     * @var CustomerGroups
     */
    private $customerGroupsSource;

    /**
     * @param \Magento\Backend\Block\Template\Context $context
     * @param \Magento\Framework\Registry $registry
     * @param \Magento\Framework\Data\FormFactory $formFactory
     * @param \Magento\Framework\Api\SearchCriteriaBuilder $searchCriteriaBuilder
     * @param \Magento\Store\Model\System\Store $systemStore
     * @param \Magento\Framework\Convert\DataObject $objectConverter
     * @param \Magento\Rule\Block\Conditions $conditions
     * @param \Magento\Backend\Block\Widget\Form\Renderer\FieldsetFactory $rendererFieldsetFactory
     * @param CustomerGroups $customerGroupsSource
     * @param array $data
     */
    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        \Magento\Framework\Registry $registry,
        \Magento\Framework\Data\FormFactory $formFactory,
        \Magento\Framework\Api\SearchCriteriaBuilder $searchCriteriaBuilder,
        \Magento\Store\Model\System\Store $systemStore,
        \Magento\Framework\Convert\DataObject $objectConverter,
        \Magento\Rule\Block\Conditions $conditions,
        \Magento\Backend\Block\Widget\Form\Renderer\FieldsetFactory $rendererFieldsetFactory,
        CustomerGroups $customerGroupsSource,
        array $data = []
    ) {
        $this->_systemStore = $systemStore;
        $this->_searchCriteriaBuilder = $searchCriteriaBuilder;
        $this->_objectConverter = $objectConverter;
        $this->_conditions = $conditions;
        $this->_rendererFieldsetFactory = $rendererFieldsetFactory;
        $this->customerGroupsSource = $customerGroupsSource;
        parent::__construct($context, $registry, $formFactory, $data);
    }

    /**
     * @return $this
     */
    protected function _prepareForm()
    {
        /* @var $model \Aheadworks\Csblock\Model\Csblock */
        $model = $this->_coreRegistry->registry('aw_csblock_model');

        /** @var \Magento\Framework\Data\Form $form */
        $form = $this->_formFactory->create();
        $form->setHtmlIdPrefix('csblock_');

        $fieldset = $form->addFieldset(
            'general_fieldset',
            [
                'legend' => __('General Information'),
            ]
        );

        if ($model->getId()) {
            $fieldset->addField('id', 'hidden', ['name' => 'id']);
        }

        $fieldset->addField(
            'name',
            'text',
            [
                'name' => 'name',
                'label' => __('Name'),
                'title' => __('Name'),
                'required' => true
            ]
        );

        $fieldset->addField(
            'status',
            'select',
            [
                'name' => 'status',
                'label' => __('Status'),
                'title' => __('Status'),
                'options' => [1 => __('Enabled'), 0 => __('Disabled')]
            ]
        );

        $fieldset->addField(
            'customer_groups',
            'multiselect',
            [
                'name' => 'customer_groups',
                'label' => __('Customer Groups'),
                'title' => __('Customer Groups'),
                'required' => true,
                'values' => $this->customerGroupsSource->toOptionArray(),
            ]
        );

        $fieldset = $form->addFieldset(
            'position_fieldset',
            [
                'legend' => __('Position'),
            ]
        );

        if (null === $model->getData('page_type')) {
            $model->setData('page_type', \Aheadworks\Csblock\Model\Source\PageType::DEFAULT_VALUE);
        }

        $pageSource = \Magento\Framework\App\ObjectManager::getInstance()
            ->create(\Aheadworks\Csblock\Model\Source\PageType::class);
        $fieldset->addField(
            'page_type',
            'select',
            [
                'name' => 'page_type',
                'label' => __('Where to Display'),
                'title' => __('Where to Display'),
                'options' => $pageSource->getOptionArray()
            ]
        );

        if (null === $model->getData('position')) {
            $model->setData('position', \Aheadworks\Csblock\Model\Source\Position::DEFAULT_VALUE);
        }
        $positionSource = \Magento\Framework\App\ObjectManager::getInstance()
            ->create(\Aheadworks\Csblock\Model\Source\Position::class);
        $fieldset->addField(
            'position',
            'select',
            [
                'name' => 'position',
                'label' => __('Position'),
                'title' => __('Position'),
                'options' => $positionSource->getOptionArray()
            ]
        );

        /* PRODUCT SECTION */

        $fieldset = $form->addFieldset(
            'conditions_fieldset',
            [
                'legend' => __(''),
                'comment' => __('Please specify products where the block should be displayed. '
                    . 'Leave blank to display the block on all product pages.')
            ]
        )->setRenderer(
            $this->_rendererFieldsetFactory->create()
                ->setTemplate('Magento_CatalogRule::promo/fieldset.phtml')
                ->setNewChildUrl(
                    $this->getUrl(
                        '*/*/newConditionHtml',
                        [
                            'form'   => $form->getHtmlIdPrefix().'conditions_fieldset',
                            'prefix' => 'csblock',
                            'rule'   => base64_encode(\Aheadworks\Csblock\Model\Rule\Product::class)
                        ]
                    )
                )
        );

        $model
            ->getRuleModel()
            ->getConditions()
            ->setJsFormObject($form->getHtmlIdPrefix() . 'conditions_fieldset');

        $fieldset
            ->addField(
                'csblock_conditions',
                'text',
                [
                    'name' => 'csblock_conditions',
                    'label' => __('Conditions'),
                    'title' => __('Conditions'),
                ]
            )
            ->setRule($model->getRuleModel())
            ->setRenderer($this->_conditions)
        ;

        /* END PRODUCT SECTION */

        /* CATALOG SECTION */

        $block = $this->getLayout()->createBlock(
            \Magento\Catalog\Block\Adminhtml\Category\Checkboxes\Tree::class,
            null,
            ['data' => ['js_form_object' => "awCsblockCategoryIds"]]
        )->setCategoryIds(explode(',', $model->getCategoryIds()));

        $fieldset = $form->addFieldset(
            'category_fieldset',
            [
                'legend' => __(''),
                'comment' => __('Please specify categories where the block should be displayed.'
                    . 'Leave blank to display the block on all category pages.')
            ]
        );
        $categoryTitle = __('Category');
        $fieldset
            ->addField(
                'category_ids',
                'hidden',
                [
                    'name' => 'category_ids',
                    'label' => __('Category'),
                    'title' => __('Category'),
                    'after_element_js' =>
                        "<script type='text/javascript'>
                            awCsblockCategoryIds = {updateElement : {value : '', linkedValue : ''}};
                            Object.defineProperty(awCsblockCategoryIds.updateElement, 'value', {
                                get: function() { return awCsblockCategoryIds.updateElement.linkedValue},
                                set: function(v) {awCsblockCategoryIds.updateElement.linkedValue = v;
                                    jQuery('#csblock_category_ids').val(v)}
                            });
                        </script>"
                        ."<label class='label admin__field-label'><span>
                         {$categoryTitle}
                        </span>
                        </label>"
                        . $block->toHtml()

                ]
            );

        /* END CATALOG SECTION */

        /* add field dependences */
        $categoryType = \Aheadworks\Csblock\Model\Source\PageType::CATEGORY_PAGE;
        $productType = \Aheadworks\Csblock\Model\Source\PageType::PRODUCT_PAGE;
        $homepageType = \Aheadworks\Csblock\Model\Source\PageType::HOME_PAGE;
        $shoppingCartType = \Aheadworks\Csblock\Model\Source\PageType::SHOPPINGCART_PAGE;

        $fieldset
            ->addField(
                'dependences',
                'note',
                [
                    'name' => 'dependences',
                    'label' => __(''),
                    'title' => __(''),
                    'after_element_html' => "
                        <script type='text/javascript'>
                            require(['jquery', 'awCsblockManagerFieldset'], function($){
                                $.awCsblockManagerFieldset.addDependence('#{$form->getHtmlIdPrefix()}'
                                +'category_fieldset', '#{$form->getHtmlIdPrefix()}'+'page_type', ['{$categoryType}']);
                                $.awCsblockManagerFieldset.addDependence('#{$form->getHtmlIdPrefix()}'
                                +'conditions_fieldset', '#{$form->getHtmlIdPrefix()}'+'page_type', ['{$productType}']);
                                $.awCsblockManagerFieldset.addDependence(
                                    '.field-position',
                                    '#{$form->getHtmlIdPrefix()}'+'page_type',
                                    ['{$categoryType}', '{$productType}', '{$homepageType}', '{$shoppingCartType}']
                                );
                            });
                        </script>"
                ]
            );

        $form->setValues($model->getData());
        $this->setForm($form);

        return parent::_prepareForm();
    }

    public function initForm()
    {
        $this->_prepareForm();
        return $this;
    }

    /**
     * Prepare label for tab
     *
     * @return \Magento\Framework\Phrase
     */
    public function getTabLabel()
    {
        return __('General');
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
     * {@inheritdoc}
     */
    public function canShowTab()
    {
        return true;
    }

    /**
     * {@inheritdoc}
     */
    public function isHidden()
    {
        return false;
    }
}
