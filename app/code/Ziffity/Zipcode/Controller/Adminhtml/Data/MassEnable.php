<?php

namespace Ziffity\Zipcode\Controller\Adminhtml\Data;

use Ziffity\Zipcode\Model\Data;

class MassEnable extends MassAction
{

    protected function massAction(Data $data)
    {
        $data->setIsActive(true);
        $this->dataRepository->save($data);
        return $this;
    }
}
