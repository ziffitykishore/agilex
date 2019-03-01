<?php
namespace SomethingDigital\ProductAttributeFields\Plugin\Block\Adminhtml\Product\Attribute\Edit\Tab;

class Front
{

    /**
     * @var Yesno
     */
    protected $yesNo;

    /**
     * Core registry
     *
     * @var \Magento\Framework\Registry
     */
    protected $coreRegistry;

    /**
     * @param Magento\Config\Model\Config\Source\Yesno $yesNo
     */
    public function __construct(
        \Magento\Config\Model\Config\Source\Yesno $yesNo,
        \Magento\Framework\Registry $registry
    ) {
        $this->yesNo = $yesNo;
        $this->coreRegistry = $registry;
    }

    /**
     * Get form HTML
     *
     * @return string
     */
    public function aroundGetFormHtml(
        \Magento\Catalog\Block\Adminhtml\Product\Attribute\Edit\Tab\Front $subject,
        \Closure $proceed
    ) {
        /** @var Attribute $attributeObject */
        $attributeObject = $this->coreRegistry->registry('entity_attribute');

        $yesnoSource = $this->yesNo->toOptionArray();
        $form = $subject->getForm();
        $fieldset = $form->getElement('front_fieldset');


        $fieldset->addField(
            'include_in_table',
            'select',
            [
                'name' => 'include_in_table',
                'label' => __('Include In Table'),
                'title' => __('Include In Table'),
                'note' => __('Depends on design theme.'),
                'values' => $yesnoSource
            ]
        );
        $fieldset->addField(
            'table_position',
            'text',
            [
                'name' => 'table_position',
                'label' => __('Table Position'),
                'title' => __('Table Position'),
                'note' => __('Depends on design theme.'),
                'class' => 'validate-number'
            ]
        );
        $fieldset->addField(
            'include_in_flyout',
            'select',
            [
                'name' => 'include_in_flyout',
                'label' => __('Include In Flyout'),
                'title' => __('Include In Flyout'),
                'note' => __('Depends on design theme.'),
                'values' => $yesnoSource
            ]
        );
        $fieldset->addField(
            'flyout_position',
            'text',
            [
                'name' => 'flyout_position',
                'label' => __('Flyout Position'),
                'title' => __('Flyout Position'),
                'note' => __('Depends on design theme.'),
                'class' => 'validate-number'
            ]
        );
        $fieldset->addField(
            'searchable_in_layered_nav',
            'select',
            [
                'name' => 'searchable_in_layered_nav',
                'label' => __('Searchable in Layered Nav'),
                'title' => __('Searchable in Layered Nav'),
                'note' => __('Depends on design theme.'),
                'values' => $yesnoSource
            ]
        );
        $fieldset->addField(
            'layered_nav_description',
            'textarea',
            [
                'name' => 'layered_nav_description',
                'label' => __('Layered Nav Description'),
                'title' => __('Layered Nav Description'),
                'note' => __('Depends on design theme.')
            ]
        );

        $form->setValues($attributeObject->getData());

        return $proceed();
    }
}