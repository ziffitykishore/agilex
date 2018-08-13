<?php
/*
 * Ziffity_Banners
 */
namespace Ziffity\Banners\Controller\Adminhtml\Image;

use Ziffity\Banners\Controller\Adminhtml\Image;

class Edit extends Image
{
    /**
     * @return \Magento\Framework\View\Result\Page
     */
    public function execute()
    {
        $imageId = $this->getRequest()->getParam('image_id');
        $resultPage = $this->resultPageFactory->create();
        $resultPage->setActiveMenu('Ziffity_Banners::image')
            ->addBreadcrumb(__('Images'), __('Images'))
            ->addBreadcrumb(__('Manage Images'), __('Manage Images'));

        if ($imageId === null) {
            $resultPage->addBreadcrumb(__('New Image'), __('New Image'));
            $resultPage->getConfig()->getTitle()->prepend(__('New Image'));
        } else {
            $resultPage->addBreadcrumb(__('Edit Image'), __('Edit Image'));
            $resultPage->getConfig()->getTitle()->prepend(__('Edit Image'));
        }
        return $resultPage;
    }
}
