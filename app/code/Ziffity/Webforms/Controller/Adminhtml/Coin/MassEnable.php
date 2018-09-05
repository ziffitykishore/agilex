<?php

namespace Ziffity\Webforms\Controller\Adminhtml\Coin;

use Ziffity\Webforms\Model\Coin;

class MassEnable extends MassAction
{
    protected function massAction(Coin $data)
    {
        $data->setIsActive(true);
        $this->dataRepository->save($data);
        return $this;
    }
}
