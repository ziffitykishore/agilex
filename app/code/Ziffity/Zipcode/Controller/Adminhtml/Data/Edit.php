<?php

namespace Ziffity\Zipcode\Controller\Adminhtml\Data;

use Ziffity\Zipcode\Controller\Adminhtml\Data;

class Edit extends Data
{

    public function execute()
    {
        $dataId = $this->getRequest()->getParam('data_id');
        $resultPage = $this->resultPageFactory->create();
        $resultPage->setActiveMenu('Ziffity_Zipcode::data')
            ->addBreadcrumb(__('Data'), __('Data'))
            ->addBreadcrumb(__('Manage Data'), __('Manage Data'));

        if ($dataId === null) {
            $resultPage->addBreadcrumb(__('New Data'), __('New Data'));
            $resultPage->getConfig()->getTitle()->prepend(__('New Data'));
        } else {
            $resultPage->addBreadcrumb(__('Edit Data'), __('Edit Data'));
            $resultPage->getConfig()->getTitle()->prepend(
                $this->dataRepository->getById($dataId)->getDataTitle()
            );
        }
        return $resultPage;
    }
}
