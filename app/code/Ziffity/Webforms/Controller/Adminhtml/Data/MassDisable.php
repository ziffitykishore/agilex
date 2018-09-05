<?php

namespace Ziffity\Webforms\Controller\Adminhtml\Data;

use Ziffity\Webforms\Model\Data;

class MassDisable extends MassAction
{
    protected function massAction(Data $data)
    {
        $data->setIsActive(false);
        $this->dataRepository->save($data);
        return $this;
    }
}
