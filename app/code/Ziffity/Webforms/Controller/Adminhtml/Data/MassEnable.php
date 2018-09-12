<?php

namespace Ziffity\Webforms\Controller\Adminhtml\Data;

use Ziffity\Webforms\Model\Data;

class MassEnable extends MassAction
{
    protected function massAction(Data $data)
    {
        $data->setIsActive(true);
        $this->dataRepository->save($data);
        return $this;
    }
}
