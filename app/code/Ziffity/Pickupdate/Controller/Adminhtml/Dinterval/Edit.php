<?php

namespace Ziffity\Pickupdate\Controller\Adminhtml\Dinterval;

class Edit extends \Ziffity\Pickupdate\Controller\Adminhtml\Dinterval
{

    public function execute()
    {
        $id = $this->getRequest()->getParam('id');
        $model = $this->_objectManager->create('Ziffity\Pickupdate\Model\Dinterval');

        if ($id) {
            $this->resourceModel->load($model, $id);
            if (!$model->getId()) {
                $this->messageManager->addErrorMessage(__('This item no longer exists.'));
                return $this->_redirect('ziffity_pickupdate/*');
            }
        }
        // set entered data if was error when we do save
        $data = $this->session->getPageData(true);
        if (!empty($data)) {
            $model->addData($data);
        }

        $this->_coreRegistry->register('current_ziffity_pickupdate_dinterval', $model);

        $resultPage = $this->resultPageFactory->create();
        $resultPage->setActiveMenu('Ziffity_Pickupdate::pickupdate_dinterval');

        $title = $model->getId() ? __('Edit Date Interval') : __('New Date Interval');
        $resultPage->getConfig()->getTitle()->prepend($title);
        $resultPage->addBreadcrumb($title, $title);

        return $resultPage;

    }
}
