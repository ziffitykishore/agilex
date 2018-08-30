<?php
/**
 * SocialShare
 *
 * @package     Ulmod_SocialShare
 * @author      Ulmod <support@ulmod.com>
 * @copyright   Copyright (c) 2016 Ulmod (http://www.ulmod.com/)
 * @license     http://www.ulmod.com/license-agreement.html
 */
 
namespace Ulmod\SocialShare\Block\Adminhtml\System\Config;

use Magento\Framework\Data\Form\Element\AbstractElement;

class Color extends \Magento\Config\Block\System\Config\Form\Field
{
    protected function _getElementHtml(AbstractElement $element)
    {
        $html = $element->getElementHtml();
        $value = $element->getData('value');
        $imgPath = $this->getViewFileUrl('Ulmod_SocialShare::js/color.png');
        $html .= '<script type="text/javascript">
        require(["jquery","jquery/colorpicker/js/colorpicker"], function ($) {
            $(document).ready(function () {
                var $el = $("#' . $element->getHtmlId() . '");
                $el.css("backgroundColor", "'. $value .'");
                $el.ColorPicker({
                    color: "'. $value .'",
                    onChange: function (hsb, hex, rgb) {
                        $el.css("backgroundColor", "#" + hex).val("#" + hex);
                    }
                });
            });
        });
        </script>';
       $html .=  '<style type="text/css">
        #' . $element->getHtmlId() . ' { background-image: url('.$imgPath.') !important;
        background-position: calc(100% - 8px) center; 
        background-repeat: no-repeat; padding-right: 44px !important; }
        input.jscolor.disabled,input.jscolor[disabled] { pointer-events: none; }
        </style>';
        return $html;
    }
}
