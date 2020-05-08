<?php
declare(strict_types=1);
namespace Earthlite\Customer\Block\Adminhtml\Customer\Address\Attribute\Edit\Tab;

/**
 * class General
 */
class General extends \Magento\CustomerCustomAttributes\Block\Adminhtml\Customer\Address\Attribute\Edit\Tab\General
{
    /**
     * @inheritdoc
     */
    protected function _prepareForm()
    {
        parent::_prepareForm();
        $attribute = $this->getAttributeObject();
        $form = $this->getForm();
        $fieldset = $form->getElement('base_fieldset');
        $fieldset->removeField('multiline_count');
        $fieldset->addField(
            'multiline_count',
            'text',
            [
                'name' => 'multiline_count',
                'label' => __('Lines Count'),
                'title' => __('Lines Count'),
                'required' => true,
                'class' => 'validate-digits-range digits-range-1-20',
                'note' => __('Valid range 1-20')
            ],
            'frontend_input'
        );
        return $this;
    }
}
