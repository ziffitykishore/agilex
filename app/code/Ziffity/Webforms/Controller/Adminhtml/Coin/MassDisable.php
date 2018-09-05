<?php

namespace Ziffity\Webforms\Controller\Adminhtml\Coin;

use Ziffity\Webforms\Model\Coin;

class MassDisable extends MassAction
{
    protected function massAction(Coin $data)
    {
        $data->setIsActive(false);
        $this->dataRepository->save($data);
        return $this;
    }
}
