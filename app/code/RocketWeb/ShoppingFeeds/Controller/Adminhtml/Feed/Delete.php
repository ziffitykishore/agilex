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

use Magento\Framework\Controller\ResultFactory;

class Delete extends \Magento\Backend\App\Action
{
    /**
     * @var \RocketWeb\ShoppingFeeds\Controller\Adminhtml\Feed\Builder
     */
    protected $feedBuilder;

    /**
     * @param \Magento\Backend\App\Action\Context $context
     * @param \RocketWeb\ShoppingFeeds\Controller\Adminhtml\Feed\Builder $feedBuilder
     */
    public function __construct(
        \Magento\Backend\App\Action\Context $context,
        \RocketWeb\ShoppingFeeds\Controller\Adminhtml\Feed\Builder $feedBuilder
    ) {
        $this->feedBuilder = $feedBuilder;

        parent::__construct($context);
    }

    /**
     * Delete individual feed 
     *
     * @return \Magento\Framework\Controller\Result\Redirect
     */
    public function execute()
    {
        $feed = $this->feedBuilder->build($this->getRequest()->getParams());

        if (!$feed->getId()) {
            $this->messageManager->addError(__('Feed wasn\'t found'));
        } else {
            $feed->delete();
            $this->messageManager->addSuccess(__('Feed has been deleted'));
        }

        return $this->resultFactory->create(ResultFactory::TYPE_REDIRECT)->setPath('shoppingfeeds/feed/index');
    }
}