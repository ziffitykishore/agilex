<?php

namespace Ziffity\Webforms\Controller\Adminhtml\Catalog;

use Ziffity\Webforms\Controller\Adminhtml\Catalog;

class Edit extends Catalog
{
    public function execute()
    {
        $dataId = $this->getRequest()->getParam('customer_id');
        $resultPage = $this->resultPageFactory->create();
        $resultPage->setActiveMenu('Ziffity_Webforms::catalog')
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
