<?php

namespace Ziffity\StockStatus\Model;

use \Magento\Catalog\Model\Product;
use \Magento\ConfigurableProduct\Model\Product\Type\Configurable\Attribute;
use \Magento\ConfigurableProduct\Model\ConfigurableAttributeData as BaseConfigurableAttributeData;
use \Magento\Catalog\Api\ProductRepositoryInterface;

/**
 * Class ConfigurableAttributeData
 */
class ConfigurableAttributeData extends BaseConfigurableAttributeData
{
    /**
     * Product Repository
     *
     * @var \Magento\Catalog\Api\ProductRepositoryInterface
     */
    protected $catalogProduct = null;
    
    public function __construct(ProductRepositoryInterface $productRepository) {
        $this->productRepository = $productRepository;
    }

    /**
     * Get product attributes
     *
     * @param Product $product
     * @param array $options
     * @return array
     */
    public function getAttributesData(Product $product, array $options = [])
    {
        $defaultValues = [];
        $attributes = [];
        
        foreach ($product->getTypeInstance()->getConfigurableAttributes($product) as $attribute) {
            $attributeOptionsData = $this->getAttributeOptionsData($attribute, $options, $product);
            if ($attributeOptionsData) {
                $productAttribute = $attribute->getProductAttribute();
                $attributeId = $productAttribute->getId();

                $attributes[$attributeId] = [
                    'id' => $attributeId,
                    'code' => $productAttribute->getAttributeCode(),
                    'label' => $productAttribute->getStoreLabel($product->getStoreId()),
                    'options' => $attributeOptionsData,
                    'position' => $attribute->getPosition(),
                ];
                $defaultValues[$attributeId] = $this->getAttributeConfigValue($attributeId, $product);
            }
        }
        return [
            'attributes' => $attributes,
            'defaultValues' => $defaultValues,
        ];
    }

    /**
     * @param Attribute $attribute
     * @param array $config
     * @return array
     */
    protected function getAttributeOptionsData($attribute, $config)
    {

        $attributeOptionsData = [];
        
        foreach ($attribute->getOptions() as $attributeOption) {
            $appendText = '';
            $optionId = $attributeOption['value_index'];
            $productId = isset($config[$attribute->getAttributeId()][$optionId])
                    ? $config[$attribute->getAttributeId()][$optionId]
                    : [];
            $productStockId = count($productId) > 0 ? $productId[0]: '';
            if($productStockId) {
               $productStock = $this->customSalableData($productStockId);
               if ($productStock == 0) {
                   $appendText = ' (Out of Stock)';
               }
            }
            $attributeOptionsData[] = [
                'id' => $optionId,
                'label' => $attributeOption['label'] . $appendText,
                'products' => isset($config[$attribute->getAttributeId()][$optionId])
                    ? $config[$attribute->getAttributeId()][$optionId]
                    : [],
            ];
        }
        return $attributeOptionsData;
    }
    
    public function customSalableData($childProductId = null)
    {
        $productData = $this->productRepository->getById($childProductId);
        $productData->getItems();
        $data = $productData->getData();
        $productStock = count($productData) > 0 ? $productData['is_salable']: '';
        return $productStock;
    }

    /**
     * @param int $attributeId
     * @param Product $product
     * @return mixed|null
     */
    protected function getAttributeConfigValue($attributeId, $product)
    {
        return $product->hasPreconfiguredValues()
            ? $product->getPreconfiguredValues()->getData('super_attribute/' . $attributeId)
            : null;
    }
}
