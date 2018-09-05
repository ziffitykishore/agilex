<?php

namespace Ziffity\Webforms\Controller\Adminhtml\Catalog;

use Ziffity\Webforms\Model\Catalog;

class MassEnable extends MassAction
{
    protected function massAction(Catalog $data)
    {
        $data->setIsActive(true);
        $this->dataRepository->save($data);
        return $this;
    }
}
