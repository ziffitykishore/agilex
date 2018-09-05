<?php

namespace Ziffity\Webforms\Controller\Adminhtml\Catalog;

use Ziffity\Webforms\Model\Catalog;

class MassDelete extends MassAction
{
    protected function massAction(Catalog $data)
    {
        $this->dataRepository->delete($data);
        return $this;
    }
}
