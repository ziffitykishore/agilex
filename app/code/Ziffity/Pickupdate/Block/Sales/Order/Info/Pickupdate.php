<?php

namespace Ziffity\Pickupdate\Block\Sales\Order\Info;

class Pickupdate extends \Ziffity\Pickupdate\Block\Sales\Order\Email\Pickupdate
{
    protected function _construct()
    {
        parent::_construct();

        $this->setTemplate('Ziffity_Pickupdate::info.phtml');
    }

    public function getFields()
    {
        return $this->pickupHelper->whatShow('order_info');
    }

    /**
     * @return string
     */
    public function getEditUrl()
    {
        if ($this->customerSession->isLoggedIn()) {
            return $this->getUrl('ziffity_pickupdate/pickupdate/edit', ['order_id' => $this->getOrderId()]);
        }
        return $this->getUrl('ziffity_pickupdate/guest/edit', ['order_id' => $this->getOrderId()]);
    }

    /**
     * @param string $field code
     *
     * @return bool
     */
    public function isFieldEditable($field)
    {
        if ($field == 'date') {
            return $this->getPickupDate()->isCanEditOnFront();
        }

        return false;
    }
}
