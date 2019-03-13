<?php
/**
 * RocketWeb
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 *
 * @category  RocketWeb
 * @package   RocketWeb_ShoppingFeeds
 * @copyright Copyright (c) 2016 RocketWeb (http://rocketweb.com)
 * @license   http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 * @author    Rocket Web Inc.
 */

namespace RocketWeb\ShoppingFeeds\Controller\Adminhtml\Feed;

class Save extends \Magento\Backend\App\Action
{
    /**
     * @var \RocketWeb\ShoppingFeeds\Model\Feed\Converter
     */
    protected $feedConverter;

    /**
     * @param \RocketWeb\ShoppingFeeds\Model\Feed\Converter $feedConverter
     * @param \Magento\Backend\App\Action\Context $context
     */
    public function __construct(
        \RocketWeb\ShoppingFeeds\Model\Feed\Converter $feedConverter,
        \Magento\Backend\App\Action\Context $context
    ) {
        $this->feedConverter = $feedConverter;
        parent::__construct($context);
    }

    /**
     * {@inheritdoc}
     */
    protected function _isAllowed()
    {
        return $this->_authorization->isAllowed('RocketWeb_ShoppingFeeds::save');
    }

    /**
     * Save action
     *
     * @return \Magento\Framework\Controller\ResultInterface
     */
    public function execute()
    {
        $formData = $this->getRequest()->getParams();
        /** @var \Magento\Backend\Model\View\Result\Redirect $resultRedirect */
        $resultRedirect = $this->resultRedirectFactory->create();
        if ($formData) {
            $feed = $this->feedConverter->populateFeedData($formData);

            $this->_eventManager->dispatch(
                'feed_prepare_save',
                ['feed' => $feed, 'request' => $this->getRequest()]
            );

            try {
                $feed->save();
                $this->messageManager->addSuccess(__('You saved this feed.'));
                $this->_getSession()->setFeedData(false);
                if ($this->getRequest()->getParam('back')) {
                    return $resultRedirect->setPath('*/*/edit', ['id' => $feed->getId(), '_current' => true]);
                }
                return $resultRedirect->setPath('*/*/');
            } catch (\Magento\Framework\Exception\LocalizedException $e) {
                $this->messageManager->addError($e->getMessage());
            } catch (\RuntimeException $e) {
                $this->messageManager->addError($e->getMessage());
            } catch (\Exception $e) {
                $this->messageManager->addException($e, __('Something went wrong while saving the feed.'));
            }

            $formData = $this->feedConverter->createArrayFromObject($feed);
            $this->_getSession()->setFeedData($formData);
            return $resultRedirect->setPath('*/*/edit', ['id' => $this->getRequest()->getParam('id')]);
        }
        return $resultRedirect->setPath('*/*/');
    }
}
