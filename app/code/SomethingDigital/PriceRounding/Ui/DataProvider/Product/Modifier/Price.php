<?php
namespace SomethingDigital\PriceRounding\Ui\DataProvider\Product\Modifier;

use Magento\Catalog\Ui\DataProvider\Product\Form\Modifier\AbstractModifier;
use Magento\Catalog\Api\Data\ProductAttributeInterface;
use Magento\Catalog\Model\Locator\LocatorInterface;
use Magento\Store\Model\StoreManagerInterface;
use Magento\Framework\UrlInterface;
use Magento\Ui\Component\Form\Field;

class Price extends AbstractModifier
{

    public function __construct(
        LocatorInterface $locator        
    ) {
        $this->locator = $locator;        
    }

    public function modifyData(array $data)
    {
        if (!$this->locator->getProduct()->getId() && $this->dataPersistor->get('catalog_product')) {
            return $this->resolvePersistentData($data);
        }
        $productId = $this->locator->getProduct()->getId();
        $productPrice =  $this->locator->getProduct()->getPrice();
        $specialPrice =  $this->locator->getProduct()->getSpecialPrice();
        $data[$productId][self::DATA_SOURCE_DEFAULT]['price'] = number_format((float)$productPrice, 4, '.', ''); 
        $data[$productId][self::DATA_SOURCE_DEFAULT]['special_price'] = number_format((float)$specialPrice, 4, '.', ''); 
        return $data;
    }

    public function modifyMeta(array $meta)
    {    
        return $meta;
    }
}