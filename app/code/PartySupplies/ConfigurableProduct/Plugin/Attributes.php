<?php

namespace PartySupplies\ConfigurableProduct\Plugin;

use Magento\Framework\Phrase;

class Attributes
{
    /**
     * To set default value for custom attributes.
     *
     * @param \Magento\Catalog\Block\Product\View\Attributes $subject
     * @param json $result
     * @return json
     */
    public function afterGetAdditionalData(
        \Magento\Catalog\Block\Product\View\Attributes $subject,
        $result
    ) {
        if (empty($result)) {
            $product = $subject->getProduct();

            if($product->getTypeId() === \Magento\ConfigurableProduct\Model\Product\Type\Configurable::TYPE_CODE) {
                $attributes = $product->getAttributes();
                foreach ($attributes as $attribute) {
                    if ($this->isVisibleOnFrontend($attribute, [])) {
                        $value = $attribute->getFrontend()->getValue($product);

                        if ($value instanceof Phrase) {
                            $value = (string)$value;
                        } elseif ($attribute->getFrontendInput() == 'price' && is_string($value)) {
                            $value = $this->priceCurrency->convertAndFormat($value);
                        }

                        if (is_string($value) && strlen($value)) {
                            $data[$attribute->getAttributeCode()] = [
                                'label' => __($attribute->getStoreLabel()),
                                'value' => $value,
                                'code' => $attribute->getAttributeCode(),
                            ];
                        } else {
                            $data[$attribute->getAttributeCode()] = [
                                'label' => __($attribute->getStoreLabel()),
                                'value' => __('notAvailable'),
                                'code' => $attribute->getAttributeCode(),
                            ];
                        }
                    }
                }
                $result = $data;
            }
        }
        return $result;
    }
 
    /**
     * To check whether visible on front-end property is enabled or not.
     *
     * @param \Magento\Eav\Model\Entity\Attribute\AbstractAttribute $attribute
     * @param array $excludeAttr
     * @return boolean
     */
    protected function isVisibleOnFrontend(
        \Magento\Eav\Model\Entity\Attribute\AbstractAttribute $attribute,
        array $excludeAttr
    ) {
        return ($attribute->getIsVisibleOnFront() && !in_array($attribute->getAttributeCode(), $excludeAttr));
    }
}
