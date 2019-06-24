<?php

namespace Ziffity\Pickupdate\Ui\Component\Listing\Column;

class TypeDay implements \Magento\Framework\Data\OptionSourceInterface
{

    /**
     * @var \Ziffity\Pickupdate\Helper\Data
     */
    protected $helper;


    public function __construct(
        \Ziffity\Pickupdate\Helper\Data $helper
    )
    {
        $this->helper = $helper;

    }

    public function toOptionArray()
    {
        $typeDays = $this->helper->getTypeDay();
        $options = [];
        foreach ($typeDays as $value => $typeDay) {
            $options[] = ['value' => $value, 'label' => $typeDay];
        }

        return $options;
    }
}
