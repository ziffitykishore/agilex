<?php

namespace Ziffity\Pickupdate\Ui\Component\Listing\Column;

class Year implements \Magento\Framework\Data\OptionSourceInterface
{
    /**
     * @var \Ziffity\Pickupdate\Model\ResourceModel\Holidays\Collection
     */
    protected $collection;

    /**
     * Year options constructor.
     *
     * @param \Ziffity\Pickupdate\Model\ResourceModel\Holidays\CollectionFactory $holidayCollectionFactory
     */
    public function __construct(
        \Ziffity\Pickupdate\Model\ResourceModel\Holidays\CollectionFactory $holidayCollectionFactory
    ) {
        $this->collection = $holidayCollectionFactory->create();
    }

    /**
     * @return array
     */
    public function toOptionArray()
    {
        return $this->collection->getYearsAsArray('year');
    }
}
