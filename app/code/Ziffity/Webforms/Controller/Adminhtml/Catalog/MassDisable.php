<?php

namespace Ziffity\Webforms\Controller\Adminhtml\Catalog;

use Ziffity\Webforms\Model\Catalog;

class MassDisable extends MassAction
{
    protected function massAction(Catalog $data)
    {
        $data->setIsActive(false);
        $this->dataRepository->save($data);
        return $this;
    }
}
