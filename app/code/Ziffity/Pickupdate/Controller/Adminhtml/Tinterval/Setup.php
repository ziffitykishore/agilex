<?php

namespace Ziffity\Pickupdate\Controller\Adminhtml\Tinterval;

class Setup extends \Ziffity\Pickupdate\Controller\Adminhtml\Tinterval
{

    public function execute()
    {
        $resultPage = $this->resultPageFactory->create();
        $resultPage->setActiveMenu('Ziffity_Pickupdate::pickupdate_tinterval');

        $title =  __('Generate Time Intervals');
        $resultPage->getConfig()->getTitle()->prepend($title);
        $resultPage->addBreadcrumb($title, $title);

        return $resultPage;

    }
}
