<?php

namespace Ziffity\PickupdateOR\Model\ResourceModel\Pickupdate;

use Ziffity\Pickupdate\Model\ResourceModel\Pickupdate\Collection;

class PickupCollection extends Collection
{


    public function getPickupByDate($date)
    {
        $this->getSelect()
            ->where('`date` <> \'0000-00-00\'')
            ->where('`date` <> \'1970-01-01\'')
            ->where('`date` = ?', $date)
            ->where('`active` = \'1\'');

        return $this;

    }

}