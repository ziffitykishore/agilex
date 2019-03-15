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
use Magento\Backend\App\Action\Context;
use Magento\Ui\Component\MassAction\Filter;
use RocketWeb\ShoppingFeeds\Model\ResourceModel\Feed\CollectionFactory;

class MassClone extends \Magento\Backend\App\Action
{
    /**
     * Massactions filter
     *
     * @var Filter
     */
    protected $filter;

    /**
     * @var CollectionFactory
     */
    protected $collectionFactory;
    
    /**
     * @var Copier
     */
    protected $copier;

    /**
     * @param Context $context
     * @param Filter $filter
     * @param CollectionFactory $collectionFactory
     * @param \RocketWeb\ShoppingFeeds\Model\Feed\Copier $copier
     */
    public function __construct(
        Context $context,
        Filter $filter,
        CollectionFactory $collectionFactory,
        \RocketWeb\ShoppingFeeds\Model\Feed\Copier $copier
    ) {
        $this->filter = $filter;
        $this->collectionFactory = $collectionFactory;
        $this->copier = $copier;
        parent::__construct($context);
    }

    /**
     * Enable selected feeds
     *
     * @return \Magento\Framework\Controller\Result\Redirect
     */
    public function execute()
    {
        $collection = $this->filter->getCollection($this->collectionFactory->create());
        foreach ($collection->getItems() as $feed) {
            $this->copier->copy($feed);
        }
        $this->messageManager->addSuccess(__('A total of %1 record(s) have been cloned.', $collection->getSize()));

        return $this->resultFactory->create(ResultFactory::TYPE_REDIRECT)->setPath('shoppingfeeds/feed/index');
    }
}
