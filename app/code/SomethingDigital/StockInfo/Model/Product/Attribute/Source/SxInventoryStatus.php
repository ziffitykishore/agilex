<?php

namespace SomethingDigital\StockInfo\Model\Product\Attribute\Source;

use Magento\Eav\Model\Entity\Attribute\Source\AbstractSource;
use Magento\Eav\Model\Entity\Attribute\Source\SourceInterface;
use Magento\Framework\Data\OptionSourceInterface;

class SxInventoryStatus extends AbstractSource implements SourceInterface, OptionSourceInterface
{
    /**#@+
     * Product sx_inventory_statur values
     */
    const STATUS_DNR = 1;
    const STATUS_ORDER_AS_NEEDED = 2;
    const STATUS_STOCK = 3;
    /**#@-*/

    /**
     * Retrieve option array
     *
     * @return string[]
     */
    public static function getOptionArray()
    {
        return [
            self::STATUS_DNR => __('DNR'),
            self::STATUS_ORDER_AS_NEEDED => __('Order as needed'),
            self::STATUS_STOCK => __('Stock')
        ];
    }

    /**
     * Retrieve option array with empty value
     *
     * @return string[]
     */
    public function getAllOptions()
    {
        $result = [];

        foreach (self::getOptionArray() as $index => $value) {
            $result[] = ['value' => $index, 'label' => $value];
        }

        return $result;
    }

    /**
     * Retrieve option text by option value
     *
     * @param string $optionId
     * @return string
     */
    public function getOptionText($optionId)
    {
        $options = self::getOptionArray();

        return isset($options[$optionId]) ? $options[$optionId] : null;
    }
}
