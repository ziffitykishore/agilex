<?php

namespace Ziffity\Zipcode\Controller\Adminhtml\Data;

use Ziffity\Zipcode\Model\Data;

class MassDelete extends MassAction
{

    protected function massAction(Data $data)
    {
        $this->dataRepository->delete($data);
        return $this;
    }
}
