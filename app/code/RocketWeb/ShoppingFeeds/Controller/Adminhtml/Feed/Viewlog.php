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

class Viewlog extends \Magento\Backend\App\Action
{
    /**
     * @var \RocketWeb\ShoppingFeeds\Controller\Adminhtml\Feed\Builder
     */
    protected $feedBuilder;

    /**
     * @var \Magento\Framework\View\Result\PageFactory
     */
    protected $resultPageFactory;

    /**
     * @var \Magento\Backend\Model\View\Result\Forward
     */
    protected $resultForwardFactory;

    /**
     * @param \Magento\Backend\App\Action\Context $context
     * @param \RocketWeb\ShoppingFeeds\Controller\Adminhtml\Feed\Builder $feedBuilder
     * @param \Magento\Framework\View\Result\PageFactory $resultPageFactory
     * @param \Magento\Backend\Model\View\Result\ForwardFactory $resultForwardFactory
     */
    public function __construct(
        \Magento\Backend\App\Action\Context $context,
        \RocketWeb\ShoppingFeeds\Controller\Adminhtml\Feed\Builder $feedBuilder,
        \Magento\Framework\View\Result\PageFactory $resultPageFactory,
        \Magento\Backend\Model\View\Result\ForwardFactory $resultForwardFactory
    ) {
        $this->feedBuilder = $feedBuilder;
        $this->resultPageFactory = $resultPageFactory;
        $this->resultForwardFactory = $resultForwardFactory;

        parent::__construct($context);
    }

    /**
     * Init actions
     *
     * @return \Magento\Backend\Model\View\Result\Page
     */
    protected function _initAction()
    {
        return $this->resultPageFactory->create();
    }

    /**
     * Create new feed page
     *
     * @return \Magento\Framework\Controller\ResultInterface
     */
    public function execute()
    {
        $feed = $this->feedBuilder->build($this->getRequest()->getParams());

        if (!$feed->getId()) {
            $this->messageManager->addError(__('Feed wasn\'t found'));
            $resultForward = $this->resultForwardFactory->create();
            return $resultForward->forward('index');
        }

        /** @var \Magento\Backend\Model\View\Result\Page $resultPage */
        $resultPage = $this->resultPageFactory->create();
        $resultPage->getConfig()->getTitle()->prepend(sprintf(__('Logs for feed #%s [%s]'), $feed->getId(), $feed->getName()));

        return $resultPage;
    }
}
