<?php

namespace Ziffity\Pickupdate\Ui\Component\Listing\Column;

class FromYear implements \Magento\Framework\Data\OptionSourceInterface
{
    /**
     * @var \Ziffity\Pickupdate\Model\ResourceModel\Dinterval\Collection
     */
    protected $collection;

    /**
     * FromYear constructor.
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
        return $this->collection->getYearsAsArray();
    }
}
