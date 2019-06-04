<?php

namespace Ziffity\Pickupdate\Ui\Component\Listing\Column;

class ToYear implements \Magento\Framework\Data\OptionSourceInterface
{
    /**
     * @var \Ziffity\Pickupdate\Model\ResourceModel\Dinterval\Collection
     */
    protected $collection;

    /**
     * ToYear constructor.
     *
     * @param \Ziffity\Pickupdate\Model\ResourceModel\Dinterval\CollectionFactory $dIntervalCollectionFactory
     */
    public function __construct(
        \Ziffity\Pickupdate\Model\ResourceModel\Dinterval\CollectionFactory $dIntervalCollectionFactory
    ) {
        $this->collection = $dIntervalCollectionFactory->create();
    }

    /**
     * @return array
     */
    public function toOptionArray()
    {
        return $this->collection->getYearsAsArray('to_year');
    }
}
