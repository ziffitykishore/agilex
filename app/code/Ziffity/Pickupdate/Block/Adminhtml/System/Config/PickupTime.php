<?php

namespace Ziffity\Pickupdate\Block\Adminhtml\System\Config;

use Magento\Config\Block\System\Config\Form\Field;
use Magento\Framework\Data\Form\Element\AbstractElement;

class PickupTime extends Field
{
    /**
     * @param AbstractElement $element
     * @return string
     */
    public function render(AbstractElement $element)
    {
        $url = $this->getUrl('ziffity_pickupdate/tinterval');
        $element->setComment(
            __(
                "In order to make Pickup Time option work, please specify Time Intervals in "
                . "<a target='_blank' href='%1'>Sales -> Time Intervals</a>" . " first.",
                $url
            )
        );

        return parent::render($element);
    }
}
