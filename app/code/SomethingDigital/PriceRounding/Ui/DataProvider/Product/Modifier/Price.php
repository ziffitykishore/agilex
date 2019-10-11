<?php
namespace SomethingDigital\PriceRounding\Ui\DataProvider\Product\Modifier;

use Magento\Catalog\Ui\DataProvider\Product\Form\Modifier\AbstractModifier;
use Magento\Catalog\Model\Locator\LocatorInterface;
use Magento\Framework\App\Request\DataPersistorInterface;

class Price extends AbstractModifier
{
    /**
     * @var LocatorInterface
     */
    protected $locator;

    /**
     * @var DataPersistorInterface
     */
    protected $dataPersistor;

    public function __construct(
        LocatorInterface $locator,
        DataPersistorInterface $dataPersistor
    ) {
        $this->locator = $locator;
        $this->dataPersistor = $dataPersistor;
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
    
     /**
     * Resolve data persistence
     *
     * @param array $data
     * @return array
     */
    private function resolvePersistentData(array $data)
    {
        $persistentData = (array)$this->dataPersistor->get('catalog_product');
        $this->dataPersistor->clear('catalog_product');
        $productId = $this->locator->getProduct()->getId();

        if (empty($data[$productId][self::DATA_SOURCE_DEFAULT])) {
            $data[$productId][self::DATA_SOURCE_DEFAULT] = [];
        }

        $data[$productId] = array_replace_recursive(
            $data[$productId][self::DATA_SOURCE_DEFAULT],
            $persistentData
        );

        return $data;
    }
}