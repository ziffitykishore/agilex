<?php

namespace PartySupplies\ConfigurableProduct\Plugin;

use Magento\Catalog\Model\ProductRepository;
use Magento\Framework\Phrase;

class Attributes
{

    /**
     * @var ProductRepository
     */
    protected $productRepository;

    /**
     * @param ProductRepository $productRepository
     */
    public function __construct(ProductRepository $productRepository)
    {
        $this->productRepository = $productRepository;
    }

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
            $parentProduct = $this->productRepository->get($product->getSku());
            $firstChildProduct = $parentProduct->getTypeInstance()
                ->getUsedProductCollection($parentProduct)
                ->addAttributeToSort('price', 'ASC')
                ->addAttributeToSort('entity_id', 'ASC')
                ->getFirstItem();

            $firstChildProduct = $this->productRepository->get($firstChildProduct->getSku());

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
                                'value' => $this->getAttributeValue($firstChildProduct, $attribute->getName()),
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
     * To get custom attribute value
     *
     * @param ProductRepository $product
     * @param string $attributeName
     * @return string
     */
    protected function getAttributeValue($product, $attributeName)
    {
        if ($product->getCustomAttribute($attributeName)) {
            return $product->getCustomAttribute($attributeName)->getValue();
        }
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
