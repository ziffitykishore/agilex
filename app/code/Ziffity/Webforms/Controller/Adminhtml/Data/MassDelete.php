<?php

namespace Ziffity\Webforms\Controller\Adminhtml\Data;

use Ziffity\Webforms\Model\Data;

class MassDelete extends MassAction
{
    protected function massAction(Data $data)
    {
        $this->dataRepository->delete($data);
        return $this;
    }
}
