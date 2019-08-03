<?php

namespace Wyomind\PointOfSale\Controller\Adminhtml\Attributes;

class Delete extends \Wyomind\PointOfSale\Controller\Adminhtml\Attributes
{
    public function execute()
    {
        $id = $this->getRequest()->getParam('attribute_id');
        
        if ($id) {
            try {
                $model = $this->_objectManager->create('Wyomind\PointOfSale\Model\Attributes');
                $model->load($id);
                $model->delete();
                $this->messageManager->addSuccess(__('The attribute has been deleted.'));
                $this->_redirect('pointofsale/attributes/index');
                return;
            } catch (\Exception $e) {
                $this->messageManager->addError($e->getMessage());
                $this->_redirect('pointofsale/attributes/edit', ['attribute_id' => $id]);
                return;
            }
        }
        
        $this->messageManager->addError(__("We can't find an attribute to delete."));
        $this->_redirect('pointofsale/attributes/index');
    }
}