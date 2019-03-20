<?php
/**
* Copyright 2016 aheadWorks. All rights reserved.
* See LICENSE.txt for license details.
*/

namespace Aheadworks\Csblock\Model\Source;

use Magento\Framework\Data\OptionSourceInterface;

/**
 * Class PageType
 * @package Aheadworks\Csblock\Model\Source
 */
class PageType implements OptionSourceInterface
{
    const HOME_PAGE = 1;
    const PRODUCT_PAGE = 2;
    const CATEGORY_PAGE = 3;
    const SHOPPINGCART_PAGE = 4;
    const CHECKOUT_PAGE = 5;

    const HOME_PAGE_LABEL = "Home Page";
    const PRODUCT_PAGE_LABEL = "Product Pages";
    const CATEGORY_PAGE_LABEL = "Catalog Pages";
    const SHOPPINGCART_PAGE_LABEL = "Shopping Cart";
    const CHECKOUT_PAGE_LABEL = "Checkout";

    const DEFAULT_VALUE = 2;

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
            self::HOME_PAGE => self::HOME_PAGE_LABEL,
            self::PRODUCT_PAGE => self::PRODUCT_PAGE_LABEL,
            self::CATEGORY_PAGE => self::CATEGORY_PAGE_LABEL,
            self::SHOPPINGCART_PAGE => self::SHOPPINGCART_PAGE_LABEL,
            self::CHECKOUT_PAGE => self::CHECKOUT_PAGE_LABEL
        ];
    }
}
