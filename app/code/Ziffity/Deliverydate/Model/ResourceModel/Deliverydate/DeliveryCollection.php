<?php

namespace Ziffity\Deliverydate\Model\ResourceModel\Deliverydate;

use Amasty\Deliverydate\Model\ResourceModel\Deliverydate\Collection;

class DeliveryCollection extends Collection
{


    public function getDeliveryByDate($date)
    {
        $this->getSelect()
            ->where('`date` <> \'0000-00-00\'')
            ->where('`date` <> \'1970-01-01\'')
            ->where('`date` = ?', $date)
            ->where('`active` = \'1\'');

        return $this;

    }

}