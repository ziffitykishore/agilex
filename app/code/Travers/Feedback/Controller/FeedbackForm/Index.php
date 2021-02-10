<?php

namespace Travers\Feedback\Controller\FeedbackForm;

use Magento\Framework\Controller\ResultFactory;

class Index extends \Magento\Framework\App\Action\Action
{   

    /**
     * Booking action
     *
     * @return void
     */
    public function execute()
    {
        
        $this->_view->loadLayout();
        $block = $this->_view->getLayout()->getBlock('feedbackform');
        $this->_view->renderLayout();

        $post = (array) $this->getRequest()->getPost();
        
        if (!empty($post)) {
            // Create issue in JIRA
            $out = $block->createIssue($post);
       
            // Display the succes form validation message
            if($out['code'] == 201)
                $this->messageManager->addSuccessMessage('Feedback Submitted!');

            // Redirect to your form page (or anywhere you want...)
            $resultRedirect = $this->resultFactory->create(ResultFactory::TYPE_REDIRECT);
            $resultRedirect->setUrl($this->_redirect->getRefererUrl());
            return $resultRedirect;
        }
       
    }
}