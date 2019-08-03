<?php

namespace Wyomind\PointOfSale\Block\Adminhtml\Attributes\Edit;

class ResetButton extends GenericButton implements \Magento\Framework\View\Element\UiComponent\Control\ButtonProviderInterface
{
    /**
     * @return array
     */
    public function getButtonData()
    {
        $data = [];
        if ($this->canRender('reset')) {
            $data = [
                'label' => __('Reset'),
                'on_click' => 'location.reload();',
                'class' => 'reset',
                'sort_order' => 30
            ];
        }
        
        return $data;
    }
}