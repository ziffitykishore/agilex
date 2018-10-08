<?php

namespace Ziffity\Blockcustomers\Controller\Adminhtml\Data;

use Ziffity\Blockcustomers\Model\Data;

class MassEnable extends MassAction
{
    /**
     * @param Data $data
     * @return $this
     */
    protected function massAction(Data $data)
    {
        $data->setIsActive(true);
        $this->dataRepository->save($data);
        return $this;
    }
}
