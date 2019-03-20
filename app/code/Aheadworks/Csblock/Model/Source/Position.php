<?php
/**
* Copyright 2016 aheadWorks. All rights reserved.
* See LICENSE.txt for license details.
*/

namespace Aheadworks\Csblock\Model\Source;

use Magento\Framework\Data\OptionSourceInterface;

/**
 * Class Position
 * @package Aheadworks\Csblock\Model\Source
 */
class Position implements OptionSourceInterface
{

    /**
     * constants are defined for custom sorting. ASC sorting:
     * MENU TOP/BOTTOM
     * CONTENT TOP/BOTTOM
     * SIDEBAR TOP/BOTTOM
     */

    const SIDEBAR_TOP = 5;
    const SIDEBAR_BOTTOM = 6;
    const CONTENT_TOP = 3;
    const PAGE_BOTTOM = 4;
    const MENU_TOP = 1;
    const MENU_BOTTOM = 2;

    const SIDEBAR_TOP_LABEL = "Sidebar top";
    const SIDEBAR_BOTTOM_LABEL = "Sidebar bottom";
    const CONTENT_TOP_LABEL = "Content top";
    const PAGE_BOTTOM_LABEL = "Page bottom";
    const MENU_TOP_LABEL = "Menu Top";
    const MENU_BOTTOM_LABEL = "Menu Bottom";

    const DEFAULT_VALUE = 3;

    public function getOptionArray()
    {
        return $this->getTranslatedOptionArray();
    }

    public function toOptionArray()
    {
        $optionsArray = [];
        $options = $this->getTranslatedOptionArray();

        foreach ($options as $key => $value) {
            $optionsArray[] = ['value' => $key, 'label' => $value];
        }
        return $optionsArray;
    }

    public function getTranslatedOptionArray()
    {
        $translatedOptions = [];
        $untranslatedOptions = $this->getUntranslatedOptionArray();

        foreach ($untranslatedOptions as $key => $value) {
            $translatedOptions[$key] = __($value);
        }
        return $translatedOptions;
    }

    public function getUntranslatedOptionArray()
    {
        return [
            self::SIDEBAR_TOP => self::SIDEBAR_TOP_LABEL,
            self::SIDEBAR_BOTTOM => self::SIDEBAR_BOTTOM_LABEL,
            self::CONTENT_TOP => self::CONTENT_TOP_LABEL,
            self::PAGE_BOTTOM => self::PAGE_BOTTOM_LABEL,
            self::MENU_TOP => self::MENU_TOP_LABEL,
            self::MENU_BOTTOM => self::MENU_BOTTOM_LABEL
        ];
    }
}
