<?php

namespace Ziffity\Webforms\Controller\Adminhtml\Data;

use Ziffity\Webforms\Controller\Adminhtml\Data;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Exception\NoSuchEntityException;

class Delete extends Data
{
    public function execute()
    {
        $resultRedirect = $this->resultRedirectFactory->create();
        $dataId = $this->getRequest()->getParam('customer_id');
        if ($dataId) {
            try {
                $this->dataRepository->deleteById($dataId);
                $this->messageManager->addSuccessMessage(__('The data has been deleted.'));
                $resultRedirect->setPath('comments/data/index');
                return $resultRedirect;
            } catch (NoSuchEntityException $e) {
                $this->messageManager->addErrorMessage(__('The data no longer exists.'));
                return $resultRedirect->setPath('comments/data/index');
            } catch (LocalizedException $e) {
                $this->messageManager->addErrorMessage($e->getMessage());
                return $resultRedirect->setPath('comments/data/index', ['customer_id' => $dataId]);
            } catch (\Exception $e) {
                $this->messageManager->addErrorMessage(__('There was a problem deleting the data'));
                return $resultRedirect->setPath('comments/data/edit', ['customer_id' => $dataId]);
            }
        }
        $this->messageManager->addErrorMessage(__('We can\'t find the data to delete.'));
        $resultRedirect->setPath('comments/data/index');
        return $resultRedirect;
    }
}
