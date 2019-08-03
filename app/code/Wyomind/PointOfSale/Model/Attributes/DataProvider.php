<?php

namespace Wyomind\PointOfSale\Model\Attributes;

class DataProvider extends \Magento\Ui\DataProvider\AbstractDataProvider
{

    protected $collection;

    /**
     * @var \Magento\Framework\App\Request\DataPersistorInterface
     */
    protected $_dataPersistor;

    /**
     * @var array
     */
    protected $_loadedData;

    /**
     * Attributes constructor.
     * @param string $name
     * @param string $primaryFieldName
     * @param string $requestFieldName
     * @param \Wyomind\PointOfSale\Model\ResourceModel\Attributes\CollectionFactory $collectionFactory
     * @param \Magento\Framework\App\Request\DataPersistorInterface $dataPersistor
     * @param array $meta
     * @param array $data
     */
    public function __construct(
        $name,
        $primaryFieldName,
        $requestFieldName,
        \Wyomind\PointOfSale\Model\ResourceModel\Attributes\CollectionFactory $collectionFactory,
        \Magento\Framework\App\Request\DataPersistorInterface $dataPersistor,
        array $meta = [],
        array $data = []

    )
    {
        $this->collection = $collectionFactory->create();
        $this->_dataPersistor = $dataPersistor;
        parent::__construct($name, $primaryFieldName, $requestFieldName, $meta, $data);
    }

    /**
     * Get data
     *
     * @return array
     */
    public function getData()
    {
        if (isset($this->_loadedData)) {
            return $this->_loadedData;
        }

        $items = $this->collection->getItems();

        foreach ($items as $entity) {
            $this->_loadedData[$entity->getAttributeId()] = $entity->getData();
        }

        $data = $this->_dataPersistor->get('attribute');
        if (!empty($data)) {
            $entity = $this->collection->getNewEmptyItem();
            $entity->setData($data);
            $this->_loadedData[$entity->getAttributeId()] = $entity->getData();
            $this->_dataPersistor->clear('attribute');
        }

        return $this->_loadedData;
    }
}