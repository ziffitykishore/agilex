<?php
/**
 * Copyright (c) 2019. Lorem ipsum dolor sit amet, consectetur adipiscing elit.
 * Morbi non lorem porttitor neque feugiat blandit. Ut vitae ipsum eget quam lacinia accumsan.
 * Etiam sed turpis ac ipsum condimentum fringilla. Maecenas magna.
 * Proin dapibus sapien vel ante. Aliquam erat volutpat. Pellentesque sagittis ligula eget metus.
 * Vestibulum commodo. Ut rhoncus gravida arcu.
 */

/**
 * 6.2 Define form and grid widgets
 */
namespace Wyomind\PointOfSale\Ui\DataProvider;

class AttributesProvider extends \Magento\Ui\DataProvider\AbstractDataProvider
{
    protected $collection;
    
    /**
     * @var array 
     */
    protected $_loadedData;

    public function __construct(
        $name,
        $primaryFieldName,
        $requestFieldName,
        \Wyomind\PointOfSale\Model\ResourceModel\Attributes\CollectionFactory $collectionFactory,
        array $meta = [],
        array $data = []
            
    )
    {
        $this->collection = $collectionFactory->create();
        parent::__construct($name, $primaryFieldName, $requestFieldName, $meta, $data);
    }
}