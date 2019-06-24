<?php

namespace Ziffity\Pickupdate\Controller\Adminhtml\Holidays;

use Ziffity\Pickupdate\Controller\Adminhtml\Holidays\Index;

class Delete extends Index
{
    public function execute()
    {
        /** @var \Magento\Backend\Model\View\Result\Redirect $resultRedirect */
        $resultRedirect = $this->resultRedirectFactory->create();

        try {
            $id = $this->getRequest()->getParam('id');
            $ddModel = $this->model->create();
            $this->resourceModel->load($ddModel, $id);
            $this->resourceModel->delete($ddModel);
            $this->messageManager->addSuccessMessage(__('Date has been deleted.'));
        } catch (\Exception $e) {
            // display error message
            $this->messageManager->addErrorMessage($e->getMessage());
        }
        return $resultRedirect->setPath('*/*/');
    }
}