<?php

namespace Ziffity\Pickupdate\Ui\Component\Listing\Column;

class Store implements \Magento\Framework\Data\OptionSourceInterface
{
    protected $options;
    protected $store;

    public function __construct(
        \Magento\Store\Model\ResourceModel\Store\Collection $store
    )
    {
        $this->store = $store;
    }

    public function toOptionArray()
    {
        if ($this->options === null) {
            $this->options = $this->store->toOptionArray();
        }

        $this->options[] = array(
            'value' => 0,
            'label' => __('All Store Views')
        );

        return $this->options;
    }
}
