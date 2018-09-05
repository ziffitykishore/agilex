<?php

namespace Ziffity\Webforms\Controller\Adminhtml\Coin;

use Ziffity\Webforms\Model\Coin;

class MassDelete extends MassAction
{
    protected function massAction(Coin $data)
    {
        $this->dataRepository->delete($data);
        return $this;
    }
}
