<?php


namespace Ziffity\Pickupdate\Ui\Component\Listing\Column;

class Month implements \Magento\Framework\Data\OptionSourceInterface
{
    /**
     * @var \Ziffity\Pickupdate\Helper\Data
     */
    private $helper;

    /**
     * @var bool
     */
    private $eachMonthAvailable;

    /**
     * Month Options constructor.
     *
     * @param \Ziffity\Pickupdate\Helper\Data $helper
     * @param bool                            $eachMonthAvailable
     */
    public function __construct(
        \Ziffity\Pickupdate\Helper\Data $helper,
        $eachMonthAvailable = false
    ) {
        $this->helper = $helper;
        $this->eachMonthAvailable = $eachMonthAvailable;
    }

    public function toOptionArray()
    {
        $months = $this->helper->getMonths($this->eachMonthAvailable);
        $options = [];
        foreach ($months as $value => $month) {
            $options[] = ['value' => $value, 'label' => $month];
        }

        return $options;
    }
}
