<?php

namespace Earthlite\NewTrending\Block\AdminHtml\Category\Widget;

use Magento\Backend\Block\Template\Context;
use Magento\Framework\Registry;
use Magento\Backend\Block\Template;
use Magento\Framework\Data\Form\Element\Factory;

/**
 * class DatePicker
 */
class DatePicker extends Template
{
    /**
     * @var  Registry
     */
    protected $_coreRegistry;

    /**
     * @param Context  $context
     * @param Registry $coreRegistry
     * @param array    $data
     */
    public function __construct(
        Context $context,
        Factory $elementFactory,
        Registry $coreRegistry,
        array $data = []
    ) {
        $this->_coreRegistry = $coreRegistry;
        $this->_elementFactory = $elementFactory;
        parent::__construct($context, $data);
    }

    /**
     * Prepare chooser element HTML
     *
     * @param \Magento\Framework\Data\Form\Element\AbstractElement $element Form Element
     * @return \Magento\Framework\Data\Form\Element\AbstractElement
     */
    public function prepareElementHtml(\Magento\Framework\Data\Form\Element\AbstractElement $element)
    {
        $htmlId = $element->getId();
        $data = $element->getData();
        $data['after_element_js'] = $this->_afterElementJs($element);
        $data['after_element_html'] = $this->_afterElementHtml($element);
        $htmlItem = $this->_elementFactory->create('text', ['data' => $data]);
        $htmlItem
                ->setId("{$htmlId}")
                ->setForm($element->getForm())
                ->addClass('required-entry')
                ->addClass('entities');
        $return = <<<HTML
                <div id="{$htmlId}-container" class="chooser_container">{$htmlItem->getElementHtml()}</div>
HTML;
        $element->setData('after_element_html', $return);
        return $element;
    }
    
    protected function _afterElementHtml($element)
    {
        $htmlId = $element->getId();
        $return = <<<HTML
            
HTML;
        return $return;
    }
    
 
    protected function _afterElementJs($element)
    {
        $chooserUrl = $this->getUrl('adminhtml/widget_instance/categories', ['is_anchor_only'=>1]);
        $htmlId     = '#'.$element->getId();
        $return = <<<HTML
            <script>
                    require([
    'jquery',
    'mage/mage',
    'mage/calendar'
], function($){
    $('{$htmlId}').datepicker({
        dateFormat: 'd-m-yy',
        changeMonth: true,
        changeYear: true
    });
});
</script>

HTML;
        return $return;
    }
}