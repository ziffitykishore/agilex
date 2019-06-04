<?php


namespace Ziffity\Pickupdate\Controller\Adminhtml\Tinterval;

use Magento\Framework\Exception\LocalizedException;

class Save extends \Ziffity\Pickupdate\Controller\Adminhtml\Tinterval
{
    public function execute()
    {
        if ($this->getRequest()->getPostValue()) {
            $data = $this->getRequest()->getPostValue();

            try {
                $model = $this->_objectManager->create('Ziffity\Pickupdate\Model\Tinterval');

                $id = $this->getRequest()->getParam('tinterval_id');

                if ($id) {
                    $this->resourceModel->load($model, $id);
                    if ($id != $model->getId()) {
                        throw new LocalizedException(__('The wrong date interval is specified.'));
                    }
                }

                $model->setData($data);
                $this->prepareForSave($model);

                $this->resourceModel->save($model);

                $this->messageManager->addSuccessMessage(__('Record has been successfully saved'));

                if ($this->getRequest()->getParam('back')) {
                    $this->_redirect('ziffity_pickupdate/*/edit', ['id' => $model->getId()]);
                    return;
                }
                $this->_redirect('ziffity_pickupdate/*/');
                return;

            } catch (LocalizedException $e) {
                $this->messageManager->addErrorMessage($e->getMessage());
                $id = (int)$this->getRequest()->getParam('tinterval_id');
                if (!empty($id)) {
                    $this->_redirect('ziffity_pickupdate/*/edit', ['id' => $id]);
                } else {
                    $this->_redirect('ziffity_pickupdate/*/index');
                }
                return;
            } catch (\Exception $e) {
                $this->messageManager->addErrorMessage(
                    __('Something went wrong while saving the date interval data. Please review the error log.')
                );
                $this->logInterface->critical($e);
                $this->session->setPageData($data);
                $this->_redirect('ziffity_pickupdate/*/edit', ['id' => $this->getRequest()->getParam('tinterval_id')]);
                return;
            }
        }
    }

    protected function prepareForSave($model)
    {
        $stores = $model->getData('store_ids');
        if (is_array($stores)) {
            // need commas to simplify sql query
            $model->setData('store_ids', implode(',', $stores));
        } else { // need for null value
            $model->setData('store_ids', '');
        }
        return true;
    }
}