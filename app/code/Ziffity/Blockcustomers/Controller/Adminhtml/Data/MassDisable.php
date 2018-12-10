<?php

namespace Ziffity\Blockcustomers\Controller\Adminhtml\Data;

use Ziffity\Blockcustomers\Model\Data;

class MassDisable extends MassAction
{
    /**
     * @param Data $data
     * @return $this
     */
    protected function massAction(Data $data)
    {
        $data->setIsActive(false);
        $this->dataRepository->save($data);
        return $this;
    }
}
