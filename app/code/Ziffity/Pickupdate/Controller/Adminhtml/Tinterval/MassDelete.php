<?php

namespace Ziffity\Pickupdate\Controller\Adminhtml\Tinterval;

use Ziffity\Pickupdate\Controller\Adminhtml\Tinterval\Index;
use Magento\Framework\Controller\ResultFactory;

class MassDelete extends Index
{
    /**
     * @return mixed
     */
    public function execute()
    {
        $collection = $this->filter->getCollection($this->tintervalCollection);
        $collectionSize = $collection->getSize();

        foreach ($collection as $tinterval) {
            $tinterval->delete();
        }
        $this->messageManager->addSuccessMessage(__('A total of %1 record(s) have been deleted.', $collectionSize));
        $resultRedirect = $this->resultFactory->create(ResultFactory::TYPE_REDIRECT);
        return $resultRedirect->setPath('*/*/');
    }
}